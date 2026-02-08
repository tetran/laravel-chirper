<?php

use App\Models\Chirp;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

// Profile Display Tests
test('displays user profile page for any user', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'bio' => 'Test bio',
        'location' => 'Tokyo',
        'website' => 'https://example.com',
    ]);

    $response = get(route('profile.show', $user));

    $response->assertSuccessful();
    $response->assertSee('John Doe');
    $response->assertSee('Test bio');
    $response->assertSee('Tokyo');
    $response->assertSee('example.com');
});

test('displays user profile page without authentication', function () {
    $user = User::factory()->create(['name' => 'Jane Doe']);

    $response = get(route('profile.show', $user));

    $response->assertSuccessful();
    $response->assertSee('Jane Doe');
});

test('displays user chirps on profile page', function () {
    $user = User::factory()->create();
    Chirp::factory()->count(3)->for($user)->create();

    $response = get(route('profile.show', $user));

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 3;
    });
});

test('paginates user chirps to 15 per page', function () {
    $user = User::factory()->create();
    Chirp::factory()->count(20)->for($user)->create();

    $response = get(route('profile.show', $user));

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 15;
    });
});

test('displays empty state when user has no chirps', function () {
    $user = User::factory()->create();

    $response = get(route('profile.show', $user));

    $response->assertSuccessful();
    $response->assertSee('No chirps yet');
});

test('displays profile with nullable fields empty', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'bio' => null,
        'location' => null,
        'website' => null,
    ]);

    $response = get(route('profile.show', $user));

    $response->assertSuccessful();
    $response->assertSee('John Doe');
});

// Profile Edit Tests
test('displays edit profile page for authenticated user', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('profile.edit', $user));

    $response->assertSuccessful();
    $response->assertSee('Edit Profile');
});

test('redirects to login when unauthenticated user tries to edit profile', function () {
    $user = User::factory()->create();

    $response = get(route('profile.edit', $user));

    $response->assertRedirect(route('login'));
});

test('denies access when user tries to edit another user profile', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $response = actingAs($user)->get(route('profile.edit', $otherUser));

    $response->assertForbidden();
});

// Profile Update Tests
test('updates user profile successfully', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->put(route('profile.update', $user), [
        'name' => 'Updated Name',
        'bio' => 'Updated bio',
        'location' => 'Updated location',
        'website' => 'https://updated.com',
    ]);

    $response->assertRedirect(route('profile.show', $user));
    $response->assertSessionHas('success', 'Profile updated successfully!');

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->bio)->toBe('Updated bio');
    expect($user->location)->toBe('Updated location');
    expect($user->website)->toBe('https://updated.com');
});

test('denies profile update for unauthenticated user', function () {
    $user = User::factory()->create();

    $response = put(route('profile.update', $user), [
        'name' => 'Updated Name',
    ]);

    $response->assertRedirect(route('login'));
});

test('denies profile update when user tries to update another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $response = actingAs($user)->put(route('profile.update', $otherUser), [
        'name' => 'Updated Name',
    ]);

    $response->assertForbidden();
});

// Validation Tests
test('requires name field', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->put(route('profile.update', $user), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
});

test('validates name max length', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->put(route('profile.update', $user), [
        'name' => str_repeat('a', 256),
    ]);

    $response->assertSessionHasErrors('name');
});

test('validates bio max length', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->put(route('profile.update', $user), [
        'name' => 'Valid Name',
        'bio' => str_repeat('a', 1001),
    ]);

    $response->assertSessionHasErrors('bio');
});

test('validates location max length', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->put(route('profile.update', $user), [
        'name' => 'Valid Name',
        'location' => str_repeat('a', 256),
    ]);

    $response->assertSessionHasErrors('location');
});

test('validates website url format', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->put(route('profile.update', $user), [
        'name' => 'Valid Name',
        'website' => 'not-a-valid-url',
    ]);

    $response->assertSessionHasErrors('website');
});

test('validates website max length', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->put(route('profile.update', $user), [
        'name' => 'Valid Name',
        'website' => 'https://'.str_repeat('a', 256).'.com',
    ]);

    $response->assertSessionHasErrors('website');
});

test('allows nullable fields to be empty', function () {
    $user = User::factory()->create([
        'bio' => 'Old bio',
        'location' => 'Old location',
        'website' => 'https://old.com',
    ]);

    $response = actingAs($user)->put(route('profile.update', $user), [
        'name' => 'Updated Name',
        'bio' => null,
        'location' => null,
        'website' => null,
    ]);

    $response->assertRedirect(route('profile.show', $user));

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->bio)->toBeNull();
    expect($user->location)->toBeNull();
    expect($user->website)->toBeNull();
});
