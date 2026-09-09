<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Menu\Filters\ActiveFilter;
use ColorlibHQ\Gentelella\Menu\Filters\GateFilter;
use ColorlibHQ\Gentelella\Menu\Filters\HrefFilter;
use ColorlibHQ\Gentelella\Menu\Filters\SearchFilter;

return [

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | `title` is the base <title>; each page prepends its own section name.
    | The sidebar brand renders `brand_initial` in the square badge, then
    | `brand_name` with `brand_suffix` as a muted <small>.
    |
    */

    'title' => 'Gentelella',
    'title_prefix' => '',
    'title_postfix' => '',

    'brand_name' => 'Gentelella',
    'brand_suffix' => 'v4',
    'brand_initial' => 'G',

    /*
    |--------------------------------------------------------------------------
    | Document
    |--------------------------------------------------------------------------
    |
    | Asset paths are resolved with asset(), so they are relative to public/.
    | `manifest` and `apple_touch_icon` are null by default: the PWA files are
    | opt-in, and pointing at one that was never published only buys a 404.
    |
    */

    'favicon' => null,
    'manifest' => null,
    'apple_touch_icon' => null,
    'google_fonts' => true,

    // The design system registers a service worker in production builds. Leave
    // this off unless you have actually published a sw.js to public/ — an app
    // that has not would take a 404 on every page load for a file it never had.
    'service_worker' => false,

    /*
    |--------------------------------------------------------------------------
    | Sidebar user block
    |--------------------------------------------------------------------------
    |
    | Read off the authenticated user when there is one. The fallbacks are what
    | the demo shows to a guest.
    |
    */

    'user' => [
        'enabled' => true,
        'name_field' => 'name',
        'role_field' => 'role',
        'fallback_name' => 'Guest',
        'fallback_role' => 'Visitor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    |
    | `middleware` wraps every route registered through Route::gentelella().
    | It needs to be session-backed: the forms post with CSRF, flash messages
    | come back through the session, and validation errors only reach the views
    | via the web group's ShareErrorsFromSession middleware.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Sidebar menu
    |--------------------------------------------------------------------------
    |
    | Leave `menu` as null to use the bundled demo sidebar (resources/menu.php,
    | generated from NAV in the upstream template). Set an array to replace it.
    |
    | Each group is ['label' => ..., 'items' => [...]]. An item is a leaf:
    |
    |     ['key' => 'products', 'text' => 'Products', 'icon' => 'shop',
    |      'route' => 'admin.products.index']
    |
    | ...or a parent carrying 'children' => [...]. Address the target with one
    | of 'route' (a route name), 'url' (verbatim), or 'page' (a demo page slug).
    | `key` is matched against the current page key to mark an item active.
    |
    */

    'menu' => null,

    /*
    |--------------------------------------------------------------------------
    | Menu filters
    |--------------------------------------------------------------------------
    |
    | Applied in order to every item before render. GateFilter drops items the
    | current user cannot access ('can' => 'ability'), HrefFilter resolves the
    | target to a URL, ActiveFilter marks the current item, and SearchFilter
    | collects the flattened list that feeds the Cmd+K command palette.
    |
    */

    'filters' => [
        GateFilter::class,
        HrefFilter::class,
        ActiveFilter::class,
        SearchFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Topbar
    |--------------------------------------------------------------------------
    */

    'docs_url' => 'https://gentelella.colorlib.com/docs/',

    /*
    |--------------------------------------------------------------------------
    | Shell links
    |--------------------------------------------------------------------------
    |
    | Where the account menu and the Cmd+K palette send people. Each value is a
    | route name or an absolute path; an entry that resolves to neither is left
    | out, and the affected menu item simply does not appear.
    |
    | Without these the design system falls back to the static template's own
    | pages — profile.html and friends — which do not exist in an application.
    |
    */

    'links' => [
        'profile' => null,
        'settings' => null,
        'theme' => null,
        'help' => null,
        'lock' => null,
        'logout' => 'logout',
    ],
    'search_enabled' => true,
    'theme_toggle' => true,
    'notifications_enabled' => true,
    'messages_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    'footer_left' => 'Gentelella — free admin dashboard template by <a href="https://colorlib.com">Colorlib</a>',
    'footer_right' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Ready-made login, registration and password-reset screens on the template's
    | own auth markup. Off by default: an application that already has auth — a
    | starter kit, Fortify, Breeze — should keep its own routes and simply point
    | them at the `gentelella::auth.*` views.
    |
    | `php artisan gentelella:make-auth` copies the controllers and views into
    | your application when you want to own them outright.
    |
    */

    'auth' => [
        'enabled' => (bool) env('GENTELELLA_AUTH', false),
        'prefix' => '',
        'middleware' => ['web'],

        // Each screen can be switched off on its own — a closed system wants
        // login without registration.
        'register' => true,
        'reset' => true,

        // Where a signed-in user lands, and where an unauthenticated one is sent.
        'home' => '/',

        // Failed logins per minute, keyed by email + IP.
        'throttle' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo pages
    |--------------------------------------------------------------------------
    |
    | The bundled showcase — every page from the static template, served under
    | `demo_prefix`. Off by default so a consumer app ships nothing it did not
    | ask for; the live preview turns it on.
    |
    */

    'demo' => (bool) env('GENTELELLA_DEMO', false),
    'demo_prefix' => 'demo',
    'demo_middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Vite entry points
    |--------------------------------------------------------------------------
    |
    | Written by `php artisan gentelella:install` and referenced by the layout.
    |
    */

    'vite' => [
        'resources/js/gentelella.js',
    ],

];
