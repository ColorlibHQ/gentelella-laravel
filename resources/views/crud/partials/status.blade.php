@if (session('status'))
    <x-gentelella::banner tone="success">{{ session('status') }}</x-gentelella::banner>
@endif
