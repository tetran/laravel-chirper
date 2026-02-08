<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

    public function update(UpdateProfileRequest $request, User $user)
    {
        $user->update($request->validated());

        return redirect()->route('profile.show', $user)->with('success', 'Profile updated successfully!');
    }
}
