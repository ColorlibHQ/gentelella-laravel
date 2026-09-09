{{-- Never the raw message: it carries rate-limit internals. --}}
@include('gentelella::errors.layout', [
    'code' => '429',
    'title' => __('Too many requests'),
    'message' => __('You have made too many requests. Wait a moment and try again.'),
    'home' => config('gentelella.auth.home', '/'),
])
