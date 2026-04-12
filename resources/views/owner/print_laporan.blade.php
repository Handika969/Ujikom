<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Owner</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        h2, p { margin: 0 0 6px; }
    </style>
</head>
<body onload="window.print()">
    <h2>Laporan Pendapatan Parkir</h2>
    <p>Periode: {{ $start }} s.d {{ $end }}</p>
    <p>Pendapatan Parkir: Rp {{ number_format($pendapatanParkir,0,',','.') }}</p>
    <p>Pendapatan Top Up: Rp {{ number_format($pendapatanTopup,0,',','.') }}</p>
    <p>Total Pendapatan: Rp {{ number_format($totalPendapatan,0,',','.') }}</p>
    <p>Total Transaksi: {{ $totalKendaraan }}</p>
    <table>
        <thead>
            <tr>
                <th>Tiket</th>
                <th>Plat</th>
                <th>Keluar</th>
                <th>Biaya</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
        @forelse($laporan as $l)
            <tr>
                <td>#{{ $l->id_parkir }}</td>
                <td>{{ $l->kendaraan->plat_nomor ?? '-' }}</td>
                <td>{{ $l->waktu_keluar }}</td>
                <td>Rp {{ number_format($l->biaya_total,0,',','.') }}</td>
                <td>{{ $l->user->nama_lengkap ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Tidak ada data</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2 style="margin-top:20px;">Daftar Top Up Berhasil</h2>
    <table>
        <thead>
            <tr>
                <th>ID Top Up</th>
                <th>Waktu</th>
                <th>Member</th>
                <th>Nominal</th>
                <th>Metode</th>
                <th>Verifikator</th>
            </tr>
        </thead>
        <tbody>
        @forelse($topupLaporan as $t)
            <tr>
                <td>#{{ $t->id_topup }}</td>
                <td>{{ $t->created_at }}</td>
                <td>{{ $t->member->nama_member ?? '-' }}</td>
                <td>Rp {{ number_format((int)$t->nominal,0,',','.') }}</td>
                <td>{{ strtoupper($t->metode) }}</td>
                <td>{{ $t->verifiedBy->nama_lengkap ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="6">Tidak ada data top up berhasil</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
