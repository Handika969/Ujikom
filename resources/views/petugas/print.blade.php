<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Tiket #{{ $transaksi->id_parkir }}</title>
<style>
    body { font-family: 'Courier New', monospace; margin: 0; padding: 20px; background: #f5f5f5; }
    .receipt-wrapper { max-width: 380px; margin: 0 auto; padding: 20px; background: #fff; border: 2px solid #333; border-radius: 8px; }
    .receipt-header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; }
    .receipt-header h1 { margin: 0; font-size: 28px; font-weight: bold; letter-spacing: 2px; }
    .receipt-header .subtitle { font-size: 12px; color: #666; margin-top: 5px; }
    .receipt-content { margin: 15px 0; }
    .receipt-content p { margin: 8px 0; font-size: 14px; line-height: 1.4; }
    .receipt-content .field { display: flex; justify-content: space-between; }
    .receipt-content .label { font-weight: bold; }
    .receipt-content .value { text-align: right; }
    .receipt-total { border-top: 2px solid #333; margin-top: 15px; padding-top: 10px; }
    .receipt-total p { font-size: 16px; font-weight: bold; }
    .receipt-total .field { border: 1px solid #333; padding: 8px; background: #f9f9f9; }
    .receipt-footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; border-top: 1px dashed #999; padding-top: 10px; }
    .no-print { display: block; text-align: center; margin-top: 15px; padding: 8px 15px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; }
    @media print { body { background: #fff; } .receipt-wrapper { border: 1px solid #000; } .no-print { display: none !important; } }
</style>
</head>
<body onload="window.print()">
<div class="receipt-wrapper">
    <div class="receipt-header">
        <h1>E-PARKING</h1>
        <div class="subtitle">SISTEM PARKIR ELEKTRONIK</div>
    </div>
    <div class="receipt-content">
        <p class="field"><span class="label">No. Tiket:</span> <span class="value">#{{ $transaksi->id_parkir }}</span></p>
        <p class="field"><span class="label">Plat Nomor:</span> <span class="value">{{ $transaksi->kendaraan->plat_nomor }}</span></p>
        <p class="field"><span class="label">Member:</span> <span class="value">{{ $transaksi->kendaraan->member->nama_member ?? 'Non-Member' }}</span></p>
        <p class="field"><span class="label">Waktu Masuk:</span> <span class="value">{{ $transaksi->waktu_masuk }}</span></p>
        @if($transaksi->status==='keluar')
            <p class="field"><span class="label">Waktu Keluar:</span> <span class="value">{{ $transaksi->waktu_keluar }}</span></p>
            <div class="receipt-total">
                <p class="field"><span class="label">TOTAL BIAYA:</span> <span class="value">Rp {{ number_format($transaksi->biaya_total,0,',','.') }}</span></p>
            </div>
        @endif
    </div>
    <div class="receipt-footer">
        Terima kasih atas kunjungan Anda<br>
        Parkir dengan nyaman dan aman
    </div>
    <a href="{{ route('petugas.dashboard') }}" class="no-print">Kembali ke Dashboard</a>
</div>
</body></html>
