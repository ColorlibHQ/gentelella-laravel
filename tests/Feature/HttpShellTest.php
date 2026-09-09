<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Gentelella;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;

it('serves a shell page over HTTP', function () {
    Route::get('/admin', fn () => view('shell', [
        'pageKey' => 'dashboard',
        'breadcrumb' => 'Home > Dashboard',
    ]))->name('admin.dashboard');

    $this->get('/admin')
        ->assertOk()
        ->assertSee('id="main-content"', false)
        ->assertSee('<aside class="sidebar"', false)
        ->assertSee('<p id="probe">page body</p>', false)
        ->assertSee('<title>Dashboard | Gentelella</title>', false);
});

it('links a menu item to a named route through the full request cycle', function () {
    Route::get('/admin/reports', fn () => '')->name('admin.reports');
    Route::get('/admin', fn () => view('shell', ['pageKey' => 'reports', 'breadcrumb' => 'Home']));

    config()->set('gentelella.menu', [[
        'label' => 'Admin',
        'items' => [['key' => 'reports', 'text' => 'Reports', 'icon' => 'charts', 'route' => 'admin.reports']],
    ]]);
    app()->forgetInstance(Gentelella::class);

    $this->get('/admin')
        ->assertOk()
        ->assertSee('href="'.url('/admin/reports').'"', false)
        ->assertSee('class="nav-link active"', false);
});

it('renders the sidebar user block from the authenticated user', function () {
    $user = new class extends User
    {
        protected $attributes = ['name' => 'Ada Lovelace', 'role' => 'Owner'];
    };

    Route::get('/admin', fn () => view('shell', ['pageKey' => 'dashboard', 'breadcrumb' => 'Home']));

    $this->actingAs($user)->get('/admin')
        ->assertOk()
        ->assertSee('Ada Lovelace')
        ->assertSee('Owner')
        ->assertSee('<div class="avatar">A<span class="online"></span></div>', false);
});

it('falls back to the configured guest identity when nobody is signed in', function () {
    Route::get('/admin', fn () => view('shell', ['pageKey' => 'dashboard', 'breadcrumb' => 'Home']));

    $this->get('/admin')->assertOk()->assertSee('Guest')->assertSee('Visitor');
});
