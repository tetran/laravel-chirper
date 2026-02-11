
@props(['chirp'])

<div class="card bg-base-100 shadow">
    <div class="card-body">
        <div class="flex space-x-3">
            @if ($chirp->user)
                <div class="avatar">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/{{ urlencode($chirp->user->email) }}"
                            alt="{{ $chirp->user->name }}'s avatar" class="rounded-full" />
                    </div>
                </div>
            @else
                <div class="avatar placeholder">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/f61123d5-0b27-434c-a4ae-c653c7fc9ed6?vibe=stealth"
                            alt="Anonymous User" class="rounded-full" />
                    </div>
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <div class="flex justify-between w-full">
                    <div class="flex items-center gap-1">
                        @if ($chirp->user)
                            <a href="{{ route('profile.show', $chirp->user) }}" class="text-sm font-semibold link link-hover">
                                {{ $chirp->user->name }}
                            </a>
                        @else
                            <span class="text-sm font-semibold">Anonymous</span>
                        @endif

                        {{-- Follow Button --}}
                        @auth
                            @if ($chirp->user && $chirp->user->id !== auth()->id())
                                @php
                                    $isFollowing = $chirp->user->relationLoaded('followers')
                                        ? $chirp->user->followers->contains(auth()->user())
                                        : false;
                                @endphp
                                <button
                                    type="button"
                                    class="follow-button btn btn-xs {{ $isFollowing ? 'btn-primary' : 'btn-outline btn-primary' }}"
                                    data-user-id="{{ $chirp->user->id }}"
                                    data-following="{{ $isFollowing ? 'true' : 'false' }}"
                                >
                                    <span class="follow-text">{{ $isFollowing ? 'Following' : 'Follow' }}</span>
                                </button>
                            @endif
                        @endauth

                        <span class="text-base-content/60">·</span>
                        <span class="text-sm text-base-content/60">{{ $chirp->created_at->diffForHumans() }}</span>
                        @if ($chirp->updated_at->gt($chirp->created_at->addSeconds(5)))
                            <span class="text-base-content/60">·</span>
                            <span class="text-sm text-base-content/60 italic">edited</span>
                        @endif
                    </div>

                    <!-- Replace the temporary @php block and $canEdit check with: -->
                    @can('update', $chirp)
                        <div class="flex gap-1">
                            <a href="/chirps/{{ $chirp->id }}/edit" class="btn btn-ghost btn-xs">
                                Edit
                            </a>
                            <form method="POST" action="/chirps/{{ $chirp->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this chirp?')"
                                    class="btn btn-ghost btn-xs text-error">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>

                {{-- Social Counters --}}
                @if ($chirp->user)
                    <div class="flex items-center gap-2 text-xs text-base-content/50">
                        <span class="followers-count" data-user-id="{{ $chirp->user->id }}">{{ $chirp->user->followers_count ?? 0 }}</span> followers
                        <span>·</span>
                        <span>{{ $chirp->user->following_count ?? 0 }}</span> following
                    </div>
                @endif

                <p class="mt-1">{{ $chirp->message }}</p>

                <div class="mt-2 flex items-center gap-1">
                    @auth
                        @can('like', $chirp)
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs like-button transition-transform hover:scale-110 {{ $chirp->likes->contains(auth()->user()) ? 'text-error' : 'text-base-content/40 hover:text-error' }}"
                                data-chirp-id="{{ $chirp->id }}"
                                data-liked="{{ $chirp->likes->contains(auth()->user()) ? 'true' : 'false' }}"
                                aria-label="{{ $chirp->likes->contains(auth()->user()) ? 'Unlike this chirp' : 'Like this chirp' }}"
                            >
                                {{-- Filled heart (liked) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 heart-filled {{ $chirp->likes->contains(auth()->user()) ? '' : 'hidden' }}">
                                    <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                </svg>
                                {{-- Outline heart (not liked) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 heart-outline {{ $chirp->likes->contains(auth()->user()) ? 'hidden' : '' }}">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            </button>
                        @else
                            {{-- Own chirp: show non-interactive heart --}}
                            <span class="btn btn-ghost btn-xs text-base-content/40 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            </span>
                        @endcan
                    @else
                        {{-- Guest: show non-interactive heart --}}
                        <span class="btn btn-ghost btn-xs text-base-content/40 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                        </span>
                    @endauth
                    <span class="text-sm text-base-content/60 likes-count" data-chirp-id="{{ $chirp->id }}">{{ $chirp->likes_count ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
