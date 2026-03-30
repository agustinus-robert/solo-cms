@extends('portal::layouts.default')

@section('title', 'Jadwal kerja | ')
@section('navtitle', 'Jadwal kerja')

@php($colors = ['minul' => '#CDF3DF', 'yuyun' => '#FFCFCC', 'topo' => '#FDECCF', 'acep' => '#D1E0FD'])
@php($textColors = ['minul' => '#28a745', 'yuyun' => '#dc3545', 'topo' => '#ffc107', 'acep' => '#007bff'])

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a class="text-decoration-none" href="{{ request('next', route('portal::schedule.nonstaf.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
        <div class="ms-4">
            <h2 class="mb-1">Buat jadwal kerja baru</h2>
            <div class="text-secondary">Anda dapat membuat jadwal kerja dengan mengisi formulir di bawah</div>
        </div>
    </div>
    <form class="form-block" id="calendar-form" action="{{ route('portal::schedule.nonstaf.store', ['next' => request('next')]) }}" method="POST"> @csrf
        <div class="card card-body mb-5 border-0">
            <div class="row justify-content-center mb-3">
                <div class="col-xl-3">
                    <div class="mb-3" id='external-events'>
                        <div class="fw-bold mb-3">Daftar nonstaf</div>
                        @foreach ($employees as $employee)
                            <a class="btn w-100 d-flex text-dark fc-event external-event-container count-{{ $employee->id }} @if (!$loop->last) mb-4 @endif rounded bg-white py-1 text-start" style="border-style: dashed; background-color: {!! $colors[$employee->user->username] !!} !important;" href="javascript:;"
                                data-event='{"title": "{{ $employee->user->name }}", "id": "{{ $employee->id }}", "empl_id": "{{ $employee->id }}", "color": "{{ $colors[$employee->user->username] }}", "textcolor": "{{ $textColors[$employee->user->username] }}"}' data-count="0">
                                <div class="rounded-circle me-2" style="background: url('{{ $employee->user->profile_avatar_path }}') center center no-repeat; background-size: cover; width: 32px; height: 32px;"></div>
                                <div>{{ $employee->user->name }} <br> <small class="text-muted">{{ $employee->position->position->name }}</small> <span class="badge bg-primary count-badge ms-auto">0</span></div>
                            </a>
                        @endforeach
                    </div>
                    <div class="table-responsive">
                        <table class="table-sm table">
                            <thead>
                                <tr class="bg-dark text-light">
                                    <th></th>
                                    <th>Karyawan</th>
                                    <th class="text-center">BL</th>
                                    <th class="text-center">BI</th>
                                    <th class="text-center">TOT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ ucfirst($employee->user->username) }}</td>
                                        <td class="text-center"><input type="number" class="input-index form-control last-{{ $employee->user->username }} text-center"></td>
                                        <td class="text-center"><input type="number" class="input-index form-control now-{{ $employee->id }} text-center"></td>
                                        <td class="text-center">
                                            <input type="number" class="input-index form-control sum-{{ $employee->user->username }} text-center">
                                            <input type="number" class="input-index form-control workdays-{{ $employee->id }} text-center">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xl-9" id="calendar" class="text-dark"></div>
            </div>
            <div class="mb-3">
                <div class="mb-3">
                    <span class="text-muted"><strong>Catatan</strong>: Fitur ini hanya dapat digunakan dengan waktu default, untuk jadwal dengan kustomisasi waktu silakan menggunakan jadwal per karyawan.</span>
                </div>
                <input type="text" class="form-control mb-3" name="month" id="event-dates" value="{{ date('Y-m') }}" onchange="loadPreviousData(event)" readonly />
                <input type="text" class="form-control mb-3" name="schedules" id="events-input" readonly />
                <button class="btn btn-soft-danger" type="submit"><i class="mdi mdi-check"></i> Simpan jadwal</button>
                <a class="btn btn-soft-secondary" id="reset" onclick="resetData()"><i class="mdi mdi-sync"></i> Reset</a>
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

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let url = "{{ route('api::portal.check-schedules') }}";
    </script>
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

            function initializeDraggable() {
                new Draggable(containerEl, {
                    itemSelector: '.fc-event',
                    eventData: function(eventEl) {
                        const data = JSON.parse(eventEl.getAttribute('data-event'));
                        const empl_id = data.empl_id;

                        return {
                            title: data.title,
                            empl_id: data.empl_id,
                            bgcolor: data.color,
                            txcolor: data.textcolor,
                            extendedProps: {
                                empl_id: data.empl_id,
                            }
                        };
                    }
                });
            }

            initializeDraggable();

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

                datesSet: function(info) {
                    let today = getTodayDate(info.start)
                    calendar.refetchEvents();

                    let eventDatesInput = document.getElementById('event-dates');
                    eventDatesInput.value = today;
                    eventDatesInput.dispatchEvent(new Event('change'));
                },

                eventReceive: function(info) {
                    info.event.setProp('backgroundColor', info.event.extendedProps.bgcolor);
                    info.event.setProp('borderColor', info.event.extendedProps.bgcolor);
                    info.event.setProp('textColor', info.event.extendedProps.txcolor);
                    incrementEventCount(info.event.extendedProps.empl_id);
                    countEventsBeforeDate(info.event.extendedProps.empl_id, 20);
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
                        countEventsBeforeDate(info.event.extendedProps.empl_id, 20);
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
                    let count = 0;

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
                        shift: shift,
                        start_time: startTime,
                        end_time: endTime,
                    });
                });

                localStorage.setItem('schedule-by-date', JSON.stringify(eventsByDate));
                document.getElementById('events-input').value = JSON.stringify(eventsByDate);
            }

            function incrementEventCount(empl_id) {
                if (typeof eventCounts[empl_id] === 'undefined') {
                    eventCounts[empl_id] = 0;
                }

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
                const workdays = document.querySelector(`.workdays-${empl_id}`);

                badge.textContent = eventCounts[empl_id];
                workdays.value = eventCounts[empl_id];
            }

            function countEventsBeforeDate(empl_id, dayLimit = 20) {
                const today = new Date();
                const currentMonth = today.getMonth();
                const currentYear = today.getFullYear();
                let count = 0;

                calendar.getEvents().forEach(event => {
                    const eventDate = new Date(event.start);

                    // Check if the event matches the employee, is within the current month and year, and before or on the dayLimit date
                    if (
                        event.extendedProps.empl_id === empl_id &&
                        eventDate.getMonth() === currentMonth &&
                        eventDate.getFullYear() === currentYear &&
                        eventDate.getDate() <= dayLimit
                    ) {
                        count++;
                    }
                });

                // Update the corresponding input field with the count
                const inputField = document.querySelector(`.now-${empl_id}`);
                if (inputField) {
                    inputField.value = count;
                }
            }
        });
    </script>
    <script>
        function debounce(func, delay = 300) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        }

        const reloadForms = () => {
            window.location.reload();
        }

        const resetData = () => {
            localStorage.removeItem('schedule-by-date');
            [...document.querySelectorAll('.input-index')].forEach(el => {
                el.value = '';
            })
            const reloadForm = debounce(reloadForms, 500);
            reloadForm();
        }

        const loadPreviousData = (e) => {
            let month = e.target.value;
            return fetch(url + `?month=${month}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok " + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const counts = data.data.count;
                        if (counts && Object.keys(counts).length > 0) {
                            Object.entries(counts).forEach(([username, count]) => {
                                const tdElement = document.querySelector(`.last-${username}`);
                                if (tdElement) {
                                    tdElement.value = count ?? 0;
                                }
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                    return {
                        error: true,
                        message: error.message
                    };
                });
        };

        const getTodayDate = (dates) => {
            const startDate = new Date(dates);
            startDate.setDate(startDate.getDate() + 7);
            const year = startDate.getFullYear();
            const month = (startDate.getMonth() + 1).toString().padStart(2, '0');
            return `${year}-${month}`;
        }
    </script>
@endpush
