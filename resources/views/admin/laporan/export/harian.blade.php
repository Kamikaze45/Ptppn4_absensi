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
    td.link a { color: #157347; }
    caption { text-align: left; font-size: 14px; font-weight: bold; padding: 8px 0; }
</style>
</head>
<body>
<table>
    <caption>Laporan Absensi Harian — {{ $tglCarbon->isoFormat('dddd, D MMMM Y') }}</caption>
    <thead>
        <tr>
            <th>Nama Pegawai</th>
            <th>NIK SAP</th>
            <th>Jabatan</th>
            <th>Status</th>
            <th>Tipe</th>
            <th>Check In</th>
            <th>Lokasi Masuk</th>
            <th>Check Out</th>
            <th>Lokasi Keluar</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dataPegawai as $row)
        @php
            $lokasiMasuk = ($row['check_in_lat'] && $row['check_in_lng'])
                ? "https://maps.google.com/?q={$row['check_in_lat']},{$row['check_in_lng']}"
                : null;
            $lokasiKeluar = ($row['check_out_lat'] && $row['check_out_lng'])
                ? "https://maps.google.com/?q={$row['check_out_lat']},{$row['check_out_lng']}"
                : null;
        @endphp
        <tr>
            <td>{{ $row['user']->name }}</td>
            <td>{{ $row['user']->nip ?? '-' }}</td>
            <td>{{ $row['user']->jabatan ?? '-' }}</td>
            <td class="center">{{ $row['status'] }}</td>
            <td class="center">{{ $row['tipe_kerja'] ?? '-' }}</td>
            <td class="center">{{ $row['check_in'] !== '-' ? $row['check_in'] : '-' }}</td>
            <td class="link">
                @if($lokasiMasuk)
                    <a href="{{ $lokasiMasuk }}">{{ $lokasiMasuk }}</a>
                @else
                    -
                @endif
            </td>
            <td class="center">{{ $row['check_out'] !== '-' ? $row['check_out'] : '-' }}</td>
            <td class="link">
                @if($lokasiKeluar)
                    <a href="{{ $lokasiKeluar }}">{{ $lokasiKeluar }}</a>
                @else
                    -
                @endif
            </td>
            <td>{{ $row['keterangan'] ?: '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>