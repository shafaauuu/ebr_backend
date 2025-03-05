<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function preRegister(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email|regex:/@oneject\.(co\.id|com)$/i',
            'nik' => 'required|unique:users,nik',
            'divisi' => 'required',
            'department' => 'required',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'nik' => $request->nik,
            'divisi' => $request->divisi,
            'department' => $request->department,
            'role_id' => $request->role_id,
            'preregistered_at' => now(),
        ]);

        // Store password in user_passwords table
        UserPassword::create([
            'user_id' => $user->id,
            'password' => md5($request->password),
            'changed_at' => now(),
        ]);

        return response()->json(['message' => 'User pre-registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)
            ->where('password',md5($request->password))
            ->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'token' => $user->createToken('authToken')->plainTextToken,
                'user' => $user
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();
        $latestPassword = $user->passwords()->latest('changed_at')->first();

        if (!$latestPassword || md5($request->old_password) !== $latestPassword->password) {
            return response()->json(['error' => 'Old password is incorrect'], 400);
        }

        // Store new password in user_passwords table instead of updating existing one
        UserPassword::create([
            'user_id' => $user->id,
            'password' => md5($request->new_password),
            'changed_at' => now(),
        ]);

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function getUser(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Load role relationship
        $user->load('role');

        return response()->json([
            'nik' => $user->nik,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'position' => $user->position,
            'div' => $user->div,
            'dept' => $user->dept,
            'role' => optional($user->role)->role ?? 'No Role', // Get role from role_auth via user_role
        ]);
    }


    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke the user's current token
        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }


}
