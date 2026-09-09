{{-- Small count/label badge. tone maps to the badge-* colour classes. --}}
@props(['tone' => null])

<span {{ $attributes->class(['badge', $tone ? 'badge-'.$tone : null]) }}>{{ $slot }}</span>
