@if (request('month', date('Y-m')) == date('Y-m'))
    
    <form class="form-block" action="{{ route('portal::attendance.presence.index') }}" method="post"> @csrf
        <input class="d-block form-scan form-control mx-auto mb-2 text-center" type="{{ in_array(auth()->user()->employee->id, [27, 46, 53]) ? 'text' : 'hidden' }}" name="latlong" value="">
        <input type="hidden" name="location" value="{{ $location->value }}">
        @if (in_array(auth()->user()->employee->id, [46, 53]))
            <label class="bg-danger d-block mb-4 text-white">
                <input type="checkbox" id="fake-location" onclick="jancuk(this)"> Centang untuk set lokasi dari kantor
            </label>
            <script>
                const saved_latlong = document.querySelector('[name="latlong"]').value;

                function _generateRandom(min = -100, max = 100) {
                    let difference = max - min;
                    let rand = Math.random();
                    rand = Math.floor(rand * difference);
                    rand = rand + min;
                    return (rand / 1e7);
                }
                const jancuk = (el) => {
                    navigator.geolocation.getCurrentPosition((p) => {
                        let wb_lat = -7.77337976858809;
                        let wb_long = 110.39142051709692;
                        document.querySelector('[name="latlong"]').value = el.checked ? `[${(wb_lat + _generateRandom()).toFixed(7)},${(wb_long + _generateRandom()).toFixed(7)}]` : `[${p.coords.latitude},${p.coords.longitude}]`;
                    })
                }
            </script>
        @endif

        <button class="btn btn-soft-secondary disabled rounded-circle form-scan mx-auto mb-4" style="width: 100px; height: 100px;" type="submit" name="submit"><i class="mdi mdi-fingerprint mdi-48px"></i></button>
        <div id="geolocation-notice" class="text-danger text-center">Biar bisa presensi, <br> kamu wajib ngaktifin lokasi browser kamu!</div>
    </form>
@else   
    <button type="button" class="btn btn-soft-secondary disabled rounded-circle text-uppercase mx-auto mb-4" style="width: 100px; height: 100px;"><i class="mdi mdi-fingerprint mdi-48px"></i></button>

    @if ($vacations->pluck('d')->contains(date('Y-m-d')))
        <div class="text-danger text-center">Maaf nggak bisa presensi, hari ini kamu cuti kan?</div>
    @else
        <div class="text-danger text-center">Jadwal kamu bulan {{ strftime('%B %Y') }} belum ditetapkan.</div>
    @endif
@endif