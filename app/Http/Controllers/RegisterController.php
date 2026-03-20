<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'unique:users', 'min:3', 'max:30', 'alpha_dash'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $nickname = $validatedData['nickname'] ?: $validatedData['full_name'];

        User::create([
            'full_name' => $validatedData['full_name'],
            'username' => $validatedData['username'],
            'nickname' => $nickname,
            'password' => $validatedData['password'], // Hash is handled by the model's 'hashed' cast
            'role' => 'student',
        ]);

        return redirect('/login')->with('success', 'Registration successful! Please login.');
    }
}
