<x-layout>
    <x-slot:title>
        Edit Profile
    </x-slot:title>

    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mt-8">Edit Profile</h1>

        <div class="card bg-base-100 shadow mt-8">
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Name</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="input input-bordered w-full @error('name') input-error @enderror"
                            maxlength="255"
                            required
                        >
                        @error('name')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div class="form-control w-full mt-4">
                        <label class="label">
                            <span class="label-text">Bio</span>
                        </label>
                        <textarea
                            name="bio"
                            class="textarea textarea-bordered w-full resize-none @error('bio') textarea-error @enderror"
                            rows="4"
                            maxlength="1000"
                            placeholder="Tell us about yourself..."
                        >{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="form-control w-full mt-4">
                        <label class="label">
                            <span class="label-text">Location</span>
                        </label>
                        <input
                            type="text"
                            name="location"
                            value="{{ old('location', $user->location) }}"
                            class="input input-bordered w-full @error('location') input-error @enderror"
                            maxlength="255"
                            placeholder="Tokyo, Japan"
                        >
                        @error('location')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div class="form-control w-full mt-4">
                        <label class="label">
                            <span class="label-text">Website</span>
                        </label>
                        <input
                            type="url"
                            name="website"
                            value="{{ old('website', $user->website) }}"
                            class="input input-bordered w-full @error('website') input-error @enderror"
                            maxlength="255"
                            placeholder="https://example.com"
                        >
                        @error('website')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="card-actions justify-between mt-6">
                        <a href="{{ route('profile.show', $user) }}" class="btn btn-ghost btn-sm">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
