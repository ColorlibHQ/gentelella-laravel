{{-- Never the exception message — that is raw internals in production. --}}
@include('gentelella::errors.layout', [
    'code' => '500',
    'title' => __('Something went wrong'),
    'message' => __('An unexpected error occurred. It has been logged and will be looked at.'),
    'home' => config('gentelella.auth.home', '/'),
])
