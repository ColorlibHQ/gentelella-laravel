{{-- abort(403, '…') sets a deliberate message, so it is shown. --}}
@include('gentelella::errors.layout', [
    'code' => '403',
    'title' => __('Forbidden'),
    'message' => $exception?->getMessage() ?: __('You do not have permission to view this page.'),
    'home' => config('gentelella.auth.home', '/'),
])
