<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class UserController
 *
 * Administrative controller for user account management, role allocation (admin/user),
 * manual tier overrides (free/premium), and subscription date adjustments.
 *
 * @package App\Http\Controllers\Admin
 */
class UserController extends Controller
{
    /**
     * Display all registered users with keyword search and role/tier filters.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
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
     * Show the user creation form.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user account with designated role and tier.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
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
        $user->password = Hash::make($validated['password']);
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
     *
     * @param  User  $user
     * @return View
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user credentials, role, tier, and subscription bounds.
     *
     * Protects the active logged-in administrator from self-demotion.
     *
     * @param  Request  $request
     * @param  User     $user
     * @return RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
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
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna "' . $user->name . '" berjaya dikemaskini!');
    }

    /**
     * Delete a user account from database.
     *
     * Prevents the active administrator from accidentally deleting their own account.
     *
     * @param  User  $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak boleh memadam akaun anda sendiri!');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna "' . $name . '" berjaya dipadam.');
    }

    /**
     * Impersonate a user to test access permissions and troubleshoot account issues.
     *
     * Saves the current administrator ID into the session, then logs in as the target user.
     *
     * @param  User  $user
     * @return RedirectResponse
     */
    public function impersonate(User $user): RedirectResponse
    {
        // Guard against impersonating self
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak boleh menyamar sebagai akaun anda sendiri.');
        }

        // Store current admin ID in session
        session()->put('impersonator_id', auth()->id());
        session()->put('impersonated_name', $user->name);

        // Switch authenticated user
        Auth::login($user);

        return redirect()->route('dashboard')->with(
            'success',
            'Mod Menyamar Diaktifkan: Anda kini sedang melihat sistem sebagai "' . $user->name . '" (' . strtoupper($user->tier) . ' Tier).'
        );
    }

    /**
     * Exit impersonation mode and restore the original administrator session.
     *
     * @return RedirectResponse
     */
    public function leaveImpersonation(): RedirectResponse
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $adminId = session()->get('impersonator_id');
        $admin = User::find($adminId);

        if (!$admin || $admin->role !== 'admin') {
            session()->forget(['impersonator_id', 'impersonated_name']);
            return redirect()->route('login')->withErrors(['email' => 'Sesi Pentadbir asal tidak sah. Sila log masuk semula.']);
        }

        // Log back in as admin
        Auth::login($admin);

        // Clean up impersonation session
        session()->forget(['impersonator_id', 'impersonated_name']);

        return redirect()->route('admin.users.index')->with(
            'success',
            'Anda telah kembali ke akaun Pentadbir (' . $admin->name . '). Mod menyamar telah ditamatkan.'
        );
    }
}
