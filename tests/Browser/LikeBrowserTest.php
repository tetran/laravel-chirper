<?php

use App\Models\Chirp;
use App\Models\User;

test('authenticated user can like and unlike a chirp asynchronously', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    Chirp::factory()->for($chirpOwner)->create(['message' => 'Likeable chirp']);

    $page = visit('/login');
    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->submit('form')
        ->navigate('/')
        ->assertSee('Likeable chirp')
        ->assertPresent('[aria-label="Like this chirp"]')
        ->click('[aria-label="Like this chirp"]')
        ->assertPresent('[aria-label="Unlike this chirp"]')
        ->click('[aria-label="Unlike this chirp"]')
        ->assertPresent('[aria-label="Like this chirp"]')
        ->assertNoJavascriptErrors();
});

test('like count updates without page reload', function () {
    $user = User::factory()->create();
    $chirpOwner = User::factory()->create();
    Chirp::factory()->for($chirpOwner)->create(['message' => 'Count chirp']);

    $page = visit('/login');
    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->submit('form')
        ->navigate('/')
        ->assertSee('Count chirp')
        ->click('[aria-label="Like this chirp"]')
        ->assertPresent('[aria-label="Unlike this chirp"]')
        ->assertNoJavascriptErrors();
});

test('own chirp shows non-interactive heart icon', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'My own chirp']);

    $page = visit('/login');
    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->submit('form')
        ->navigate('/')
        ->assertSee('My own chirp')
        ->assertNotPresent('[aria-label="Like this chirp"]')
        ->assertPresent('.pointer-events-none svg')
        ->assertNoJavascriptErrors();
});

test('guest sees heart icon but cannot interact', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create(['message' => 'Public chirp']);
    $chirp->likes()->attach(User::factory()->create());

    $page = visit('/');

    $page->assertSee('Public chirp')
        ->assertSee('1')
        ->assertNotPresent('.like-button')
        ->assertPresent('.pointer-events-none svg')
        ->assertNoJavascriptErrors();
});
