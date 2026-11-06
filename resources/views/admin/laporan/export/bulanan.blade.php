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
    th, td { border: 1px solid #D1D5DB; padding: 6px 10px; font-size: 12px; }
    th { background: #157347; color: #FFFFFF; font-weight: bold; text-align: center; }
    td.center { text-align: center; }
    caption { text-align: left; font-size: 14px; font-weight: bold; padding: 8px 0; }
    .tot-hadir     { color: #15803d; font-weight: bold; }
    .tot-terlambat { color: #d97706; font-weight: bold; }
    .tot-alpha     { color: #dc2626; font-weight: bold; }
    .tot-izin      { color: #7c3aed; font-weight: bold; }
    .tot-cuti      { color: #0369a1; font-weight: bold; }
    .tot-sakit     { color: #dc2626; font-weight: bold; }
</style>
</head>
<body>
<table>
    <caption>
        Laporan Absensi Bulanan — {{ $namaBulan }} ({{ $totalHariKerja }} hari kerja)
    </caption>
    <thead>
        <tr>
            <th>Nama Pegawai</th>
            <th>Jabatan</th>
            <th>Hadir</th>
            <th>Terlambat</th>
            <th>Alpha</th>
            <th>Izin</th>
            <th>Cuti</th>
            <th>Sakit</th>
            <th>% Kehadiran</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapBulanan->sortByDesc('hadir') as $row)
        <tr>
            <td>{{ $row['user']->name }}</td>
            <td>{{ $row['user']->jabatan ?? '-' }}</td>
            <td class="center tot-hadir">{{ $row['hadir'] }}</td>
            <td class="center tot-terlambat">{{ $row['terlambat'] }}</td>
            <td class="center tot-alpha">{{ $row['alpha'] }}</td>
            <td class="center tot-izin">{{ $row['izin'] }}</td>
            <td class="center tot-cuti">{{ $row['cuti'] }}</td>
            <td class="center tot-sakit">{{ $row['sakit'] }}</td>
            <td class="center">{{ $row['persen_kehadiran'] }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>