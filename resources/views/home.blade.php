<x-layout>
    <x-slot:title>
        Home Feed
    </x-slot:title>

    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mt-8">Latest Chirps</h1>

        <!-- Search Form -->
        <form method="GET" action="/" class="mt-8">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="form-control">
                <div class="join w-full">
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search chirps..."
                        class="input input-bordered join-item flex-1"
                        maxlength="255"
                    >
                    <button type="submit" class="btn btn-primary join-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                    @if($search)
                        <a href="/?tab={{ $tab }}" class="btn btn-ghost join-item">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Tabs -->
        @auth
            <div role="tablist" class="tabs tabs-bordered mt-8">
                <a role="tab" href="/?search={{ urlencode($search) }}&tab=all"
                   class="tab {{ $tab === 'all' ? 'tab-active' : '' }}">
                    All Chirps
                </a>
                <a role="tab" href="/?search={{ urlencode($search) }}&tab=following"
                   class="tab {{ $tab === 'following' ? 'tab-active' : '' }}">
                    Following
                </a>
            </div>
        @endauth

        <!-- Chirp Form -->
        <div class="card bg-base-100 shadow mt-8">
            <div class="card-body">
                <form method="POST" action="/chirps">
                    @csrf
                    <div class="form-control w-full">
                        <textarea
                            name="message"
                            placeholder="What's on your mind?"
                            class="textarea textarea-bordered w-full resize-none @error('message') textarea-error @enderror"
                            rows="4"
                            maxlength="255"
                            required
                        >{{ old('message') }}</textarea>

                        @error('message')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="mt-4 flex items-center justify-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Chirp
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Feed -->
        <div class="space-y-4 mt-8">
            @forelse ($chirps as $chirp)
                <x-chirp :chirp="$chirp" />
            @empty
                <div class="hero py-12">
                    <div class="hero-content text-center">
                        <div>
                            <svg class="mx-auto h-12 w-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="mt-4 text-base-content/60">
                                @if($search)
                                    No chirps found matching "{{ $search }}". Try a different search term.
                                @elseif($tab === 'following')
                                    Follow some users to see their chirps here!
                                @else
                                    No chirps yet. Be the first to chirp!
                                @endif
                            </p>
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
