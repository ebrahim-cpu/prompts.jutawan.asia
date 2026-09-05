<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display all users with search.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('tier') && $request->tier !== 'all') {
            $query->where('tier', $request->tier);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $premiumCount = User::where('tier', 'premium')->count();
        $freeCount = User::where('tier', 'free')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'adminCount', 'premiumCount', 'freeCount'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'tier' => 'required|in:free,premium',
            'subscription_starts_at' => 'nullable|date',
            'premium_expires_at' => 'nullable|date|after_or_equal:subscription_starts_at',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->tier = $validated['tier'];

        if ($validated['tier'] === 'premium') {
            $user->subscription_starts_at = $validated['subscription_starts_at'] ?? null;
            $user->premium_expires_at = $validated['premium_expires_at'] ?? null;
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna "' . $user->name . '" berjaya ditambah!');
    }

    /**
     * Show user edit form.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user role/tier.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'tier' => 'required|in:free,premium',
            'subscription_starts_at' => 'nullable|date',
            'premium_expires_at' => 'nullable|date|after_or_equal:subscription_starts_at',
        ]);

        // Prevent admin from demoting themselves
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak boleh menukar role anda sendiri!');
        }

        $user->update($validated);

        // If tier changed to free, clear premium expiry & start
        if ($request->tier === 'free') {
            $user->subscription_starts_at = null;
            $user->premium_expires_at = null;
            $user->save();
        } else if ($request->tier === 'premium') {
            // Keep the dates if passed, else use defaults
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna "' . $user->name . '" berjaya dikemaskini!');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak boleh memadam akaun anda sendiri!');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna "' . $name . '" berjaya dipadam.');
    }
}
