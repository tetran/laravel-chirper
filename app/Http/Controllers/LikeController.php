<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Chirp $chirp)
    {
        $this->authorize('like', $chirp);

        $chirp->likes()->syncWithoutDetaching(auth()->id());

        if ($request->wantsJson()) {
            return response()->json([
                'likes_count' => $chirp->likes()->count(),
                'is_liked' => true,
            ]);
        }

        return back();
    }

    public function destroy(Request $request, Chirp $chirp)
    {
        $chirp->likes()->detach(auth()->id());

        if ($request->wantsJson()) {
            return response()->json([
                'likes_count' => $chirp->likes()->count(),
                'is_liked' => false,
            ]);
        }

        return back();
    }
}
