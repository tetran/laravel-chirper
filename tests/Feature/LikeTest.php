<?php

use App\Models\Chirp;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('guests cannot like a chirp', function () {
    $chirp = Chirp::factory()->create();

    $response = post("/chirps/{$chirp->id}/likes");

    $response->assertRedirect('/login');
    expect($chirp->likes()->count())->toBe(0);
});

test('authenticated user can like a chirp', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    $chirp = Chirp::factory()->for($chirpOwner)->create();

    $response = actingAs($user)->post("/chirps/{$chirp->id}/likes");

    $response->assertRedirect();
    expect($chirp->likes()->count())->toBe(1);
    expect($chirp->likes()->first()->id)->toBe($user->id);
});

test('user can unlike a chirp', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    $chirp = Chirp::factory()->for($chirpOwner)->create();
    $chirp->likes()->attach($user);

    $response = actingAs($user)->delete("/chirps/{$chirp->id}/likes");

    $response->assertRedirect();
    expect($chirp->likes()->count())->toBe(0);
});

test('user cannot like their own chirp', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    $response = actingAs($user)->post("/chirps/{$chirp->id}/likes");

    $response->assertForbidden();
    expect($chirp->likes()->count())->toBe(0);
});

test('user cannot like the same chirp twice', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    $chirp = Chirp::factory()->for($chirpOwner)->create();
    $chirp->likes()->attach($user);

    $response = actingAs($user)->post("/chirps/{$chirp->id}/likes");

    $response->assertRedirect();
    expect($chirp->likes()->count())->toBe(1);
});

test('like count is displayed on the home page', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    $chirp = Chirp::factory()->for($chirpOwner)->create();
    $chirp->likes()->attach($user);

    $response = get('/');

    $response->assertSuccessful();
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->first()->likes_count === 1;
    });
});

test('like via ajax returns json with count and status', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    $chirp = Chirp::factory()->for($chirpOwner)->create();

    $response = actingAs($user)
        ->postJson("/chirps/{$chirp->id}/likes");

    $response->assertOk()
        ->assertJson([
            'likes_count' => 1,
            'is_liked' => true,
        ]);
});

test('unlike via ajax returns json with count and status', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    $chirp = Chirp::factory()->for($chirpOwner)->create();
    $chirp->likes()->attach($user);

    $response = actingAs($user)
        ->deleteJson("/chirps/{$chirp->id}/likes");

    $response->assertOk()
        ->assertJson([
            'likes_count' => 0,
            'is_liked' => false,
        ]);
});

test('likes are deleted when chirp is deleted', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    $chirp = Chirp::factory()->for($chirpOwner)->create();
    $chirp->likes()->attach($user);

    expect($chirp->likes()->count())->toBe(1);

    $chirp->delete();

    $this->assertDatabaseMissing('likes', [
        'chirp_id' => $chirp->id,
    ]);
});
