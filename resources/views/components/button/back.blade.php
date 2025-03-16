@props(['url'])

<a href="{{ @$_GET['back'] ?? $url }}" {!! $attributes->merge(['class' => 'text-primary fw-semibold']) !!}>
    <i class="fas fa-angle-left me-1"></i> Kembali
</a>
