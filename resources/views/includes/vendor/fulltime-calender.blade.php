@push('after-scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        $(document).ready(function() {
            var calendarEl = $('#calendar');
            var calendar = new FullCalendar.Calendar(calendarEl[0], {
                initialView: 'dayGridMonth',
                @if (@$events)
                    events: @json(@$events)
                @endif
            });
            calendar.render();
        });
    </script>
@endpush
