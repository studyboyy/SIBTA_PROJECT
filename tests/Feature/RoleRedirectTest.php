<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('role mismatch redirects authenticated user to their own dashboard', function () {
    Role::firstOrCreate(['name' => 'mahasiswa']);

    $user = User::factory()->create();
    $user->assignRole('mahasiswa');

    $this->actingAs($user)
        ->withHeader('HTTP_REFERER', route('dashboard'))
        ->get(route('dashboard'))
        ->assertRedirect(route('mahasiswa.dashboard'));
});

test('role mismatch does not send admin back to a mahasiswa page', function () {
    Role::firstOrCreate(['name' => 'admin']);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
        ->withHeader('HTTP_REFERER', route('mahasiswa.dashboard'))
        ->get(route('mahasiswa.dashboard'))
        ->assertRedirect(route('dashboard'));
});
