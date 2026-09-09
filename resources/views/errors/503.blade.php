{{-- Never the raw message: maintenance mode carries no user-facing text. --}}
@include('gentelella::errors.layout', [
    'code' => '503',
    'title' => __('Be right back'),
    'message' => __('We are down for maintenance. Please check back shortly.'),
    'home' => config('gentelella.auth.home', '/'),
])
