<?php

use App\Models\Chirp;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

test('guests cannot follow a user', function () {
    $user = User::factory()->create();

    $response = post("/users/{$user->id}/follow");

    $response->assertRedirect('/login');
    expect($user->followers()->count())->toBe(0);
});

test('authenticated user can follow another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $response = actingAs($user)->post("/users/{$other->id}/follow");

    $response->assertRedirect();
    expect($other->followers()->count())->toBe(1);
    expect($other->followers()->first()->id)->toBe($user->id);
});

test('user can unfollow another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $user->following()->attach($other);

    $response = actingAs($user)->delete("/users/{$other->id}/follow");

    $response->assertRedirect();
    expect($other->followers()->count())->toBe(0);
});

test('user cannot follow themselves', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->post("/users/{$user->id}/follow");

    $response->assertForbidden();
    expect($user->followers()->count())->toBe(0);
});

test('following the same user twice is idempotent', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $user->following()->attach($other);

    $response = actingAs($user)->post("/users/{$other->id}/follow");

    $response->assertRedirect();
    expect($other->followers()->count())->toBe(1);
});

test('follow via ajax returns json with count and status', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $response = actingAs($user)
        ->postJson("/users/{$other->id}/follow");

    $response->assertOk()
        ->assertJson([
            'followers_count' => 1,
            'is_following' => true,
        ]);
});

test('unfollow via ajax returns json with count and status', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $user->following()->attach($other);

    $response = actingAs($user)
        ->deleteJson("/users/{$other->id}/follow");

    $response->assertOk()
        ->assertJson([
            'followers_count' => 0,
            'is_following' => false,
        ]);
});

test('follower count is available on chirps in home page', function () {
    $chirpOwner = User::factory()->create();
    $follower = User::factory()->create();
    Chirp::factory()->for($chirpOwner)->create();
    $follower->following()->attach($chirpOwner);

    $response = actingAs($follower)->get('/');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->first()->user->followers_count === 1;
    });
});
