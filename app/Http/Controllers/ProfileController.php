<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Class ProfileController
 *
 * Manages user profile settings, avatar image uploads into user-isolated directories,
 * and account closure/deletion.
 *
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * Display the user's account profile management form.
     *
     * @param  Request  $request
     * @return View
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's personal details and avatar image file.
     *
     * If an avatar file is submitted, it is organized into `/uploads/users/{id}/`.
     *
     * @param  ProfileUpdateRequest  $request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle profile picture upload into user-scoped directory
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $uploadDir = public_path('uploads/users/' . $user->id);

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'avatar_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);

            $user->avatar = '/uploads/users/' . $user->id . '/' . $filename;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Terminate and purge the user's account after verifying current password.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
