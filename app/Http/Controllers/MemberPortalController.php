<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Member;
use App\Models\NotifikasiMember;
use App\Models\SaldoMutasi;
use App\Models\Topup;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberPortalController extends Controller
{
    private function member(Request $request): Member
    {
        $member = Member::findOrFail((int) $request->session()->get('member_id'));
        if (! $member->kode_qr_member) {
            $member->kode_qr_member = 'MBR-'.Str::upper(Str::random(10));
            $member->save();
        }

        return $member;
    }

    public function dashboard(Request $request)
    {
        $member = $this->member($request);
        $kendaraanIds = $member->kendaraan()->pluck('id_kendaraan');
        $riwayatParkir = Transaksi::whereIn('id_kendaraan', $kendaraanIds)->where('status', 'keluar')->latest('id_parkir')->limit(10)->get();
        $mutasi = SaldoMutasi::where('id_member', $member->id_member)->latest('id_mutasi')->limit(10)->get();
        $notifikasi = NotifikasiMember::where('id_member', $member->id_member)->latest('id_notifikasi')->limit(10)->get();

        return view('member.dashboard', compact('member', 'riwayatParkir', 'mutasi', 'notifikasi'));
    }

    public function topupForm(Request $request)
    {
        $member = $this->member($request);

        return view('member.topup', compact('member'));
    }

    public function submitTopup(Request $request)
    {
        $member = $this->member($request);
        $v = $request->validate([
            'nominal' => 'required|integer|min:10000',
            'metode' => 'required|in:cash,qris,va,ewallet',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        Topup::create([
            'id_member' => $member->id_member,
            'nominal' => $v['nominal'],
            'metode' => $v['metode'],
            'status' => 'pending',
            'ref_gateway' => $v['metode'] === 'cash' ? null : 'MOCK-'.Str::upper(Str::random(10)),
            'payment_proof_path' => $proofPath,
        ]);

        return redirect()->route('member.topup.history')->with('success', 'Request topup dibuat.');
    }

    public function topupHistory(Request $request)
    {
        $member = $this->member($request);
        $topups = Topup::where('id_member', $member->id_member)->latest('id_topup')->paginate(10);

        return view('member.topup_history', compact('topups'));
    }

    public function notifications(Request $request)
    {
        $member = $this->member($request);
        $notifikasi = NotifikasiMember::where('id_member', $member->id_member)->latest('id_notifikasi')->paginate(10);

        return view('member.notifications', compact('notifikasi'));
    }

    public function addKendaraan(Request $request)
    {
        $member = $this->member($request);
        $v = $request->validate([
            'plat_nomor' => 'required|max:15|unique:tb_kendaraan,plat_nomor',
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
        ]);

        Kendaraan::create([
            'plat_nomor' => strtoupper($v['plat_nomor']),
            'jenis_kendaraan' => $v['jenis_kendaraan'],
            'pemilik' => $member->nama_member,
            'id_member' => $member->id_member,
        ]);

        return back()->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function deleteKendaraan(Request $request, Kendaraan $kendaraan)
    {
        $member = $this->member($request);
        if ($kendaraan->id_member !== $member->id_member) {
            abort(403);
        }

        if ($kendaraan->transaksi()->exists()) {
            return back()->with('error', 'Kendaraan tidak bisa dihapus karena sudah ada riwayat transaksi.');
        }

        $kendaraan->delete();

        return back()->with('success', 'Kendaraan berhasil dihapus.');
    }
}
