{{-- Never the raw message: it is framework wording, not something to show. --}}
@include('gentelella::errors.layout', [
    'code' => '419',
    'title' => __('Page expired'),
    'message' => __('Your session has expired. Refresh and try again.'),
    'home' => config('gentelella.auth.home', '/'),
])
