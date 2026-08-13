<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\FirebaseService;
use Closure;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) {
                    $firebase = app(FirebaseService::class);
                    $exists = $firebase->runSimpleQuery('users', 'email', '=', $value);
                    if (count($exists) > 0 && $exists[0]['id'] !== $this->user()->id) {
                        $fail('The '.$attribute.' has already been taken.');
                    }
                },
            ],
        ];
    }
}
