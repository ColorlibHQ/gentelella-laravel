# Installation

## Requirements

| | |
|---|---|
| PHP | 8.3+ |
| Laravel | 13 |
| Node.js | 18+ (Vite asset pipeline) |

## 1. Require the package

```bash
composer require colorlibhq/gentelella-laravel
```

## 2. Run the installer

```bash
php artisan gentelella:install
```

It publishes `config/gentelella.php` and writes two Vite entry stubs:

```
resources/js/gentelella.js     imports the design system, then your overrides
resources/css/gentelella.scss  your overrides
```

## 3. Install the frontend dependencies

```bash
npm install gentelella@^4.1 sass
```

Only two. ECharts, DataTables and Leaflet are pulled in lazily by the pages that use them, exactly
as in the HTML edition — nothing ships to a page that does not need it.

## 4. Wire Vite

```js
// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/gentelella.js'],
            refresh: true,
        }),
    ],
})
```

**One entry, not two.** `gentelella.js` imports the design system and then `gentelella.scss`, so
your overrides always load after the base. Listing the stylesheet as a second Vite entry leaves the
order undefined, because Vite also emits CSS from the JS graph.

## 5. Build

```bash
npm run dev      # development
npm run build    # production
```

## 6. Your first page

```blade
{{-- resources/views/dashboard.blade.php --}}
@extends('gentelella::page')

@section('title', 'Dashboard')
@section('page_key', 'dashboard')
@section('breadcrumb', 'Home > Dashboard')

@section('content')
    <x-gentelella::page-header title="Dashboard" pretitle="Overview" />

    <x-gentelella::card title="Revenue">
        …
    </x-gentelella::card>
@endsection
```

```php
Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
```

That is a complete page: sidebar, topbar, breadcrumb, dark mode and the ⌘K palette all come from
the layout.

## Optional next steps

```bash
php artisan gentelella:make-auth   # own the auth screens
php artisan gentelella:crud Product   # scaffold a CRUD panel from a table
php artisan gentelella:demo        # seed the bundled showcase (demo mode only)
```
