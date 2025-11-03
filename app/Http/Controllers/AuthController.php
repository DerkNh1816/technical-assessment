<?php

namespace App\Http\Controllers;

use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function loginView(Request $request)
    {
        return Inertia::render('auth/SignIn');
    }

    public function registerView(Request $request)
    {
        return Inertia::render('auth/SignUp');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->status === 'approved' && Auth::attempt($credentials)) {
            if ($user->is_admin) {
                return redirect('/admin/users');
            } else {
                return redirect('/');
            }
        } else {
            if ($user && $user->status === 'pending') {
                return redirect()->back()->withErrors([
                    'activity' => 'This account is not approved.',
                ]);
            }

            return redirect()->back()->withErrors([
                'password' => 'Credentials are incorrect.',
            ]);
        }
    }

    public function logout()
    {
        Auth::logout();
    }

    public function register(RegisterRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_admin' => false,
            'status' => 'pending',
        ]);

        // Redirect to the next step
        return Redirect::to('/auth/login');
    }
}
