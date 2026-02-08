<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function show(User $user)
    {
        $chirps = $user->chirps()
            ->with('user')
            ->latest()
            ->paginate(15);

        return view('profile.show', [
            'user' => $user,
            'chirps' => $chirps,
        ]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ], [
            'name.required' => 'Please enter your name.',
            'name.max' => 'Name must be 255 characters or less.',
            'bio.max' => 'Bio must be 1000 characters or less.',
            'location.max' => 'Location must be 255 characters or less.',
            'website.url' => 'Please enter a valid URL.',
            'website.max' => 'Website must be 255 characters or less.',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show', $user)->with('success', 'Profile updated successfully!');
    }
}
