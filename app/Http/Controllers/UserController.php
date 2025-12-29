<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display users management page
     */
    public function index()
    {
        $users = User::with('verifiedBy:id,name')
            ->select([
                'id', 
                'name', 
                'email', 
                'role', 
                'avatar',
                'email_verified_at',
                'is_verified_by_admin',
                'verified_by',
                'verified_at',
                'created_at',
                'updated_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $statistics = [
            'total' => User::count(),
            'verified' => User::where('is_verified_by_admin', true)->count(),
            'pending' => User::where('is_verified_by_admin', false)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'engineers' => User::where('role', 'engineer')->count(),
            'nms' => User::where('role', 'nms')->count(),
        ];

        return Inertia::render('users', [
            'users' => $users,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,engineer,nms',
            'is_verified_by_admin' => 'boolean',
        ]);

        $isVerified = $validated['is_verified_by_admin'] ?? false;
        /** @var int $currentUserId */
        $currentUserId = Auth::id();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_verified_by_admin' => $isVerified,
            'verified_by' => $isVerified ? $currentUserId : null,
            'verified_at' => $isVerified ? now() : null,
        ]);

        return back()->with('success', "User {$user->name} has been created successfully.");
    }

    /**
     * Update existing user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,engineer,nms',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return back()->with('success', "User {$user->name} has been updated successfully.");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        /** @var int $currentUserId */
        $currentUserId = Auth::id();
        
        if ($user->id === $currentUserId) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User {$userName} has been deleted successfully.");
    }

    /**
     * Toggle admin verification
     */
    public function toggleAdminVerification(User $user)
    {
        // Hanya admin yang bisa verifikasi
        /** @var User $currentUser */
        $currentUser = Auth::user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            return back()->withErrors(['error' => 'Only admins can verify users.']);
        }

        $newStatus = !$user->is_verified_by_admin;
        /** @var int $currentUserId */
        $currentUserId = $currentUser->id;
        
        $user->update([
            'is_verified_by_admin' => $newStatus,
            'verified_by' => $newStatus ? $currentUserId : null,
            'verified_at' => $newStatus ? now() : null,
        ]);

        $message = $newStatus 
            ? "{$user->name} has been verified and can now login."
            : "{$user->name}'s verification has been revoked.";

        return back()->with('success', $message);
    }
}