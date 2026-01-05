<x-filament::page>

    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">
            📅 Kalender Acara Wedding
        </h2>

        <div id="calendar"></div>
    </x-filament::card>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let calendarEl = document.getElementById('calendar');

                let calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    locale: 'id',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: '/api/calendar/orders',
                    eventClick(info) {
                        if (info.event.url) {
                            window.open(info.event.url, '_blank');
                            info.jsEvent.preventDefault();
                        }
                    }
                });

                calendar.render();
            });
        </script>
    @endpush

</x-filament::page>
