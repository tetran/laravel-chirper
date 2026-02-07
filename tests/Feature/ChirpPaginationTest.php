<?php

use App\Models\Chirp;
use App\Models\User;

test('chirps are paginated with 15 items per page', function () {
    $user = User::factory()->create();

    // Create 20 chirps
    Chirp::factory()->count(20)->create(['user_id' => $user->id]);

    $response = $this->get('/');

    $response->assertStatus(200);
    // Should only see 15 chirps on the first page
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 15;
    });
});

test('pagination links are displayed when there are more than 15 chirps', function () {
    $user = User::factory()->create();

    // Create 20 chirps
    Chirp::factory()->count(20)->create(['user_id' => $user->id]);

    $response = $this->get('/');

    $response->assertStatus(200);
    // Pagination links should be present - check for page query parameter
    $response->assertSee('?page=2', false);
});

test('second page displays correct chirps', function () {
    $user = User::factory()->create();

    // Create 20 chirps
    Chirp::factory()->count(20)->create(['user_id' => $user->id]);

    $response = $this->get('/?page=2');

    $response->assertStatus(200);
    // Second page should have 5 chirps (20 total - 15 on first page)
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 5;
    });
});

test('displays all chirps when less than or equal to 15', function () {
    $user = User::factory()->create();

    // Create 10 chirps
    Chirp::factory()->count(10)->create(['user_id' => $user->id]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewHas('chirps', function ($chirps) {
        return $chirps->count() === 10;
    });
});
