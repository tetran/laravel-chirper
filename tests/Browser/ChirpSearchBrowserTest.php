<?php

use App\Models\Chirp;
use App\Models\User;

test('can search for chirps using search form', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Laravel is awesome']);
    Chirp::factory()->for($user)->create(['message' => 'PHP is great']);
    Chirp::factory()->for($user)->create(['message' => 'Laravel testing']);

    $page = visit('/');

    $page->assertSee('Laravel is awesome')
        ->assertSee('PHP is great')
        ->assertSee('Laravel testing')
        ->fill('search', 'Laravel')
        ->click('Search')
        ->assertSee('Laravel is awesome')
        ->assertSee('Laravel testing')
        ->assertDontSee('PHP is great');
});

test('clear button appears when search is active', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Test chirp']);

    $page = visit('/');

    $page->assertDontSee('Clear');

    $page->fill('search', 'Test')
        ->click('Search')
        ->assertSee('Clear');
});

test('search input preserves value after search', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Searchable content']);

    $page = visit('/');

    $page->fill('search', 'Searchable')
        ->click('Search')
        ->assertValue('input[name="search"]', 'Searchable');
});

test('clear button redirects to home and clears search', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Test chirp']);

    $page = visit('/?search=Test');

    $page->assertSee('Clear')
        ->click('Clear')
        ->assertValue('input[name="search"]', '');
});

test('displays no results message when search returns nothing', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Existing chirp']);

    $page = visit('/');

    $page->fill('search', 'NonexistentKeyword')
        ->click('Search')
        ->assertSee('No chirps found matching "NonexistentKeyword"');
});
