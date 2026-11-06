<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
@verbatim
<!--[if gte mso 9]>
<xml>
    <x:ExcelWorkbook>
        <x:ExcelWorksheets>
            <x:ExcelWorksheet>
                <x:Name><?php echo e($namaSheet); ?></x:Name>
                <x:WorksheetOptions>
                    <x:DisplayGridlines/>
                </x:WorksheetOptions>
            </x:ExcelWorksheet>
        </x:ExcelWorksheets>
    </x:ExcelWorkbook>
</xml>
<![endif]-->
@endverbatim
<style>
    table { border-collapse: collapse; width: 100%; font-family: Calibri, Arial, sans-serif; }
    th, td { border: 1px solid #D1D5DB; padding: 6px 8px; font-size: 11px; }
    th { background: #157347; color: #FFFFFF; font-weight: bold; text-align: center; }
    td.center { text-align: center; }
    caption { text-align: left; font-size: 14px; font-weight: bold; padding: 8px 0; }
    .tot-hadir     { color: #15803d; font-weight: bold; }
    .tot-terlambat { color: #d97706; font-weight: bold; }
    .tot-alpha     { color: #dc2626; font-weight: bold; }
    .tot-ics       { color: #7c3aed; font-weight: bold; }
</style>
</head>
<body>
<table>
    <caption>
        Laporan Absensi Mingguan — Minggu ke-{{ $minggu }}
        ({{ $awalMinggu->format('d M Y') }} s/d {{ $akhirMinggu->format('d M Y') }})
    </caption>
    <thead>
        <tr>
            <th rowspan="2">Nama Pegawai</th>
            <th rowspan="2">Jabatan</th>
            @foreach($hariKerja as $hk)
                <th>{{ $hk->isoFormat('ddd, DD/MM') }}</th>
            @endforeach
            <th rowspan="2">Hadir</th>
            <th rowspan="2">Telat</th>
            <th rowspan="2">Alpha</th>
            <th rowspan="2">Izin</th>
            <th rowspan="2">Cuti</th>
            <th rowspan="2">Sakit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapMinggu as $row)
        <tr>
            <td>{{ $row['user']->name }}</td>
            <td>{{ $row['user']->jabatan ?? '-' }}</td>
            @foreach($hariKerja as $hk)
                @php
                    $tgl = $hk->toDateString();
                    $det = $row['detail'][$tgl] ?? null;
                    $st  = is_array($det) ? ($det['status'] ?? '-') : ($det ?? '-');
                @endphp
                <td class="center">{{ $st }}</td>
            @endforeach
            <td class="center tot-hadir">{{ $row['hadir'] }}</td>
            <td class="center tot-terlambat">{{ $row['terlambat'] }}</td>
            <td class="center tot-alpha">{{ $row['alpha'] }}</td>
            <td class="center tot-ics">{{ $row['izin'] }}</td>
            <td class="center tot-ics">{{ $row['cuti'] }}</td>
            <td class="center tot-ics">{{ $row['sakit'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>