{{-- abort(404, '…') sets a deliberate message, so it is shown. --}}
@include('gentelella::errors.layout', [
    'code' => '404',
    'title' => __('Page not found'),
    'message' => $exception?->getMessage() ?: __('The page you are looking for does not exist or has been moved.'),
    'home' => config('gentelella.auth.home', '/'),
])
