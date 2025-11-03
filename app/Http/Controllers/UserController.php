<?php

namespace App\Http\Controllers;

use App\Http\Requests\users\CreateRequest;
use App\Mail\UserCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('is_admin', 'desc')->get();

        return Inertia::render('admin/Users', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $user->status = $request->has('status') ? $request->status : $user->status;
            $user->name = $request->has('name') ? $request->name : $user->name;

            $user->save();
        }

        return redirect()->back();
    }

    public function store(CreateRequest $request)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $tmp_password = substr(str_shuffle($chars), 0, 8);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($tmp_password),
            'tmp_password' => $tmp_password,
            'status' => 'approved',
        ]);

        Mail::to($request->email)->send(new UserCreated($user));

        return redirect()->back();
    }
}
