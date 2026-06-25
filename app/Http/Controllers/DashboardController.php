<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the hospital dashboard with real-time metrics.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        
        $kd_dokter = session('user')->kd_dokter ?? null;

        // 1. Total Pasien Hari Ini
        $totalPasienQuery = DB::table('reg_periksa')->where('tgl_registrasi', $today);
        $yesterdayTotalQuery = DB::table('reg_periksa')->where('tgl_registrasi', $yesterday);
        
        if ($kd_dokter) {
            $totalPasienQuery->where('kd_dokter', $kd_dokter);
            $yesterdayTotalQuery->where('kd_dokter', $kd_dokter);
        }
        
        $stats['total_pasien'] = $totalPasienQuery->count();
        $yesterday_total = $yesterdayTotalQuery->count();
        $stats['total_pasien_diff'] = $stats['total_pasien'] - $yesterday_total;

        // 2. Rawat Jalan Today
        $ralanQuery = DB::table('reg_periksa')
            ->where('tgl_registrasi', $today)
            ->where('status_lanjut', 'Ralan');
        $yesterdayRalanQuery = DB::table('reg_periksa')
            ->where('tgl_registrasi', $yesterday)
            ->where('status_lanjut', 'Ralan');
            
        if ($kd_dokter) {
            $ralanQuery->where('kd_dokter', $kd_dokter);
            $yesterdayRalanQuery->where('kd_dokter', $kd_dokter);
        }
        
        $stats['ralan'] = $ralanQuery->count();
        $yesterday_ralan = $yesterdayRalanQuery->count();
        $stats['ralan_diff'] = $stats['ralan'] - $yesterday_ralan;

        // 3. Rawat Inap (Active)
        $ranapQuery = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('kamar_inap.stts_pulang', '-');
            
        $ranapNewQuery = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('kamar_inap.tgl_masuk', $today);
            
        if ($kd_dokter) {
            $ranapQuery->where('reg_periksa.kd_dokter', $kd_dokter);
            $ranapNewQuery->where('reg_periksa.kd_dokter', $kd_dokter);
        }
        
        $stats['ranap'] = $ranapQuery->count();
        $stats['ranap_new'] = $ranapNewQuery->count();

        // 4. Bed Status (BOR) - Tetap Global
        $total_beds = DB::table('kamar')->where('statusdata', '1')->count();
        $occupied_beds = DB::table('kamar')->where('status', 'ISI')->count();
        $stats['available_beds'] = $total_beds - $occupied_beds;
        $stats['bor'] = $total_beds > 0 ? round(($occupied_beds / $total_beds) * 100, 1) : 0;

        // 5. Prescriptions Today
        $farmasiQuery = DB::table('resep_obat')->where('tgl_perawatan', $today);
        $yesterdayFarmasiQuery = DB::table('resep_obat')->where('tgl_perawatan', $yesterday);
        
        if ($kd_dokter) {
            $farmasiQuery->where('kd_dokter', $kd_dokter);
            $yesterdayFarmasiQuery->where('kd_dokter', $kd_dokter);
        }
        
        $stats['farmasi'] = $farmasiQuery->count();
        $yesterday_farmasi = $yesterdayFarmasiQuery->count();
        $stats['farmasi_diff'] = $stats['farmasi'] - $yesterday_farmasi;

        // 6. Recent Activities
        $activitiesQuery = DB::table(DB::raw('reg_periksa FORCE INDEX (reg_periksa_tgl_jam_index)'))
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli'
            );
            
        if ($kd_dokter) {
            $activitiesQuery->where('reg_periksa.kd_dokter', $kd_dokter);
        }
        
        $recent_activities = $activitiesQuery->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc')
            ->limit(8)
            ->get();

        // Ambil nama dokter dari database dokter secara terpisah
        $kd_dokters = $recent_activities->pluck('kd_dokter')->unique()->toArray();
        if (!empty($kd_dokters)) {
            $dokters = DB::connection('dokter')->table('dokter')
                ->whereIn('kd_dokter', $kd_dokters)
                ->pluck('nm_dokter', 'kd_dokter');
        } else {
            $dokters = collect();
        }

        foreach ($recent_activities as $activity) {
            $activity->nm_dokter = $dokters[$activity->kd_dokter] ?? '-';
        }

        return view('dashboard.index', compact('stats', 'recent_activities'));
    }
}
