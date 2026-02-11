<?php

use App\Models\Chirp;
use App\Models\User;

test('can view user profile page', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'bio' => 'Test bio content',
        'location' => 'Tokyo',
        'website' => 'https://example.com',
    ]);
    Chirp::factory()->for($user)->create(['message' => 'Test chirp']);

    $page = visit(route('profile.show', $user));

    $page->assertSee('John Doe')
        ->assertSee('Test bio content')
        ->assertSee('Tokyo')
        ->assertSee('example.com')
        ->assertSee('Test chirp')
        ->assertNoJavascriptErrors();
});

test('can navigate to profile from chirp username', function () {
    $user = User::factory()->create(['name' => 'Jane Doe']);
    Chirp::factory()->for($user)->create(['message' => 'Test chirp']);

    $page = visit('/');

    $page->assertSee('Jane Doe')
        ->click('Jane Doe')
        ->assertTitleContains("Jane Doe's Profile")
        ->assertNoJavascriptErrors();
});

test('can edit own profile', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'bio' => 'Original bio',
    ]);

    $page = browserLogin($user)
        ->navigate(route('profile.show', $user));

    $page->assertSee('Edit Profile')
        ->click('Edit Profile')
        ->assertSee('Edit Profile')
        ->fill('name', 'Updated Name')
        ->fill('bio', 'Updated bio content')
        ->fill('location', 'Osaka')
        ->fill('website', 'https://updated.com')
        ->click('Update Profile')
        ->assertSee('Profile updated successfully!')
        ->assertSee('Updated Name')
        ->assertSee('Updated bio content')
        ->assertSee('Osaka')
        ->assertSee('updated.com')
        ->assertNoJavascriptErrors();
});

test('edit profile cancel button returns to profile', function () {
    $user = User::factory()->create(['name' => 'John Doe']);

    $page = browserLogin($user)
        ->navigate(route('profile.edit', $user));

    $page->assertSee('Edit Profile')
        ->click('Cancel')
        ->assertTitleContains("John Doe's Profile")
        ->assertNoJavascriptErrors();
});

test('shows validation errors for invalid profile data', function () {
    $user = User::factory()->create();

    $page = browserLogin($user)
        ->navigate(route('profile.edit', $user));

    $page->fill('name', '')
        ->fill('website', 'not-a-valid-url');

    $page->script("document.querySelector('.card-body form').submit()");

    $page->assertSee('Please enter your name')
        ->assertSee('Please enter a valid URL')
        ->assertNoJavascriptErrors();
});

test('profile page shows empty state when user has no chirps', function () {
    $user = User::factory()->create(['name' => 'Empty User']);

    $page = visit(route('profile.show', $user));

    $page->assertSee('Empty User')
        ->assertSee('No chirps yet')
        ->assertNoJavascriptErrors();
});

test('edit profile button is not visible for other users', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $otherUser = User::factory()->create();

    $page = browserLogin($otherUser)
        ->navigate(route('profile.show', $user));

    $page->assertSee('John Doe')
        ->assertDontSee('Edit Profile')
        ->assertNoJavascriptErrors();
});

test('profile page is accessible without authentication', function () {
    $user = User::factory()->create(['name' => 'Public User']);

    $page = visit(route('profile.show', $user));

    $page->assertSee('Public User')
        ->assertDontSee('Edit Profile')
        ->assertNoJavascriptErrors();
});
