@extends('layouts.app')
@section('page-title', 'Absensi')
@section('title', 'Absensi')

@push('styles')
<style>
    #map { height: 280px; border-radius: 12px; border: 2px solid var(--border); overflow: hidden; }

    .absen-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .absen-hero {
        background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-main) 100%);
        padding: 24px;
        text-align: center;
        color: white;
        position: relative;
    }

    .clock-display {
        font-family: 'Sora', sans-serif;
        font-size: 52px;
        font-weight: 800;
        letter-spacing: 2px;
        line-height: 1;
        margin-bottom: 6px;
    }

    .clock-date { font-size: 14px; opacity: 0.8; }

    .absen-body { padding: 24px; }

    .location-box {
        background: var(--gray-light);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .location-box i { color: #ef4444; font-size: 18px; flex-shrink: 0; margin-top: 2px; }
    .location-box .loc-text { font-size: 13px; color: var(--dark); line-height: 1.5; flex: 1; }
    .location-box .loc-coords { font-size: 11px; color: var(--gray); margin-top: 2px; }

    .absen-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .btn-absen {
        padding: 16px 20px;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        transition: all 0.25s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .btn-absen i { font-size: 28px; }
    .btn-absen span { font-size: 12px; font-weight: 500; opacity: 0.85; }

    .btn-checkin { background: linear-gradient(135deg, #16a34a, #0d7a39); color: white; box-shadow: 0 4px 18px rgba(22,163,74,0.35); }
    .btn-checkin:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(22,163,74,0.4); }
    .btn-checkout { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; box-shadow: 0 4px 18px rgba(37,99,235,0.35); }
    .btn-checkout:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(37,99,235,0.4); }
    .btn-absen:disabled { opacity: 0.4; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

    .status-done {
        background: #dcfce7;
        border: 1.5px solid #bbf7d0;
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: #15803d;
        font-weight: 600;
    }
    .status-done i { font-size: 20px; }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 20px;
    }

    .info-item {
        background: var(--gray-light);
        border-radius: 12px;
        padding: 14px;
    }
    .info-item label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray); font-weight: 600; display: block; margin-bottom: 4px; }
    .info-item .info-val { font-family: 'Sora', sans-serif; font-size: 18px; font-weight: 700; color: var(--dark); }
    .info-item .info-val.green { color: var(--green-main); }
    .info-item .info-val.blue { color: #3b82f6; }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: white;
        border-radius: 20px;
        padding: 32px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        animation: modalIn 0.3s ease;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    .modal-icon { font-size: 52px; margin-bottom: 16px; }
    .modal-title { font-family: 'Sora', sans-serif; font-size: 20px; font-weight: 700; margin-bottom: 8px; }
    .modal-msg { font-size: 14px; color: var(--gray); margin-bottom: 20px; line-height: 1.6; }

    .loading-spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Absensi Kehadiran</h1>
        <p>Lakukan check in dan check out kehadiran Anda hari ini</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;" class="absen-layout">
    <!-- Map & Location -->
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header" style="padding-bottom:16px;">
                <div class="card-title"><i class="fas fa-map-marked-alt" style="color:var(--green-main);"></i> Lokasi Anda</div>
                <button onclick="refreshLocation()" class="btn btn-secondary btn-sm" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i> Perbarui
                </button>
            </div>
            <div class="card-body" style="padding-top:0;">
                <div id="map"></div>
                <div class="location-box" style="margin-top:14px;margin-bottom:0;">
                    <i class="fas fa-map-pin"></i>
                    <div>
                        <div class="loc-text" id="addressText">Mendeteksi lokasi...</div>
                        <div class="loc-coords" id="coordsText"></div>
                    </div>
                    <div id="gpsStatus" style="flex-shrink:0;">
                        <i class="fas fa-spinner fa-spin" style="color:var(--gray);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Absen Actions -->
    <div>
        <div class="absen-card">
            <div class="absen-hero">
                <div class="clock-display" id="bigClock">00:00:00</div>
                <div class="clock-date" id="bigDate"></div>
            </div>
            <div class="absen-body">
                <!-- Today Info -->
                <div class="info-grid">
                    <div class="info-item">
                        <label>Check In</label>
                        <div class="info-val green">{{ $todayAttendance?->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '--:--' }}</div>
                    </div>
                    <div class="info-item">
                        <label>Check Out</label>
                        <div class="info-val blue">{{ $todayAttendance?->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') : '--:--' }}</div>
                    </div>
                </div>

                @if($todayAttendance && $todayAttendance->check_out)
                <div class="status-done">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <div>Absensi hari ini selesai</div>
                        <div style="font-weight:400;font-size:12px;margin-top:2px;">Durasi: {{ $todayAttendance->work_hours }}</div>
                    </div>
                </div>
                @else
                <div class="absen-actions">
                    <button class="btn-absen btn-checkin" id="btnCheckIn"
                        onclick="doAbsen('in')"
                        {{ $todayAttendance ? 'disabled' : '' }}>
                        <i class="fas fa-sign-in-alt"></i>
                        <strong>Check In</strong>
                        <span>Absen Masuk</span>
                    </button>
                    <button class="btn-absen btn-checkout" id="btnCheckOut"
                        onclick="doAbsen('out')"
                        {{ !$todayAttendance || $todayAttendance->check_out ? 'disabled' : '' }}>
                        <i class="fas fa-sign-out-alt"></i>
                        <strong>Check Out</strong>
                        <span>Absen Keluar</span>
                    </button>
                </div>
                @endif

                <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--gray);text-align:center;line-height:1.6;">
                        <i class="fas fa-info-circle"></i>
                        Jam kerja: <strong>08:00</strong> — <strong>17:00</strong><br>
                        Absen setelah 08:00 dianggap terlambat
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal-overlay" id="resultModal">
    <div class="modal-box">
        <div class="modal-icon" id="modalIcon">✅</div>
        <div class="modal-title" id="modalTitle">Berhasil!</div>
        <div class="modal-msg" id="modalMsg"></div>
        <button onclick="closeModal()" class="btn btn-primary" style="width:100%;">OK</button>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNBQ&libraries=places&callback=initMap" async defer></script>
<script>
    // Clock
    function updateBigClock() {
        const now = new Date();
        const t = now.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:false});
        document.getElementById('bigClock').textContent = t;
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        document.getElementById('bigDate').textContent = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    }
    updateBigClock();
    setInterval(updateBigClock, 1000);

    // Location & Map
    let userLat = null, userLng = null, userAddress = null;
    let map = null, marker = null;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: { lat: -3.7957, lng: 102.2551 }, // Bengkulu area
            styles: [
                { featureType: 'water', stylers: [{ color: '#a8d5f0' }] },
                { featureType: 'landscape', stylers: [{ color: '#f0f5ef' }] },
            ],
            disableDefaultUI: false,
            zoomControl: true,
        });
        getLocation();
    }

    function getLocation() {
        if (!navigator.geolocation) {
            showLocationError('GPS tidak didukung browser ini.');
            return;
        }
        document.getElementById('gpsStatus').innerHTML = '<i class="fas fa-spinner fa-spin" style="color:var(--gray);"></i>';
        navigator.geolocation.getCurrentPosition(onLocationSuccess, onLocationError, {
            enableHighAccuracy: true, timeout: 15000, maximumAge: 0
        });
    }

    function onLocationSuccess(pos) {
        userLat = pos.coords.latitude;
        userLng = pos.coords.longitude;
        document.getElementById('coordsText').textContent = 'Lat: ' + userLat.toFixed(6) + ', Lng: ' + userLng.toFixed(6);
        document.getElementById('gpsStatus').innerHTML = '<i class="fas fa-check-circle" style="color:var(--green-main);"></i>';

        const latlng = { lat: userLat, lng: userLng };
        if (map) {
            map.setCenter(latlng);
            if (marker) marker.setMap(null);
            marker = new google.maps.Marker({
                position: latlng,
                map: map,
                title: 'Lokasi Anda',
                icon: { path: google.maps.SymbolPath.CIRCLE, scale: 10, fillColor: '#1a8a45', fillOpacity: 1, strokeColor: 'white', strokeWeight: 2 }
            });
        }

        // Reverse geocode
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: latlng }, (results, status) => {
            if (status === 'OK' && results[0]) {
                userAddress = results[0].formatted_address;
                document.getElementById('addressText').textContent = userAddress;
            } else {
                userAddress = 'Lat: ' + userLat.toFixed(5) + ', Lng: ' + userLng.toFixed(5);
                document.getElementById('addressText').textContent = userAddress;
            }
        });
    }

    function onLocationError(err) {
        showLocationError('Gagal mendapatkan lokasi. Pastikan GPS aktif.');
        document.getElementById('gpsStatus').innerHTML = '<i class="fas fa-times-circle" style="color:#ef4444;"></i>';
    }

    function showLocationError(msg) {
        document.getElementById('addressText').textContent = msg;
        userLat = -3.7957; userLng = 102.2551; // Default coords
    }

    function refreshLocation() {
        document.getElementById('addressText').textContent = 'Mendeteksi lokasi...';
        document.getElementById('coordsText').textContent = '';
        getLocation();
    }

    async function doAbsen(type) {
        if (!userLat || !userLng) {
            showModal('⚠️', 'Lokasi Belum Tersedia', 'Tunggu hingga GPS berhasil mendeteksi lokasi Anda.', false);
            return;
        }

        const btn = type === 'in' ? document.getElementById('btnCheckIn') : document.getElementById('btnCheckOut');
        btn.disabled = true;
        btn.innerHTML = '<div class="loading-spinner"></div><strong>Memproses...</strong>';

        try {
            const res = await fetch('{{ route("attendance." . ($todayAttendance ? "checkout" : "checkin")) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ latitude: userLat, longitude: userLng, address: userAddress })
            });

            const url = type === 'in' ? '{{ route("attendance.checkin") }}' : '{{ route("attendance.checkout") }}';
            const res2 = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ latitude: userLat, longitude: userLng, address: userAddress })
            });

            const data = await res2.json();
            if (data.success) {
                const icon = type === 'in' ? '✅' : '🏁';
                let msg = data.message;
                if (type === 'in') msg += '<br><br>Waktu: <strong>' + data.time + '</strong>';
                if (type === 'out') msg += '<br>Durasi: <strong>' + (data.duration || '-') + '</strong>';
                showModal(icon, 'Berhasil!', msg, true);
            } else {
                showModal('⚠️', 'Gagal', data.message, false);
                btn.disabled = false;
                if (type === 'in') btn.innerHTML = '<i class="fas fa-sign-in-alt"></i><strong>Check In</strong><span>Absen Masuk</span>';
                else btn.innerHTML = '<i class="fas fa-sign-out-alt"></i><strong>Check Out</strong><span>Absen Keluar</span>';
            }
        } catch(e) {
            showModal('❌', 'Error', 'Terjadi kesalahan. Coba lagi.', false);
            btn.disabled = false;
        }
    }

    function showModal(icon, title, msg, reload) {
        document.getElementById('modalIcon').textContent = icon;
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMsg').innerHTML = msg;
        document.getElementById('resultModal').classList.add('show');
        if (reload) {
            document.querySelector('#resultModal button').onclick = () => location.reload();
        }
    }

    function closeModal() {
        document.getElementById('resultModal').classList.remove('show');
    }
</script>
<style>
@media(max-width:900px) {
    .absen-layout { grid-template-columns: 1fr !important; }
}
</style>
@endpush
