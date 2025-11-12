<?php

namespace Src\Admin\Profile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Src\Admin\Profile\Requests\LoginRequest;
use Src\Admin\Profile\Requests\RegisterRequest;

class AuthController extends Controller
{
    /**
     * Admin Profile Login - Cookie Based
     */
    public function login(LoginRequest $request)
    {
        $admin = Admin::where('email', $request->email)
            ->with('organization')
            ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if admin is active
        if (!$admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        // Check if organization is active
        if (!$admin->organization->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your organization has been deactivated.'],
            ]);
        }

        // Login using session/cookie with admin guard
        Auth::guard('admin')->login($admin, $request->boolean('remember'));

        // Regenerate session to prevent fixation attacks
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Admin login successful',
            'user' => $admin->load('organization'),
        ]);
    }

    /**
     * Admin Profile Registration - Cookie Based
     * Creates both organization and admin account
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Generate organization name from admin's name if not provided
            $organizationName = $request->organization_name 
                ?? $request->first_name . ' ' . $request->last_name . "'s Organization";

            // Create organization first
            $organization = Organization::create([
                'name' => $organizationName,
                'email' => $request->email,
                'phone' => $request->phone ?? null,
                'is_active' => true,
            ]);

            // Create admin with organization_id
            $admin = Admin::create([
                'organization_id' => $organization->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? null,
                'is_active' => true,
            ]);

            // Login using session/cookie with admin guard
            Auth::guard('admin')->login($admin);

            // Regenerate session
            $request->session()->regenerate();

            DB::commit();

            return response()->json([
                'message' => 'Admin registration successful',
                'user' => $admin->load('organization'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin Profile Logout - Cookie Based
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Admin logged out successfully',
        ]);
    }

    /**
     * Get Authenticated Admin User Profile
     */
    public function getProfile(Request $request)
    {
        return response()->json([
            'data' => Auth::guard('admin')->user()->load('organization'),
        ]);
    }

    /**
     * Update Admin Profile
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:admins,email,' . $admin->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|string',
        ]);

        if ($request->has('first_name')) {
            $admin->first_name = $request->first_name;
        }

        if ($request->has('last_name')) {
            $admin->last_name = $request->last_name;
        }

        if ($request->has('email')) {
            $admin->email = $request->email;
        }

        if ($request->has('password')) {
            $admin->password = Hash::make($request->password);
        }

        if ($request->has('phone')) {
            $admin->phone = $request->phone;
        }

        if ($request->has('avatar')) {
            $admin->avatar = $request->avatar;
        }

        $admin->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $admin->load('organization'),
        ]);
    }

    /**
     * Delete Admin Profile (Soft delete)
     */
    public function deleteProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Logout first
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Soft delete admin account
        $admin->delete();

        return response()->json([
            'message' => 'Profile deleted successfully',
        ]);
    }

    /**
     * Get CSRF Token
     */
    public function getCsrfToken(Request $request)
    {
        return response()->json([
            'csrf_token' => csrf_token(),
        ]);
    }
}
