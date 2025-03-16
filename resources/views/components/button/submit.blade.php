@props([
    'class' => null,
    'slot' => 'Simpan'
])

<button type="submit" class="{{ $class ?: 'btn btn-primary' }}">
    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
    {{ @$slot }}
</button>

@push('after-scripts')
    <script>
        // jika button submit di klik maka tampilkan spinner gunakan jquery
        $('form').on('submit', function () {
            $(this).find('button[type="submit"]').attr('disabled', true).find('.spinner-border').removeClass('d-none');
        });
    </script>
@endpush