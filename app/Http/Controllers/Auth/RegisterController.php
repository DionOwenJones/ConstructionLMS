<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'role' => ['required', 'string', 'in:user,business'],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.mixed' => 'The password must contain at least one uppercase and one lowercase letter.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.symbols' => 'The password must contain at least one symbol.',
            'password.uncompromised' => 'This password has been exposed in a data leak. Please choose a different password.',
            'role.in' => 'Please select a valid account type.',
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role']
        ]);

        if ($data['role'] === 'business') {
            // Create business record with user_id and complete profile
            $user->business()->create([
                'name' => $data['name'],
                'company_name' => $data['name'],
                'email' => $data['email'],
                'user_id' => $user->id,
                'is_setup_complete' => true
            ]);
        }

        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        
        // Only fire the Registered event, which triggers verification email
        event(new Registered($user));

        Auth::login($user);

        if ($request->role === 'business') {
            return redirect()->route('business.dashboard')->with('verification-notice', 'Please check your email to verify your account.');
        }

        return redirect()->route('dashboard')->with('verification-notice', 'Please check your email to verify your account.');
    }
}
