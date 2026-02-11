<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, User $user)
    {
        $this->authorize('follow', $user);

        auth()->user()->following()->syncWithoutDetaching($user->id);

        if ($request->wantsJson()) {
            return response()->json([
                'followers_count' => $user->followers()->count(),
                'is_following' => true,
            ]);
        }

        return back();
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('follow', $user);

        auth()->user()->following()->detach($user->id);

        if ($request->wantsJson()) {
            return response()->json([
                'followers_count' => $user->followers()->count(),
                'is_following' => false,
            ]);
        }

        return back();
    }
}
