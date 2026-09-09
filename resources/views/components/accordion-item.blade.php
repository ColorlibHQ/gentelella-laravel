{{-- Native <details>, so it works with JS disabled. --}}
@props(['summary', 'open' => false])
<details {{ $attributes->merge(['class' => 'accordion-item'])->merge($open ? ['open' => 'open'] : []) }}>
    <summary class="accordion-summary">{{ $summary }}<svg class="chev" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 6l4 4 4-4"/></svg></summary>
    <div class="accordion-content">{{ $slot }}</div>
</details>
