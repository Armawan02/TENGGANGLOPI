<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Str;

class FirebaseService
{
    protected $projectId;
    protected $credentialsPath;
    protected $credentialsData;
    protected $baseUrl;

    public function __construct()
    {
        // 1. Cek dari Base64 Env Var (Prioritas untuk Vercel/Produksi)
        $envBase64 = env('FIREBASE_CREDENTIALS_BASE64');
        if ($envBase64) {
            $credentials = json_decode(base64_decode($envBase64), true);
            $this->projectId = $credentials['project_id'] ?? null;
            $this->credentialsData = $credentials;
        } else {
            // 2. Fallback ke File JSON (Untuk Local Laragon)
            $this->credentialsPath = base_path(env('FIREBASE_CREDENTIALS', 'firebase_credentials.json'));
            
            if (file_exists($this->credentialsPath)) {
                $credentials = json_decode(file_get_contents($this->credentialsPath), true);
                $this->projectId = $credentials['project_id'] ?? null;
                $this->credentialsData = $this->credentialsPath; // bisa string path file
            }
        }

        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    /**
     * Dapatkan Bearer Token (Cache untuk performa)
     */
    protected function getAuthToken()
    {
        if (!$this->credentialsData) {
            throw new \Exception("Kredensial Firebase tidak ditemukan! Pastikan file JSON ada atau ENV FIREBASE_CREDENTIALS_BASE64 diset.");
        }

        return Cache::remember('firestore_auth_token', 3000, function () {
            $scopes = ['https://www.googleapis.com/auth/datastore'];
            $sa = new ServiceAccountCredentials($scopes, $this->credentialsData);
            
            // Custom Guzzle handler to bypass SSL for Laragon local development
            $guzzle = new \GuzzleHttp\Client(['verify' => false]);
            $httpHandler = function ($request) use ($guzzle) {
                return $guzzle->send($request);
            };
            
            $tokenInfo = $sa->fetchAuthToken($httpHandler);
            return $tokenInfo['access_token'];
        });
    }

    /**
     * Dapatkan Headers untuk request Http
     */
    protected function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Get all documents from a collection
     */
    public function getCollection($collectionName)
    {
        $response = Http::withoutVerifying()
            ->withHeaders($this->getHeaders())
            ->get("{$this->baseUrl}/{$collectionName}");
            
        if (!$response->successful()) {
            throw new \Exception('Firestore GET Error: ' . $response->body());
        }

        $data = $response->json();
        $result = [];
        
        if (isset($data['documents'])) {
            foreach ($data['documents'] as $doc) {
                $result[] = $this->parseFirestoreDocument($doc);
            }
        }
        
        return $result;
    }

    /**
     * Get a specific document
     */
    public function getDocument($collectionName, $documentId)
    {
        $response = Http::withoutVerifying()
            ->withHeaders($this->getHeaders())
            ->get("{$this->baseUrl}/{$collectionName}/{$documentId}");
            
        if ($response->status() === 404) {
            return null; // Document not found
        }
            
        if (!$response->successful()) {
            throw new \Exception('Firestore GET DOC Error: ' . $response->body());
        }

        return $this->parseFirestoreDocument($response->json());
    }

    /**
     * Create or update a document
     */
    public function saveDocument($collectionName, $data, $documentId = null)
    {
        $url = "{$this->baseUrl}/{$collectionName}";
        
        if ($documentId) {
            // Update document
            $url = "{$url}/{$documentId}";
            $method = 'patch';
        } else {
            // Create document with auto ID
            $documentId = (string) Str::uuid();
            $url = "{$url}?documentId={$documentId}";
            $method = 'post';
        }

        $payload = $this->serializeFields($data);

        $response = Http::withoutVerifying()
            ->withHeaders($this->getHeaders())
            ->$method($url, $payload);
            
        if (!$response->successful()) {
            throw new \Exception('Firestore SAVE Error: ' . $response->body());
        }

        return $documentId;
    }

    /**
     * Delete a document
     */
    public function deleteDocument($collectionName, $documentId)
    {
        $response = Http::withoutVerifying()
            ->withHeaders($this->getHeaders())
            ->delete("{$this->baseUrl}/{$collectionName}/{$documentId}");
            
        if (!$response->successful()) {
            throw new \Exception('Firestore DELETE Error: ' . $response->body());
        }

        return true;
    }

    /**
     * Query Documents based on single where clause (for Login/Auth)
     */
    public function runSimpleQuery($collectionName, $field, $operator, $value)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
        
        $opMap = [
            '=' => 'EQUAL',
            '<' => 'LESS_THAN',
            '<=' => 'LESS_THAN_OR_EQUAL',
            '>' => 'GREATER_THAN',
            '>=' => 'GREATER_THAN_OR_EQUAL'
        ];

        $payload = [
            'structuredQuery' => [
                'from' => [['collectionId' => $collectionName]],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => $field],
                        'op' => $opMap[$operator] ?? 'EQUAL',
                        'value' => $this->serializeValue($value)
                    ]
                ],
                'limit' => 1
            ]
        ];

        $response = Http::withoutVerifying()->withHeaders($this->getHeaders())->post($url, $payload);
        
        if (!$response->successful()) {
            throw new \Exception('Firestore QUERY Error: ' . $response->body());
        }

        $data = $response->json();
        $result = [];
        
        foreach ($data as $item) {
            if (isset($item['document'])) {
                $result[] = $this->parseFirestoreDocument($item['document']);
            }
        }
        
        return $result;
    }

    // --- PARSER UTILITIES ---

    public function parseFirestoreDocument($document)
    {
        if (!isset($document['fields'])) {
            return [];
        }
        $result = $this->parseFields($document['fields']);
        
        if (isset($document['name'])) {
            $parts = explode('/', $document['name']);
            $result['id'] = end($parts);
        }
        
        return $result;
    }

    private function parseFields($fields)
    {
        $result = [];
        foreach ($fields as $key => $valueObj) {
            $result[$key] = $this->parseValue($valueObj);
        }
        return $result;
    }

    private function parseValue($valueObj)
    {
        if (isset($valueObj['stringValue'])) return $valueObj['stringValue'];
        if (isset($valueObj['integerValue'])) return (int) $valueObj['integerValue'];
        if (isset($valueObj['doubleValue'])) return (float) $valueObj['doubleValue'];
        if (isset($valueObj['booleanValue'])) return (bool) $valueObj['booleanValue'];
        if (isset($valueObj['nullValue'])) return null;
        if (isset($valueObj['mapValue']['fields'])) return $this->parseFields($valueObj['mapValue']['fields']);
        if (isset($valueObj['arrayValue']['values'])) {
            return array_map([$this, 'parseValue'], $valueObj['arrayValue']['values']);
        }
        if (isset($valueObj['timestampValue'])) return $valueObj['timestampValue'];
        return null;
    }

    // --- SERIALIZER UTILITIES ---

    public function serializeFields($data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if ($key === 'id') continue; // ID tidak disimpan di field
            $fields[$key] = $this->serializeValue($value);
        }
        return ['fields' => $fields];
    }

    private function serializeValue($value)
    {
        if (is_string($value)) return ['stringValue' => $value];
        if (is_int($value)) return ['integerValue' => (string) $value];
        if (is_float($value)) return ['doubleValue' => $value];
        if (is_bool($value)) return ['booleanValue' => $value];
        if (is_null($value)) return ['nullValue' => null];
        if (is_array($value)) {
            if (array_keys($value) !== range(0, count($value) - 1)) {
                return ['mapValue' => $this->serializeFields($value)];
            } else {
                return ['arrayValue' => ['values' => array_map([$this, 'serializeValue'], $value)]];
            }
        }
        return ['stringValue' => (string) $value];
    }
}
