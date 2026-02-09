<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LikeController extends Controller
{
    use AuthorizesRequests;

    public function store(Chirp $chirp)
    {
        $this->authorize('like', $chirp);

        $chirp->likes()->syncWithoutDetaching(auth()->id());

        return back();
    }

    public function destroy(Chirp $chirp)
    {
        $chirp->likes()->detach(auth()->id());

        return back();
    }
}
