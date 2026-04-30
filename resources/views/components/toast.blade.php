@props(['position' => 'bottom-center'])

@php
    $positions = [
        'top-center'    => 'toast-top-center',
        'top-right'     => 'toast-top-right',
        'bottom-center' => 'toast-bottom-center',
        'bottom-right'  => 'toast-bottom-right',
    ];

    $positionClass = $positions[$position] ?? $positions['bottom-center'];
@endphp

<div id="toast-container" class="toast-container {{ $positionClass }}" aria-live="polite"></div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
@endpush

<script src="{{ asset('js/toast.js') }}"></script>
