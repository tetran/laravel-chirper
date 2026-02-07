<?php

use App\Models\Chirp;
use App\Models\User;

use function Pest\Laravel\get;

test('displays all chirps when no search keyword is provided', function () {
    $user = User::factory()->create();
    Chirp::factory()->count(3)->for($user)->create();

    $response = get('/');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 3;
    });
});

test('displays only matching chirps for partial match search', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Hello world']);
    Chirp::factory()->for($user)->create(['message' => 'Good morning']);
    Chirp::factory()->for($user)->create(['message' => 'Hello again']);

    $response = get('/?search=Hello');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 2;
    });
    $response->assertSee('Hello world');
    $response->assertSee('Hello again');
    $response->assertDontSee('Good morning');
});

test('search is case insensitive', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Hello World']);
    Chirp::factory()->for($user)->create(['message' => 'HELLO EVERYONE']);
    Chirp::factory()->for($user)->create(['message' => 'goodbye']);

    $response = get('/?search=hello');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 2;
    });
});

test('displays no results message when no chirps match search', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Hello world']);

    $response = get('/?search=nonexistent');

    $response->assertSuccessful();
    $response->assertSee('No chirps found matching');
    $response->assertSee('nonexistent');
});

test('displays all chirps when search keyword is empty', function () {
    $user = User::factory()->create();
    Chirp::factory()->count(3)->for($user)->create();

    $response = get('/?search=');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 3;
    });
});

test('limits results to 15 chirps per page', function () {
    $user = User::factory()->create();
    Chirp::factory()->count(60)->for($user)->create(['message' => 'Test chirp']);

    $response = get('/');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 15;
    });
});

test('search with 15 chirp limit per page', function () {
    $user = User::factory()->create();
    Chirp::factory()->count(60)->for($user)->create(['message' => 'Searchable chirp']);
    Chirp::factory()->count(10)->for($user)->create(['message' => 'Different chirp']);

    $response = get('/?search=Searchable');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 15;
    });
});

test('handles special characters safely', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Test with @mention']);
    Chirp::factory()->for($user)->create(['message' => 'Test with #hashtag']);
    Chirp::factory()->for($user)->create(['message' => 'Test with 100% success']);

    $response = get('/?search=@mention');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 1;
    });
});

test('search keyword is preserved in view', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Hello world']);

    $response = get('/?search=Hello');

    $response->assertSuccessful();
    $response->assertViewHas('search', 'Hello');
    $response->assertSee('value="Hello"', false);
});

test('displays no chirps message when no chirps exist and no search', function () {
    $response = get('/');

    $response->assertSuccessful();
    $response->assertSee('No chirps yet. Be the first to chirp!');
});
