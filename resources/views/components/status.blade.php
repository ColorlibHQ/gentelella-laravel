{{-- Status pill. tone: green | red | yellow | blue --}}
@props(['tone' => 'green'])

<span {{ $attributes->class(['status', 'status-'.$tone]) }}>{{ $slot }}</span>
