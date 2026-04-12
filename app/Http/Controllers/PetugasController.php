<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Member;
use App\Models\NotifikasiMember;
use App\Models\SaldoMutasi;
use App\Models\Tarif;
use App\Models\Topup;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PetugasController extends Controller
{
    public function dashboard(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $transaksiQuery = Transaksi::where('status', 'masuk')->with(['kendaraan.member', 'area', 'tarif']);
        if ($q !== '') {
            $transaksiQuery->where(function ($query) use ($q) {
                $query->where('id_parkir', 'like', '%'.$q.'%')
                    ->orWhereHas('kendaraan', function ($q2) use ($q) {
                        $q2->where('plat_nomor', 'like', '%'.strtoupper($q).'%');
                    })
                    ->orWhereHas('kendaraan.member', function ($q3) use ($q) {
                        $q3->where('nama_member', 'like', '%'.$q.'%');
                    });
            });
        }
        $transaksi = $transaksiQuery->latest('id_parkir')->get();

        $today = now()->toDateString();
        $shiftMasuk = Transaksi::whereDate('waktu_masuk', $today)->count();
        $shiftKeluar = Transaksi::whereDate('waktu_keluar', $today)->count();
        $shiftTunai = (int) Transaksi::whereDate('waktu_keluar', $today)->where('metode_bayar', 'tunai')->sum('biaya_total');
        $shiftSaldo = (int) Transaksi::whereDate('waktu_keluar', $today)->where('metode_bayar', 'saldo')->sum('biaya_total');
        $areas = AreaParkir::orderBy('nama_area')->get();

        return view('petugas.index', compact('transaksi', 'q', 'shiftMasuk', 'shiftKeluar', 'shiftTunai', 'shiftSaldo', 'areas'));
    }

    public function createEntry(Request $request)
    {
        $areas = AreaParkir::whereRaw('terisi < kapasitas')->get();
        $tarifs = Tarif::all();
        $mode = in_array($request->query('mode'), ['member', 'non-member'], true) ? $request->query('mode') : 'member';

        return view('petugas.entry', compact('areas', 'tarifs', 'mode'));
    }

    public function checkMember(Request $request)
    {
        $plat = strtoupper(trim((string) $request->query('plat_nomor', '')));
        $kendaraan = Kendaraan::with('member')->where('plat_nomor', $plat)->first();
        $tarif = $kendaraan ? Tarif::where('jenis_kendaraan', $kendaraan->jenis_kendaraan)->first() : null;
        if (! $kendaraan || ! $kendaraan->member) {
            return response()->json(['found' => false, 'suggested_tarif_id' => $tarif?->id_tarif]);
        }

        return response()->json([
            'found' => true,
            'suggested_tarif_id' => $tarif?->id_tarif,
            'member' => ['nama_member' => $kendaraan->member->nama_member, 'no_hp' => $kendaraan->member->no_hp],
        ]);
    }

    public function scanMemberQr(Request $request)
    {
        $kodeQr = trim((string) $request->query('kode_qr_member', ''));
        if ($kodeQr === '') {
            return response()->json(['found' => false, 'message' => 'QR kosong.']);
        }

        $member = Member::with('kendaraan')
            ->where('kode_qr_member', $kodeQr)
            ->where('status_aktif', 1)
            ->first();

        if (! $member) {
            return response()->json(['found' => false, 'message' => 'QR member tidak valid.']);
        }

        $kendaraan = $member->kendaraan->first();
        if (! $kendaraan) {
            return response()->json([
                'found' => true,
                'message' => 'QR valid. Member belum punya kendaraan, silakan input plat lalu simpan.',
                'member' => [
                    'id_member' => $member->id_member,
                    'nama_member' => $member->nama_member,
                    'kode_qr_member' => $member->kode_qr_member,
                ],
                'needs_vehicle_input' => true,
                'suggested_tarif_id' => null,
            ]);
        }

        $tarif = Tarif::where('jenis_kendaraan', $kendaraan->jenis_kendaraan)->first();

        return response()->json([
            'found' => true,
            'message' => 'QR valid, siap proses masuk.',
            'member' => [
                'id_member' => $member->id_member,
                'nama_member' => $member->nama_member,
                'kode_qr_member' => $member->kode_qr_member,
            ],
            'kendaraan' => [
                'plat_nomor' => $kendaraan->plat_nomor,
                'jenis_kendaraan' => $kendaraan->jenis_kendaraan,
            ],
            'suggested_tarif_id' => $tarif?->id_tarif,
        ]);
    }

    public function memberLinkForm(Request $request)
    {
        $plat = strtoupper(trim((string) $request->query('plat_nomor', '')));
        $kendaraan = $plat ? Kendaraan::with('member')->where('plat_nomor', $plat)->first() : null;

        return view('petugas.member_link', compact('plat', 'kendaraan'));
    }

    public function saveMemberLink(Request $request)
    {
        $plat = strtoupper((string) $request->input('plat_nomor'));
        $existing = $plat ? Kendaraan::with('member')->where('plat_nomor', $plat)->first() : null;
        $ignore = optional($existing?->member)->id_member;
        $request->validate([
            'plat_nomor' => 'required|max:15',
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
            'nama_member' => 'required|max:100',
            'no_hp' => ['required', 'regex:/^08\d{10}$/'],
            'username_member' => 'nullable|max:50|unique:tb_member,username_member,'.$ignore.',id_member',
            'password_member' => 'nullable|min:4',
        ]);
        $kendaraan = Kendaraan::firstOrCreate(['plat_nomor' => $plat], ['jenis_kendaraan' => $request->jenis_kendaraan]);
        if (! $kendaraan->member) {
            $member = Member::create([
                'nama_member' => $request->nama_member,
                'username_member' => $request->username_member,
                'password_member' => $request->password_member ? Hash::make($request->password_member) : null,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'kode_qr_member' => 'MBR-'.Str::upper(Str::random(10)),
            ]);
            $kendaraan->id_member = $member->id_member;
        } else {
            $payload = [
                'nama_member' => $request->nama_member,
                'username_member' => $request->username_member ?: $kendaraan->member->username_member,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
            ];
            if ($request->password_member) {
                $payload['password_member'] = Hash::make($request->password_member);
            }
            $kendaraan->member->update($payload);
        }
        $kendaraan->jenis_kendaraan = $request->jenis_kendaraan;
        $kendaraan->pemilik = $request->nama_member;
        $kendaraan->save();

        return redirect()->route('petugas.entry')->with('success', 'Data member tersimpan.');
    }

    public function storeEntry(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required',
            'id_tarif' => 'required',
            'id_area' => 'required',
            'scanned_member_id' => 'nullable|integer|exists:tb_member,id_member',
        ]);
        $tarif = Tarif::findOrFail($request->id_tarif);
        $area = AreaParkir::findOrFail($request->id_area);
        if ($area->terisi >= $area->kapasitas) {
            return back()->with('error', 'Area parkir penuh.');
        }
        $kendaraan = Kendaraan::firstOrCreate(['plat_nomor' => strtoupper($request->plat_nomor)], ['jenis_kendaraan' => $tarif->jenis_kendaraan]);

        $scannedMemberId = $request->input('scanned_member_id');
        if ($scannedMemberId) {
            if ($kendaraan->id_member && (int) $kendaraan->id_member !== (int) $scannedMemberId) {
                return back()->with('error', 'Plat nomor ini sudah terhubung ke member lain.');
            }
            if (! $kendaraan->id_member) {
                $kendaraan->id_member = (int) $scannedMemberId;
                $kendaraan->pemilik = optional(Member::find($scannedMemberId))->nama_member ?? $kendaraan->pemilik;
                $kendaraan->save();
            }
        }

        if (Transaksi::where('id_kendaraan', $kendaraan->id_kendaraan)->where('status', 'masuk')->exists()) {
            return back()->with('error', 'Kendaraan masih tercatat parkir.');
        }
        $urutanHarian = Transaksi::whereDate('waktu_masuk', now()->toDateString())->count() + 1;
        $kodeTiket = 'PKR-'.now()->format('Ymd').'-'.str_pad((string) $urutanHarian, 4, '0', STR_PAD_LEFT);

        $trx = Transaksi::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'id_tarif' => $tarif->id_tarif,
            'id_user' => Auth::user()->id_user,
            'id_area' => $area->id_area,
            'waktu_masuk' => now(),
            'status' => 'masuk',
            'kode_qr_tiket' => $kodeTiket,
            'gateway_in_status' => 'auto_open',
        ]);
        $area->increment('terisi');
        LogAktivitas::create(['id_user' => Auth::id(), 'aktivitas' => 'Palang masuk otomatis: tiket #'.$trx->id_parkir, 'waktu_aktivitas' => now()]);

        if ($kendaraan->id_member) {
            NotifikasiMember::create([
                'id_member' => $kendaraan->id_member,
                'judul' => 'Kendaraan masuk parkir',
                'isi' => 'Kendaraan Anda telah masuk parkir dengan tiket #'.$trx->id_parkir.'.',
            ]);
        }

        return redirect()->route('petugas.print', $trx->id_parkir);
    }

    public function checkout($id)
    {
        $transaksi = Transaksi::with(['kendaraan.member', 'tarif'])->findOrFail($id);
        $masuk = Carbon::parse($transaksi->waktu_masuk);
        $keluar = now();
        $durasiMenit = (int) $masuk->diffInMinutes($keluar);
        $durasi = max(1, (int) ceil($durasiMenit / 60));
        $member = $transaksi->kendaraan->member;
        $isMember = (bool) $member;
        $tarifNormalPerJam = (int) $transaksi->tarif->tarif_per_jam;
        $tarifPerJam = $isMember
            ? (int) round($tarifNormalPerJam * 0.9)
            : $tarifNormalPerJam;
        $biaya = $durasi * $tarifPerJam;
        $saldoCukup = $member ? ((int) $member->saldo >= $biaya) : false;

        return view('petugas.checkout', compact('transaksi', 'durasi', 'biaya', 'keluar', 'isMember', 'member', 'saldoCukup', 'tarifPerJam', 'tarifNormalPerJam'));
    }

    public function processExit(Request $request, $id)
    {
        $v = $request->validate(['durasi' => 'required|integer|min:1', 'biaya' => 'required|integer|min:0', 'metode_bayar' => 'required|in:tunai,saldo']);
        $trx = Transaksi::with('kendaraan.member')->findOrFail($id);
        DB::transaction(function () use ($v, $trx) {
            $member = $trx->kendaraan->member;
            if ($v['metode_bayar'] === 'saldo') {
                if (! $member || (int) $member->saldo < (int) $v['biaya']) {
                    throw new \RuntimeException('Saldo member tidak cukup.');
                }
                $before = (int) $member->saldo;
                $after = $before - (int) $v['biaya'];
                if ($after < 0) {
                    throw new \RuntimeException('Saldo member tidak boleh menjadi negatif.');
                }
                $member->update(['saldo' => $after]);
                SaldoMutasi::create([
                    'id_member' => $member->id_member, 'tipe' => 'debit', 'nominal' => $v['biaya'], 'sumber' => 'parkir', 'id_ref' => $trx->id_parkir,
                    'saldo_sebelum' => $before, 'saldo_sesudah' => $after,
                ]);
                NotifikasiMember::create(['id_member' => $member->id_member, 'judul' => 'Pembayaran parkir berhasil', 'isi' => 'Saldo terpotong Rp '.number_format((int) $v['biaya'], 0, ',', '.')]);
            } elseif ($member) {
                NotifikasiMember::create(['id_member' => $member->id_member, 'judul' => 'Pembayaran parkir berhasil', 'isi' => 'Pembayaran parkir tunai berhasil Rp '.number_format((int) $v['biaya'], 0, ',', '.')]);
            }
            $trx->update([
                'waktu_keluar' => now(),
                'durasi_jam' => $v['durasi'],
                'biaya_total' => $v['biaya'],
                'status' => 'keluar',
                'metode_bayar' => $v['metode_bayar'],
                'gateway_out_status' => 'auto_open',
            ]);
            AreaParkir::where('id_area', $trx->id_area)->where('terisi', '>', 0)->decrement('terisi');
        });
        LogAktivitas::create(['id_user' => Auth::id(), 'aktivitas' => 'Checkout tiket #'.$trx->id_parkir.' metode '.$v['metode_bayar'], 'waktu_aktivitas' => now()]);

        return redirect()->route('petugas.print', $trx->id_parkir)->with('success', 'Transaksi selesai.');
    }

    public function lostTicketForm($id)
    {
        $transaksi = Transaksi::with(['kendaraan.member', 'tarif'])->where('status', 'masuk')->findOrFail($id);
        $masuk = Carbon::parse($transaksi->waktu_masuk);
        $durasiMenit = (int) $masuk->diffInMinutes(now());
        $durasi = max(1, (int) ceil($durasiMenit / 60));
        $isMember = (bool) $transaksi->kendaraan->member;
        $tarifNormalPerJam = (int) $transaksi->tarif->tarif_per_jam;
        $tarifPerJam = $isMember
            ? (int) round($tarifNormalPerJam * 0.9)
            : $tarifNormalPerJam;
        $biayaDasar = $durasi * $tarifPerJam;
        $dendaNormal = 10000;
        $dendaDefault = $isMember ? (int) round($dendaNormal * 0.9) : $dendaNormal;
        $biayaTotal = $biayaDasar + $dendaDefault;

        return view('petugas.lost_ticket', compact('transaksi', 'durasi', 'biayaDasar', 'dendaDefault', 'biayaTotal', 'tarifPerJam', 'tarifNormalPerJam', 'dendaNormal'));
    }

    public function processLostTicket(Request $request, $id)
    {
        $v = $request->validate([
            'durasi' => 'required|integer|min:1',
            'biaya_dasar' => 'required|integer|min:0',
            'metode_bayar' => 'required|in:tunai,saldo',
            'alasan' => 'nullable|max:150',
        ]);

        $trx = Transaksi::with('kendaraan.member')->where('status', 'masuk')->findOrFail($id);
        $dendaNormal = 10000;
        $dendaTiketHilang = $trx->kendaraan->member ? (int) round($dendaNormal * 0.9) : $dendaNormal;
        $biayaTotal = (int) $v['biaya_dasar'] + $dendaTiketHilang;

        if ($biayaTotal < 0) {
            return back()->with('error', 'Biaya total tidak boleh negatif.');
        }

        DB::transaction(function () use ($trx, $v, $biayaTotal, $dendaTiketHilang) {
            $member = $trx->kendaraan->member;
            if ($v['metode_bayar'] === 'saldo') {
                if (! $member || (int) $member->saldo < $biayaTotal) {
                    throw new \RuntimeException('Saldo member tidak cukup untuk tiket hilang.');
                }
                $before = (int) $member->saldo;
                $after = $before - $biayaTotal;
                if ($after < 0) {
                    throw new \RuntimeException('Saldo member tidak boleh menjadi negatif.');
                }
                $member->update(['saldo' => $after]);
                SaldoMutasi::create([
                    'id_member' => $member->id_member,
                    'tipe' => 'debit',
                    'nominal' => $biayaTotal,
                    'sumber' => 'tiket_hilang',
                    'id_ref' => $trx->id_parkir,
                    'saldo_sebelum' => $before,
                    'saldo_sesudah' => $after,
                ]);
                NotifikasiMember::create(['id_member' => $member->id_member, 'judul' => 'Pembayaran tiket hilang berhasil', 'isi' => 'Saldo terpotong Rp '.number_format($biayaTotal, 0, ',', '.').' untuk tiket hilang.']);
            } elseif ($member) {
                NotifikasiMember::create(['id_member' => $member->id_member, 'judul' => 'Pembayaran tiket hilang berhasil', 'isi' => 'Pembayaran tiket hilang tunai berhasil Rp '.number_format($biayaTotal, 0, ',', '.').'.']);
            }

            $trx->update([
                'waktu_keluar' => now(),
                'durasi_jam' => $v['durasi'],
                'biaya_total' => $biayaTotal,
                'status' => 'keluar',
                'metode_bayar' => $v['metode_bayar'],
                'gateway_out_status' => 'auto_open',
                'is_tiket_hilang' => 1,
                'denda_tiket_hilang' => $dendaTiketHilang,
            ]);

            AreaParkir::where('id_area', $trx->id_area)->where('terisi', '>', 0)->decrement('terisi');
        });

        LogAktivitas::create([
            'id_user' => Auth::id(),
            'aktivitas' => 'Proses tiket hilang #'.$trx->id_parkir.' denda Rp '.number_format($dendaTiketHilang, 0, ',', '.').($v['alasan'] ? ' ('.$v['alasan'].')' : ''),
            'waktu_aktivitas' => now(),
        ]);

        return redirect()->route('petugas.print', $trx->id_parkir)->with('success', 'Tiket hilang berhasil diproses.');
    }

    public function history()
    {
        $riwayat = Transaksi::where('status', 'keluar')->with(['kendaraan.member', 'user'])->latest('id_parkir')->paginate(15);

        return view('petugas.history', compact('riwayat'));
    }

    public function printReceipt($id)
    {
        $transaksi = Transaksi::with(['kendaraan.member', 'user'])->findOrFail($id);

        return view('petugas.print', compact('transaksi'));
    }

    public function pendingTopup()
    {
        $topups = Topup::with('member')->where('status', 'pending')->latest('id_topup')->paginate(10);
        $topups->getCollection()->transform(function ($topup) {
            $path = ltrim((string) ($topup->payment_proof_path ?? ''), '/');
            $topup->proof_available = $path !== '' && Storage::disk('public')->exists($path);

            return $topup;
        });

        return view('petugas.topup_pending', compact('topups'));
    }

    public function showTopupProof(Topup $topup)
    {
        if (! $topup->payment_proof_path) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        $path = ltrim((string) $topup->payment_proof_path, '/');
        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan di server.');
        }

        return response()->file(Storage::disk('public')->path($path));
    }

    public function verifyTopup(Request $request, Topup $topup)
    {
        if ($topup->status !== 'pending') {
            return back()->with('error', 'Topup sudah diproses.');
        }
        if ((int) $topup->nominal <= 0) {
            return back()->with('error', 'Nominal topup tidak valid.');
        }
        $v = $request->validate([
            'aksi' => 'nullable|in:success,failed',
            'catatan_verifikasi' => 'nullable|max:150',
        ]);
        $aksi = $v['aksi'] ?? 'success';
        if ($aksi === 'failed') {
            $topup->update([
                'status' => 'failed',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'catatan_verifikasi' => $v['catatan_verifikasi'] ?? null,
            ]);
            LogAktivitas::create(['id_user' => Auth::id(), 'aktivitas' => 'Topup ditolak #'.$topup->id_topup, 'waktu_aktivitas' => now()]);

            return back()->with('success', 'Topup ditandai gagal.');
        }

        DB::transaction(function () use ($topup) {
            $member = Member::findOrFail($topup->id_member);
            $before = (int) $member->saldo;
            $after = $before + (int) $topup->nominal;
            if ($after < 0) {
                throw new \RuntimeException('Saldo member tidak boleh menjadi negatif.');
            }
            $member->update(['saldo' => $after]);
            $topup->update(['status' => 'success', 'verified_by' => Auth::id(), 'verified_at' => now()]);
            SaldoMutasi::create([
                'id_member' => $member->id_member, 'tipe' => 'kredit', 'nominal' => $topup->nominal, 'sumber' => 'topup_'.$topup->metode,
                'id_ref' => $topup->id_topup, 'saldo_sebelum' => $before, 'saldo_sesudah' => $after,
            ]);
        });

        $topup->update([
            'catatan_verifikasi' => $v['catatan_verifikasi'] ?? null,
        ]);
        LogAktivitas::create(['id_user' => Auth::id(), 'aktivitas' => 'Topup diverifikasi #'.$topup->id_topup, 'waktu_aktivitas' => now()]);

        return back()->with('success', 'Topup berhasil diverifikasi.');
    }
}
