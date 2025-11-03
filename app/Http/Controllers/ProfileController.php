<?php

namespace App\Http\Controllers;

use App\Http\Requests\profile\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function showPasswordReset(Request $request)
    {
        return Inertia::render('profile/ResetPassword');
    }

    public function showGeneralInfo(Request $request)
    {
        $user = Auth::user();

        return Inertia::render('profile/GeneralInfo', compact('user'));
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $user = Auth::user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => bcrypt($request->password),
            ]);

            return redirect()->back();
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Old password is incorrect',
            ]);
        }
    }
}
