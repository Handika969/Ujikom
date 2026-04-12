<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Tarif;
use App\Models\Topup;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        return view('admin.dashboard', [
            'totalUser' => User::count(),
            'masukHariIni' => Transaksi::whereDate('waktu_masuk', $today)->count(),
            'keluarHariIni' => Transaksi::whereDate('waktu_keluar', $today)->count(),
            'sedangParkir' => Transaksi::where('status', 'masuk')->count(),
            'pendapatanHariIni' => (int) Transaksi::whereDate('waktu_keluar', $today)->sum('biaya_total'),
            'transaksiTerbaru' => Transaksi::with(['kendaraan', 'user'])->latest('id_parkir')->limit(10)->get(),
        ]);
    }

    public function users()
    {
        $users = User::orderBy('id_user')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $v = $request->validate([
            'nama_lengkap' => 'required|max:100',
            'username' => 'required|max:50|unique:tb_user,username',
            'password' => 'required|min:4',
            'role' => 'required|in:admin,petugas,owner',
            'status_aktif' => 'required|in:0,1',
        ]);
        User::create($v);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $v = $request->validate([
            'nama_lengkap' => 'required|max:100',
            'username' => 'required|max:50|unique:tb_user,username,'.$user->id_user.',id_user',
            'password' => 'nullable|min:4',
            'role' => 'required|in:admin,petugas,owner',
            'status_aktif' => 'required|in:0,1',
        ]);
        if (empty($v['password'])) {
            unset($v['password']);
        }
        $user->update($v);

        return back()->with('success', 'User berhasil diubah.');
    }

    public function deleteUser(User $user)
    {
        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    public function tarifs()
    {
        $tarifs = Tarif::withCount('transaksi')->orderBy('id_tarif')->get();

        return view('admin.tarif.index', compact('tarifs'));
    }

    public function storeTarif(Request $request)
    {
        $v = $request->validate([
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
            'tarif_per_jam' => 'required|integer|min:0',
        ]);
        Tarif::create($v);

        return back()->with('success', 'Tarif berhasil ditambahkan.');
    }

    public function updateTarif(Request $request, Tarif $tarif)
    {
        $v = $request->validate([
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
            'tarif_per_jam' => 'required|integer|min:0',
        ]);
        $tarif->update($v);

        return back()->with('success', 'Tarif berhasil diubah.');
    }

    public function deleteTarif(Tarif $tarif)
    {
        if ($tarif->transaksi()->exists()) {
            return back()->with('error', 'Tarif tidak bisa dihapus karena sudah dipakai pada transaksi.');
        }
        $tarif->delete();

        return back()->with('success', 'Tarif berhasil dihapus.');
    }

    public function areas()
    {
        $areas = AreaParkir::orderBy('id_area')->get();

        return view('admin.area.index', compact('areas'));
    }

    public function storeArea(Request $request)
    {
        $v = $request->validate([
            'nama_area' => 'required|max:100',
            'kapasitas' => 'required|integer|min:1',
            'terisi' => 'required|integer|min:0',
        ]);
        if ((int) $v['terisi'] > (int) $v['kapasitas']) {
            return back()->withErrors(['terisi' => 'Terisi tidak boleh melebihi kapasitas.'])->withInput();
        }
        AreaParkir::create($v);

        return back()->with('success', 'Area parkir berhasil ditambahkan.');
    }

    public function updateArea(Request $request, AreaParkir $area)
    {
        $v = $request->validate([
            'nama_area' => 'required|max:100',
            'kapasitas' => 'required|integer|min:1',
        ]);
        if ((int) $area->terisi > (int) $v['kapasitas']) {
            return back()->withErrors(['kapasitas' => 'Kapasitas baru tidak boleh lebih kecil dari jumlah terisi saat ini.'])->withInput();
        }
        $area->update($v);

        return back()->with('success', 'Area parkir berhasil diubah.');
    }

    public function deleteArea(AreaParkir $area)
    {
        $area->delete();

        return back()->with('success', 'Area parkir berhasil dihapus.');
    }

    public function kendaraan()
    {
        $kendaraan = Kendaraan::with('member')->withCount('transaksi')->orderBy('id_kendaraan')->paginate(10);

        return view('admin.kendaraan.index', compact('kendaraan'));
    }

    public function storeKendaraan(Request $request)
    {
        $v = $request->validate([
            'plat_nomor' => 'required|max:15|unique:tb_kendaraan,plat_nomor',
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
            'pemilik' => 'nullable|max:100',
        ]);
        $v['plat_nomor'] = strtoupper($v['plat_nomor']);
        Kendaraan::create($v);

        return back()->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function updateKendaraan(Request $request, Kendaraan $kendaraan)
    {
        $v = $request->validate([
            'plat_nomor' => 'required|max:15|unique:tb_kendaraan,plat_nomor,'.$kendaraan->id_kendaraan.',id_kendaraan',
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
            'pemilik' => 'nullable|max:100',
        ]);
        $v['plat_nomor'] = strtoupper($v['plat_nomor']);
        $kendaraan->update($v);

        return back()->with('success', 'Kendaraan berhasil diubah.');
    }

    public function deleteKendaraan(Kendaraan $kendaraan)
    {
        if ($kendaraan->transaksi()->exists()) {
            return back()->with('error', 'Kendaraan tidak bisa dihapus karena sudah memiliki riwayat transaksi.');
        }
        $kendaraan->delete();

        return back()->with('success', 'Kendaraan berhasil dihapus.');
    }

    public function logs()
    {
        $logs = LogAktivitas::with('user')->orderByDesc('id_log')->paginate(20);
        $topupLogs = Topup::with(['member', 'verifiedBy'])->latest('id_topup')->limit(20)->get();

        return view('admin.logs.index', compact('logs', 'topupLogs'));
    }
}
