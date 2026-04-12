<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    private function buildReport(Request $request): array
    {
        $start = $request->query('start_date', now()->toDateString());
        $end = $request->query('end_date', now()->toDateString());
        $query = Transaksi::with(['kendaraan', 'user'])->where('status', 'keluar')->whereBetween('waktu_keluar', [$start.' 00:00:00', $end.' 23:59:59']);
        $laporan = $query->latest('id_parkir')->get();
        $topupQuery = Topup::with(['member', 'verifiedBy'])
            ->where('status', 'success')
            ->whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59']);
        $topupLaporan = $topupQuery->latest('id_topup')->get();
        $pendapatanParkir = (int) $laporan->sum('biaya_total');
        $pendapatanTopup = (int) $topupLaporan->sum('nominal');

        return [
            'start' => $start,
            'end' => $end,
            'laporan' => $laporan,
            'topupLaporan' => $topupLaporan,
            'pendapatanParkir' => $pendapatanParkir,
            'pendapatanTopup' => $pendapatanTopup,
            'totalPendapatan' => $pendapatanParkir + $pendapatanTopup,
            'totalKendaraan' => (int) $laporan->count(),
        ];
    }

    public function index(Request $request)
    {
        return view('owner.laporan', $this->buildReport($request));
    }

    public function print(Request $request)
    {
        return view('owner.print_laporan', $this->buildReport($request));
    }
}
