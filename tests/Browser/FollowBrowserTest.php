<?php

use App\Models\Chirp;
use App\Models\User;

test('user can follow and unfollow asynchronously', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Chirp::factory()->for($other)->create(['message' => 'Followable chirp']);

    browserLogin($user)
        ->navigate('/')
        ->assertSee('Followable chirp')
        ->assertPresent('.follow-button')
        ->click('.follow-button')
        ->waitForText('Following')
        ->assertSee('Following')
        ->click('.follow-button')
        ->waitForText('Follow')
        ->assertSee('Follow')
        ->assertNoJavascriptErrors();
});

test('follower count updates without page reload', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Chirp::factory()->for($other)->create(['message' => 'Count test chirp']);

    browserLogin($user)
        ->navigate('/')
        ->assertSee('Count test chirp')
        ->assertSee('0 followers')
        ->click('.follow-button')
        ->waitForText('Following')
        ->assertSee('1 followers')
        ->assertNoJavascriptErrors();
});

test('own chirps do not show follow button', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'My own chirp here']);

    browserLogin($user)
        ->navigate('/')
        ->assertSee('My own chirp here')
        ->assertNotPresent('.follow-button')
        ->assertNoJavascriptErrors();
});

test('multiple chirps from same user update together', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Chirp::factory()->for($other)->create(['message' => 'First chirp by other']);
    Chirp::factory()->for($other)->create(['message' => 'Second chirp by other']);

    browserLogin($user)
        ->navigate('/')
        ->assertSee('First chirp by other')
        ->assertSee('Second chirp by other')
        ->click(".follow-button[data-user-id=\"{$other->id}\"] >> nth=0")
        ->waitForText('Following')
        ->assertNoJavascriptErrors();
});

test('tabs switch between all and following', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    $notFollowed = User::factory()->create();

    Chirp::factory()->for($followed)->create(['message' => 'Followed user chirp']);
    Chirp::factory()->for($notFollowed)->create(['message' => 'Random user chirp']);

    $user->following()->attach($followed);

    browserLogin($user)
        ->navigate('/')
        ->assertSee('Followed user chirp')
        ->assertSee('Random user chirp')
        ->click('a[href*="tab=following"]')
        ->waitForText('Followed user chirp')
        ->assertSee('Followed user chirp')
        ->assertDontSee('Random user chirp')
        ->assertNoJavascriptErrors();
});

test('following tab shows empty state message', function () {
    $user = User::factory()->create();

    browserLogin($user)
        ->navigate('/?tab=following')
        ->assertSee('Follow some users to see their chirps here!')
        ->assertNoJavascriptErrors();
});

test('guest does not see follow buttons', function () {
    $user = User::factory()->create();
    Chirp::factory()->for($user)->create(['message' => 'Public chirp for guest']);

    visit('/')
        ->assertSee('Public chirp for guest')
        ->assertNotPresent('.follow-button')
        ->assertNoJavascriptErrors();
});
