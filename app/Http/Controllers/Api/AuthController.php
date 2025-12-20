<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'user_type' => 'required|in:seeker,recruiter',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token
        ]);
    }

    // LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // FORGOT PASSWORD - verify email exists
    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        return response()->json(['message' => 'Email found, proceed to reset']);
    }

    // RESET PASSWORD - change password by email (no token)
    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated']);
    }

    // UPDATE BIO (authenticated)
    public function updateBio(Request $request)
    {
        $data = $request->validate([
            'bio' => 'nullable|string'
        ]);

        $user = $request->user();
        $user->bio = $data['bio'] ?? null;
        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }

    // UPDATE SKILLS (authenticated)
    public function updateSkills(Request $request)
    {
        $data = $request->validate([
            'skills' => 'nullable|string'
        ]);

        $user = $request->user();
        $user->skills = $data['skills'] ?? null;
        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }

    // UPDATE LOCATION (authenticated)
    public function updateLocation(Request $request)
    {
        $data = $request->validate([
            'location' => 'nullable|string|max:255'
        ]);

        $user = $request->user();
        $user->location = $data['location'] ?? null;
        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }

    // UPDATE PROFILE PIC (authenticated)
    public function updateProfilePic(Request $request)
    {
        $data = $request->validate([
            'profile_pic' => [
                'required',
                'string',
                // Accept avatar names like: Avatars Set Flat Style-1 ... -50
                'regex:/^Avatars Set Flat Style-(?:[1-9]|[1-4]\d|50)$/'
            ]
        ]);

        $user = $request->user();
        $user->profile_pic = $data['profile_pic'];
        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }


}
