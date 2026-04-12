@extends('layouts.master')
@section('title','Kendaraan Masuk')
@section('content')
<div class="card panel">
    <div class="card-header bg-success text-white">Input Kendaraan Masuk</div>
    <div class="card-body">
        <div class="mb-3 {{ ($mode ?? 'member') === 'non-member' ? 'd-none' : '' }}" id="member_qr_block">
            <label class="form-label">Hasil Scan QR Member</label>
            <div class="input-group">
                <input id="qr_member_input" class="form-control" placeholder="Contoh: MBR-ABC123..." autocomplete="off">
                <button type="button" id="btn_scan_qr" class="btn btn-outline-primary">Proses Scan</button>
            </div>
            <div id="qr_scan_status" class="small mt-2"></div>
        </div>
        <form id="form_entry" method="POST" action="{{ route('petugas.store-entry') }}">@csrf
            <input type="hidden" id="mode_data" value="{{ $mode ?? 'member' }}">
            <input type="hidden" id="scanned_member_id" name="scanned_member_id">
            <div class="mb-3"><label class="form-label">Plat Nomor</label><input id="plat_nomor_input" name="plat_nomor" class="form-control text-uppercase" placeholder="D 1234 ABC" required></div>
            <div id="member_status" class="small mb-2"></div>
            <div class="mb-3"><label class="form-label">Tarif</label><select id="id_tarif_select" name="id_tarif" class="form-select">@foreach($tarifs as $t)<option value="{{ $t->id_tarif }}">{{ ucfirst($t->jenis_kendaraan) }} - Rp {{ number_format((($mode ?? 'member') === 'member') ? ((int)round(((int)$t->tarif_per_jam) * 0.9)) : ((int)$t->tarif_per_jam)) }}/jam</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label">Area Parkir</label><select name="id_area" class="form-select">@foreach($areas as $a)<option value="{{ $a->id_area }}">{{ $a->nama_area }} (Sisa {{ max(0, $a->kapasitas - $a->terisi) }})</option>@endforeach</select></div>
            <button class="btn btn-primary w-100 rounded-pill">Simpan & Cetak Tiket</button>
        </form>
    </div>
</div>
<script>

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('plat_nomor_input');
    const statusBox = document.getElementById('member_status');
    const tarif = document.getElementById('id_tarif_select');
    const qrInput = document.getElementById('qr_member_input');
    const qrBtn = document.getElementById('btn_scan_qr');
    const qrStatus = document.getElementById('qr_scan_status');
    const entryForm = document.getElementById('form_entry');
    const scannedMemberId = document.getElementById('scanned_member_id');
    const modeDataInput = document.getElementById('mode_data');
    const mode = modeDataInput ? modeDataInput.value : 'member';
    let waitingPlateForAutoLink = false;
    let qrMemberName = '';
    let timeoutId = null;

    if (!input || !statusBox || !tarif || !qrInput || !qrBtn || !qrStatus || !entryForm || !scannedMemberId) {
        return;
    }

    input.readOnly = false;
    input.placeholder = 'D 1234 ABC';

    entryForm.addEventListener('submit', (event) => {
        if (!input.value.trim()) {
            event.preventDefault();
            statusBox.innerHTML = '<span class="badge bg-warning">Plat nomor harus diisi</span>';
            return;
        }
        if (waitingPlateForAutoLink && !scannedMemberId.value.trim()) {
            event.preventDefault();
            qrStatus.innerHTML = '<span class="badge bg-warning text-dark">Mohon scan QR member dulu sebelum menyimpan.</span>';
            return;
        }
    });

    qrInput.addEventListener('input', () => {
        if (waitingPlateForAutoLink) {
            waitingPlateForAutoLink = false;
            qrMemberName = '';
            scannedMemberId.value = '';
            qrStatus.innerHTML = '';
        }
    });

    qrBtn.addEventListener('click', async () => {
        const kode = qrInput.value.trim();
        if (!kode) {
            qrStatus.innerHTML = '<span class="badge bg-warning">QR belum diisi</span>';
            return;
        }

        try {
            const response = await fetch(`{{ route('petugas.scan-member-qr') }}?kode_qr_member=${encodeURIComponent(kode)}`);
            const data = await response.json();

            if (!data.found) {
                qrStatus.innerHTML = `<span class="badge bg-danger">${data.message || 'QR tidak valid'}</span>`;
                scannedMemberId.value = '';
                waitingPlateForAutoLink = false;
                return;
            }

            scannedMemberId.value = String(data.member?.id_member || '');
            qrMemberName = data.member?.nama_member || '';
            if (data.suggested_tarif_id) {
                tarif.value = String(data.suggested_tarif_id);
            }

            statusBox.innerHTML = `<span class="badge bg-success">MEMBER: ${qrMemberName || '-'}</span>`;

            if (data.needs_vehicle_input) {
                qrStatus.innerHTML = '<span class="badge bg-warning text-dark">Member belum punya kendaraan. Isi plat nomor lalu klik "Simpan & Cetak Tiket"</span>';
                waitingPlateForAutoLink = true;
                input.focus();
                return;
            }

            waitingPlateForAutoLink = false;
            qrStatus.innerHTML = '<span class="badge bg-success">QR valid. Silakan isi plat nomor untuk melanjutkan.</span>';
        } catch (error) {
            qrStatus.innerHTML = '<span class="badge bg-warning">Gagal proses scan QR</span>';
        }
    });

    input.addEventListener('input', () => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(async () => {
            const plat = input.value.trim().toUpperCase();
            if (plat.length < 3) {
                if (!waitingPlateForAutoLink) {
                    statusBox.innerHTML = '';
                }
                return;
            }

            if (waitingPlateForAutoLink && scannedMemberId.value.trim()) {
                statusBox.innerHTML = `<span class="badge bg-success">MEMBER: ${qrMemberName || '-'} </span>`;
                return;
            }

            try {
                const response = await fetch(`{{ route('petugas.check-member') }}?plat_nomor=${encodeURIComponent(plat)}`);
                const data = await response.json();
                statusBox.innerHTML = data.found
                    ? '<span class="badge bg-success">MEMBER</span>'
                    : '<span class="badge bg-secondary">NON-MEMBER</span>';
                if (data.suggested_tarif_id) {
                    tarif.value = String(data.suggested_tarif_id);
                }
            } catch (error) {
                statusBox.innerHTML = '<span class="badge bg-warning">Gagal cek</span>';
            }
        }, 300);
    });
});
</script>
@endsection
