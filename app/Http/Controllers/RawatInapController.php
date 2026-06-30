<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RawatInapController extends Controller
{
    /**
     * Display the list of active inpatient patients.
     */
    public function index(Request $request)
    {
        $kd_dokter = session('user')->kd_dokter ?? null;

        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('kamar_inap.stts_pulang', '-')
            ->select(
                'kamar_inap.*',
                'pasien.nm_pasien',
                'pasien.no_rkm_medis',
                'kamar.kd_kamar',
                'kamar.kelas',
                'bangsal.nm_bangsal',
                'reg_periksa.kd_dokter',
                'reg_periksa.p_jawab',
                'reg_periksa.hubunganpj'
            );

        if ($kd_dokter) {
            $query->where(function($q) use ($kd_dokter) {
                $q->where('reg_periksa.kd_dokter', $kd_dokter)
                  ->orWhereIn('reg_periksa.no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pasien.nm_pasien', 'like', "%{$search}%")
                  ->orWhere('pasien.no_rkm_medis', 'like', "%{$search}%")
                  ->orWhere('bangsal.nm_bangsal', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('kamar_inap.tgl_masuk', 'desc')
            ->orderBy('kamar_inap.jam_masuk', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Ambil nama dokter dari database dokter secara terpisah
        $kd_dokters = collect($data->items())->pluck('kd_dokter')->unique()->toArray();
        if (!empty($kd_dokters)) {
            $dokters = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $kd_dokters)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $dokters = collect();
        }

        foreach ($data->items() as $item) {
            $item->dpjp = $dokters[$item->kd_dokter] ?? '-';
        }

        return view('rawat_inap.index', compact('data'));
    }

    /**
     * Display the room occupancy map.
     */
    public function kamar(Request $request)
    {
        $wards = DB::table('bangsal')
            ->where('status', '1')
            ->orderBy('nm_bangsal', 'asc')
            ->get();

        $beds = DB::table('kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('kamar.statusdata', '1')
            ->select('kamar.*', 'bangsal.nm_bangsal')
            ->orderBy('kamar.kd_kamar', 'asc')
            ->get();

        return view('rawat_inap.kamar', compact('wards', 'beds'));
    }
}
