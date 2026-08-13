<?php

namespace App\Auth;

use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FirestoreUserProvider implements UserProvider
{
    protected $firebase;
    protected $collection = 'users';

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $userData = $this->firebase->getDocument($this->collection, $identifier);

        if (!$userData) {
            return null;
        }

        return $this->getGenericUser($userData);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $userData = $this->firebase->getDocument($this->collection, $identifier);

        if (!$userData) {
            return null;
        }

        $user = $this->getGenericUser($userData);

        if ($user->getRememberToken() !== $token) {
            return null;
        }

        return $user;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        
        $userData = $user->getAttributes();
        
        try {
            $this->firebase->saveDocument($this->collection, $userData, $user->getAuthIdentifier());
        } catch (\Exception $e) {
            // Log error or ignore if updating token fails
        }
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || (count($credentials) === 1 && isset($credentials['password']))) {
            return null;
        }

        // Usually credentials contains 'email'
        if (isset($credentials['email'])) {
            $results = $this->firebase->runSimpleQuery($this->collection, 'email', '=', $credentials['email']);
            
            if (!empty($results)) {
                return $this->getGenericUser($results[0]);
            }
        }

        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'] ?? '';
        return Hash::check($plain, $user->getAuthPassword());
    }

    /**
     * Rehydrate user into object.
     *
     * @param array $userData
     * @return User
     */
    protected function getGenericUser($userData)
    {
        return new User($userData, $userData['id'] ?? null);
    }
}
