<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Services\FirebaseService;
use Closure;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                function (string $attribute, mixed $value, Closure $fail) {
                    $firebase = app(FirebaseService::class);
                    $exists = $firebase->runSimpleQuery('users', 'email', '=', $value);
                    if (count($exists) > 0) {
                        $fail('The '.$attribute.' has already been taken.');
                    }
                },
            ],
            'role' => ['required', 'string', 'in:superadmin,petugas'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($user->role === 'superadmin') {
            return redirect(route('superadmin.dashboard', absolute: false));
        }

        return redirect(route('petugas.dashboard', absolute: false));
    }
}
