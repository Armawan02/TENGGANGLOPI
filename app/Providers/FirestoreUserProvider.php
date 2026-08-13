<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Str;

class FirestoreUserProvider implements UserProvider
{
    protected $collection = 'users';
    protected $firebaseService;

    public function __construct()
    {
        $this->firebaseService = app(FirebaseService::class);
    }

    public function retrieveById($identifier)
    {
        $doc = $this->firebaseService->getDocument($this->collection, $identifier);
        if ($doc) {
            return new User($doc, $identifier);
        }
        return null;
    }

    public function retrieveByToken($identifier, $token)
    {
        $doc = $this->firebaseService->getDocument($this->collection, $identifier);
        
        if ($doc && isset($doc['remember_token']) && $doc['remember_token'] === $token) {
            return new User($doc, $identifier);
        }
        
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Update partially - fetch first then merge
        $doc = $this->firebaseService->getDocument($this->collection, $user->getAuthIdentifier());
        if ($doc) {
            $doc['remember_token'] = $token;
            $this->firebaseService->saveDocument($this->collection, $doc, $user->getAuthIdentifier());
        }
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return null;
        }
        
        // Find the first credential to query (e.g. email)
        $searchKey = null;
        $searchValue = null;
        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, 'password')) {
                $searchKey = $key;
                $searchValue = $value;
                break;
            }
        }

        if (!$searchKey) return null;

        $results = $this->firebaseService->runSimpleQuery($this->collection, $searchKey, '=', $searchValue);
        
        if (count($results) > 0) {
            return new User($results[0], $results[0]['id']);
        }
        
        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return Hash::check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        return false;
    }
}
