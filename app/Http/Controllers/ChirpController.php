<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ChirpController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab = auth()->check() ? $request->input('tab', 'all') : 'all';

        $query = Chirp::with(['user' => function ($q) {
            $q->withCount(['followers', 'following']);

            if (auth()->check()) {
                $q->with(['followers' => function ($fq) {
                    $fq->where('follower_id', auth()->id());
                }]);
            }
        }])
            ->withCount('likes')
            ->search($search)
            ->latest();

        if ($tab === 'following' && auth()->check()) {
            $query->fromFollowing(auth()->user());
        }

        if (auth()->check()) {
            $query->with(['likes' => function ($q) {
                $q->where('user_id', auth()->id());
            }]);
        }

        $chirps = $query->paginate(15)->withQueryString();

        return view('home', [
            'chirps' => $chirps,
            'search' => $search ?? '',
            'tab' => $tab,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ], [
            'message.required' => 'Please write something to chirp!',
            'message.max' => 'Chirps must be 255 characters or less.',
        ]);

        auth()->user()->chirps()->create($validated);

        return redirect('/')->with('success', 'Your chirp has been posted!');
    }

    public function edit(Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        return view('chirps.edit', compact('chirp'));
    }

    public function update(Request $request, Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        // Validate
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Update
        $chirp->update($validated);

        return redirect('/')->with('success', 'Chirp updated!');
    }

    public function destroy(Chirp $chirp)
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();

        return redirect('/')->with('success', 'Chirp deleted!');
    }
}
