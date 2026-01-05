<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">
            📅 Kalender Acara
        </h2>

        <div
            wire:ignore
            x-data
            x-init="
                const calendar = new FullCalendar.Calendar($el, {
                    initialView: 'dayGridMonth',
                    height: 550,
                    events: {{ Js::from($events) }},

                    eventClick: function(info) {
                        info.jsEvent.preventDefault(); // stop default

                        if (info.event.url) {
                            window.location.href = info.event.url;
                        }
                    },
                });

                calendar.render();
            "
        ></div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendar = new FullCalendar.Calendar(
                    document.getElementById('calendar'),
                    {
                        initialView: 'dayGridMonth',
                        height: 550,
                        events: @json($events),

                        // supaya klik tetap jalan
                        eventClick: function(info) {
                            if (info.event.url) {
                                window.location.href = info.event.url;
                            }
                        }
                    }
                );

                calendar.render();
            });
        </script>
    </x-filament::card>
</x-filament::widget>
