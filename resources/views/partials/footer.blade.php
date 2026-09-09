{{-- Mirrors renderFooter() in src/v4/shell-render.js. --}}
<footer class="footer">
    <span>{!! config('gentelella.footer_left') !!}</span>
    <span>{!! config('gentelella.footer_right') ?? 'v'.\ColorlibHQ\Gentelella\Gentelella::VERSION.' · <a href="https://github.com/ColorlibHQ/gentelella-laravel/blob/main/LICENSE" target="_blank" rel="noopener">MIT</a>' !!}</span>
</footer>
