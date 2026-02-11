<?php

use App\Models\Chirp;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('all tab shows all chirps by default', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Chirp::factory()->for($other)->create(['message' => 'Hello from other']);

    $response = actingAs($user)->get('/');

    $response->assertSuccessful();
    $response->assertSee('Hello from other');
    $response->assertViewHas('tab', 'all');
});

test('following tab shows only followed users and own chirps', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    $notFollowed = User::factory()->create();

    Chirp::factory()->for($user)->create(['message' => 'My own chirp']);
    Chirp::factory()->for($followed)->create(['message' => 'Followed chirp']);
    Chirp::factory()->for($notFollowed)->create(['message' => 'Not followed chirp']);

    $user->following()->attach($followed);

    $response = actingAs($user)->get('/?tab=following');

    $response->assertSuccessful();
    $response->assertSee('My own chirp');
    $response->assertSee('Followed chirp');
    $response->assertDontSee('Not followed chirp');
    $response->assertViewHas('tab', 'following');
});

test('following tab shows own chirps even when not following anyone', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'My solo chirp']);

    $response = actingAs($user)->get('/?tab=following');

    $response->assertSuccessful();
    $response->assertSee('My solo chirp');
});

test('following tab shows empty state when no chirps', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get('/?tab=following');

    $response->assertSuccessful();
    $response->assertSee('Follow some users to see their chirps here!');
});

test('following tab with search works', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    Chirp::factory()->for($followed)->create(['message' => 'Findable chirp']);
    Chirp::factory()->for($followed)->create(['message' => 'Other chirp']);
    $user->following()->attach($followed);

    $response = actingAs($user)->get('/?tab=following&search=Findable');

    $response->assertSuccessful();
    $response->assertSee('Findable chirp');
    $response->assertDontSee('Other chirp');
});

test('following tab pagination works', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    Chirp::factory()->for($followed)->count(20)->create();
    $user->following()->attach($followed);

    $response = actingAs($user)->get('/?tab=following');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 15;
    });
});

test('pagination links preserve tab parameter', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    Chirp::factory()->for($followed)->count(20)->create();
    $user->following()->attach($followed);

    $response = actingAs($user)->get('/?tab=following');

    $response->assertSuccessful();
    $response->assertSee('tab=following');
});

test('guest accessing following tab falls back to all', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Public chirp']);

    $response = get('/?tab=following');

    $response->assertSuccessful();
    $response->assertSee('Public chirp');
    $response->assertViewHas('tab', 'all');
});
