<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use App\Services\FirebaseService;
use Illuminate\Support\Str;

class User implements Authenticatable
{
    protected $attributes = [];
    protected $id;

    public function __construct(array $attributes = [], $id = null)
    {
        $this->attributes = $attributes;
        $this->id = $id;
    }

    public function __get($key)
    {
        if ($key === 'id') return $this->id;
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function save()
    {
        $firebase = app(FirebaseService::class);
        
        if (!$this->id) {
            $this->id = (string) Str::uuid();
        }
        
        $firebase->saveDocument('users', $this->attributes, $this->id);
        return true;
    }

    public static function create(array $attributes)
    {
        $user = new self($attributes);
        $user->save();
        return $user;
    }

    public static function where($field, $operator, $value = null)
    {
        return new class($field, $operator, $value) {
            public function first() { return null; }
            public function exists() { return false; }
        };
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        return $this->attributes['password'] ?? '';
    }
    
    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getRememberToken()
    {
        return $this->attributes['remember_token'] ?? null;
    }

    public function setRememberToken($value)
    {
        $this->attributes['remember_token'] = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
