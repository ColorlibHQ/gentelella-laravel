{{--
    Base document skeleton — everything common to shell pages and bare pages
    (auth, errors, landing). Mirrors the <head> that the static template's
    shellInjectionPlugin assembles at build time, in the same order.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if (! config('gentelella.service_worker', false)) data-sw="off" @endif>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $gentelella->title(trim(html_entity_decode($__env->yieldContent('title'), ENT_QUOTES))) }}</title>

@if ($favicon = config('gentelella.favicon'))
    <link rel="icon" href="{{ asset($favicon) }}" type="image/svg+xml">
@endif

@if (config('gentelella.google_fonts', true))
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endif

<meta name="theme-color" content="#1ABB9C" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#1a2332" media="(prefers-color-scheme: dark)">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

@if ($manifest = config('gentelella.manifest'))
    <link rel="manifest" href="{{ asset($manifest) }}">
@endif
@if ($touchIcon = config('gentelella.apple_touch_icon'))
    <link rel="apple-touch-icon" href="{{ asset($touchIcon) }}">
@endif

@hasSection('description')
    <meta name="description" content="@yield('description')">
@endif

{{--
    Pre-paint theme + direction: applied to <html> before the body renders so
    neither dark mode nor RTL flashes the wrong way round. Kept byte-identical
    to the static template's inline script.
--}}
<script>(function(){try{var t=localStorage.getItem('theme');var d=window.matchMedia('(prefers-color-scheme: dark)').matches;var theme=t||(d?'dark':'light');document.documentElement.setAttribute('data-theme',theme);var dir=localStorage.getItem('dir');if(dir==='rtl'||dir==='ltr'){document.documentElement.setAttribute('dir',dir);}}catch(e){}})();</script>

@vite(config('gentelella.vite', []))
@stack('head')
</head>
{{--
    data-shell="admin" is what mountShell() looks for. Without it the design
    system wires nothing: no sidebar accordion, no mobile drawer, no theme
    toggle, no topbar panels. Bare pages deliberately omit it.
--}}
<body @class(['gentelella', $bodyClass ?? null])
    @if ($shell ?? false) data-shell="admin" @endif
    @isset($pageKey) data-page="{{ $pageKey }}" @endisset>

@yield('body')

@stack('scripts')
</body>
</html>
