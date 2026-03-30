@extends('portal::layouts.default')

@section('title', 'Jadwal kerja | ')
@section('navtitle', 'Jadwal kerja')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a class="text-decoration-none" href="{{ request('next', route('portal::schedule.manages.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
        <div class="ms-4">
            <h2 class="mb-1">Buat jadwal kerja baru</h2>
            <div class="text-secondary">Anda dapat membuat jadwal kerja dengan mengisi formulir di bawah</div>
        </div>
    </div>
    <form class="form-block" id="calendar-form" action="{{ route('portal::schedule.manages.collective.store', ['next' => request('next')]) }}" method="POST"> @csrf
        <div class="card card-body mb-5 border-0">
            <div class="row justify-content-center mb-3">
                <div class="col-xl-3" id='external-events'>
                    <div class="fw-bold mb-3">Daftar pengajar</div>
                    @foreach ($employees as $employee)
                        <a class="btn btn-outline-secondary w-100 d-flex text-dark fc-event external-event-container count-{{ $employee->id }} mb-4 rounded bg-white py-1 text-start" style="border-style: dashed;" href="javascript:;" data-event='{"title": "{{ $employee->user->name }}", "id": "{{ $employee->id }}", "empl_id": "{{ $employee->id }}", "position_type": "{{ $employee->position->position->type->value }}", "color": "{{ $employee->color }}"}' data-count="0">
                            <div class="rounded-circle me-2" style="background: url('{{ $employee->user->profile_avatar_path }}') center center no-repeat; background-size: cover; width: 32px; height: 32px;"></div>
                            <div>{{ $employee->user->name }} <br> <small class="text-muted">{{ $employee->position->position->name }}</small> <span class="badge bg-primary count-badge ms-auto">0</span></div>
                        </a>
                    @endforeach
                </div>
                <div class="col-xl-9" id="calendar" class="text-dark"></div>
            </div>
            <div class="mb-3">
                <div class="mb-3">
                    <span class="text-muted"><strong>Catatan</strong>: Fitur ini hanya dapat digunakan dengan waktu default, untuk jadwal dengan kustomisasi waktu silakan menggunakan jadwal per karyawan.</span>
                </div>
                <input type="text" class="d-none form-control mb-3" name="schedules" id="events-input" readonly />
                <button class="btn btn-soft-danger" type="submit"><i class="mdi mdi-check"></i> Simpan jadwal</button>
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        #external-events {
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            border-radius: .5rem;
            border: 0px;
        }

        #external-events .fc-event {
            cursor: pointer;
            margin: 5px 0;
        }

        .remove-event-btn {
            background-color: transparent;
            border: none;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            margin-left: auto;
            display: flex;
            align-items: center;
        }

        .fc-event {
            position: relative;
        }

        .fc-view a {
            color: rgb(39, 40, 39) !important;
        }
    </style>
@endpush

@push('scripts')
    <script type='importmap'>
    {
        "imports": {
            "@fullcalendar/core": "https://cdn.skypack.dev/@fullcalendar/core@6.1.15",
            "@fullcalendar/daygrid": "https://cdn.skypack.dev/@fullcalendar/daygrid@6.1.15",
            "@fullcalendar/timegrid": "https://cdn.skypack.dev/@fullcalendar/timegrid@6.1.15",
            "@fullcalendar/interaction": "https://cdn.skypack.dev/@fullcalendar/interaction@6.1.15"
        }
    }
    </script>
    <script type='module'>
        import {
            Calendar
        } from '@fullcalendar/core'
        import dayGridPlugin from '@fullcalendar/daygrid'
        import interactionPlugin, {
            Draggable
        } from '@fullcalendar/interaction'
        import timeGridPlugin from '@fullcalendar/timegrid'

        document.addEventListener('DOMContentLoaded', function() {
            const containerEl = document.getElementById('external-events');
            const eventCounts = {};

            new Draggable(containerEl, {
                itemSelector: '.fc-event',
                eventData: function(eventEl) {
                    const data = JSON.parse(eventEl.getAttribute('data-event'));
                    const empl_id = data.empl_id;
                    if (!eventCounts[empl_id]) {
                        eventCounts[empl_id] = 0;
                    }
                    incrementEventCount(empl_id);
                    return {
                        title: data.title,
                        empl_id: data.empl_id,
                        poss_type: data.position_type,
                        bgcolor: data.color
                    };
                }
            });

            const calendarEl = document.getElementById('calendar');
            const calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin, interactionPlugin],
                slotMinTime: '06:00:00',
                slotMaxTime: '18:00:00',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                editable: true,
                droppable: true,
                eventOrder: "start,-id",
                eventResizableFromStart: true,
                eventReceive: function(info) {
                    console.log(info.event.extendedProps.bgcolor);
                    info.event.setProp('backgroundColor', info.event.extendedProps.bgcolor);
                    info.event.setProp('borderColor', info.event.extendedProps.bgcolor);
                    saveEvents();
                },
                eventDidMount: function(info) {
                    let removeBtn = document.createElement('button');
                    let icon = document.createElement('i');
                    removeBtn.className = 'remove-event-btn';
                    icon.className = 'mdi mdi-close-circle-outline';
                    icon.style.fontSize = '16px';
                    removeBtn.appendChild(icon);
                    removeBtn.addEventListener('click', function() {
                        decrementEventCount(info.event.extendedProps.empl_id);
                        info.event.remove();
                        saveEvents();
                    });
                    info.el.appendChild(removeBtn);
                },
                eventChange: function() {
                    saveEvents();
                },
                eventRemove: function() {
                    saveEvents();
                },
            });

            calendar.render();

            function saveEvents() {
                const eventsByDate = {};

                const formatter = new Intl.DateTimeFormat('id-ID', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    timeZone: 'Asia/Jakarta'
                });

                calendar.getEvents().forEach(event => {
                    const formattedDateParts = formatter.formatToParts(new Date(event.start));
                    const year = formattedDateParts.find(part => part.type === 'year').value;
                    const month = formattedDateParts.find(part => part.type === 'month').value;
                    const day = formattedDateParts.find(part => part.type === 'day').value;
                    const dateKey = `${year}-${month}-${day}`;

                    if (!eventsByDate[dateKey]) {
                        eventsByDate[dateKey] = [];
                    }

                    const startTime = event.start.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });

                    const endTime = event.end ? event.end.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    }) : null;

                    const shift = eventsByDate[dateKey].length + 1;

                    eventsByDate[dateKey].push({
                        empl_id: event.extendedProps.empl_id,
                        poss_type: event.extendedProps.poss_type,
                        shift: shift,
                        start_time: startTime,
                        end_time: endTime,
                    });
                });

                localStorage.setItem('Jadwal by date', JSON.stringify(eventsByDate));
                document.getElementById('events-input').value = JSON.stringify(eventsByDate);
            }

            function incrementEventCount(empl_id) {
                eventCounts[empl_id]++;
                updateEventCountDisplay(empl_id);
            }

            function decrementEventCount(empl_id) {
                if (eventCounts[empl_id] > 0) {
                    eventCounts[empl_id]--;
                }
                updateEventCountDisplay(empl_id);
            }

            function updateEventCountDisplay(empl_id) {
                const parent = document.querySelector(`.count-${empl_id}`);
                const badge = parent.closest('.external-event-container').querySelector('.badge');
                badge.textContent = eventCounts[empl_id];
            }
        });
    </script>
@endpush
