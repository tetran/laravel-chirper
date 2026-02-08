<x-layout>
    <x-slot:title>
        {{ $user->name }}'s Profile
    </x-slot:title>

    <div class="max-w-2xl mx-auto">
        <!-- Profile Header -->
        <div class="card bg-base-100 shadow mt-8">
            <div class="card-body">
                <div class="flex items-start gap-4">
                    <div class="avatar">
                        <div class="size-24 rounded-full">
                            <img src="https://avatars.laravel.cloud/{{ urlencode($user->email) }}"
                                alt="{{ $user->name }}'s avatar" />
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                            @can('update', $user)
                                <a href="{{ route('profile.edit', $user) }}" class="btn btn-primary btn-sm">
                                    Edit Profile
                                </a>
                            @endcan
                        </div>

                        @if($user->bio)
                            <p class="mt-3 text-base-content/80 break-words">{{ $user->bio }}</p>
                        @endif

                        <div class="flex flex-wrap gap-4 mt-4 text-sm text-base-content/60">
                            @if($user->location)
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $user->location }}</span>
                                </div>
                            @endif

                            @if($user->website)
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <a href="{{ $user->website }}" target="_blank" rel="noopener noreferrer" class="link link-hover">
                                        {{ parse_url($user->website, PHP_URL_HOST) }}
                                    </a>
                                </div>
                            @endif

                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Joined {{ $user->created_at->format('M Y') }}</span>
                            </div>
                        </div>

                        <div class="stats shadow mt-4">
                            <div class="stat py-3 px-4">
                                <div class="stat-title text-xs">Chirps</div>
                                <div class="stat-value text-2xl">{{ $user->chirps()->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chirps Section -->
        <h2 class="text-2xl font-bold mt-8">Chirps</h2>

        <div class="space-y-4 mt-6">
            @forelse ($chirps as $chirp)
                <x-chirp :chirp="$chirp" />
            @empty
                <div class="hero py-12">
                    <div class="hero-content text-center">
                        <div>
                            <svg class="mx-auto h-12 w-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="mt-4 text-base-content/60">No chirps yet.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($chirps->hasPages())
            <div class="mt-8">
                {{ $chirps->links() }}
            </div>
        @endif
    </div>
</x-layout>
