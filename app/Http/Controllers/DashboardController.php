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
            $totalPasienQuery->where(function($q) use ($kd_dokter) {
                $q->where('kd_dokter', $kd_dokter)
                  ->orWhereIn('no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
            $yesterdayTotalQuery->where(function($q) use ($kd_dokter) {
                $q->where('kd_dokter', $kd_dokter)
                  ->orWhereIn('no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
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
            $ralanQuery->where(function($q) use ($kd_dokter) {
                $q->where('kd_dokter', $kd_dokter)
                  ->orWhereIn('no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
            $yesterdayRalanQuery->where(function($q) use ($kd_dokter) {
                $q->where('kd_dokter', $kd_dokter)
                  ->orWhereIn('no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
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
            $ranapQuery->where(function($q) use ($kd_dokter) {
                $q->where('reg_periksa.kd_dokter', $kd_dokter)
                  ->orWhereIn('reg_periksa.no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
            $ranapNewQuery->where(function($q) use ($kd_dokter) {
                $q->where('reg_periksa.kd_dokter', $kd_dokter)
                  ->orWhereIn('reg_periksa.no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
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
            $farmasiQuery->where(function($q) use ($kd_dokter) {
                $q->where('kd_dokter', $kd_dokter)
                  ->orWhereIn('no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
            $yesterdayFarmasiQuery->where(function($q) use ($kd_dokter) {
                $q->where('kd_dokter', $kd_dokter)
                  ->orWhereIn('no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
        }
        
        $stats['farmasi'] = $farmasiQuery->count();
        $yesterday_farmasi = $yesterdayFarmasiQuery->count();
        $stats['farmasi_diff'] = $stats['farmasi'] - $yesterday_farmasi;

        // 6. Emergency (IGD) - Mock/Placeholder
        $stats['emergency'] = 0;
        $stats['emergency_diff'] = 0;

        // 7. Konsul - Mock/Placeholder
        $stats['konsul'] = 0;
        $stats['konsul_diff'] = 0;

        // 8. Recent Activities
        $activitiesQuery = DB::table('reg_periksa')
            ->select(
                'no_rawat',
                'tgl_registrasi',
                'jam_reg',
                'stts',
                'kd_dokter',
                'no_rkm_medis',
                'kd_poli'
            );
            
        if ($kd_dokter) {
            $activitiesQuery->where(function($q) use ($kd_dokter) {
                $q->where('kd_dokter', $kd_dokter)
                  ->orWhereIn('no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')->from('reg_dpjp_tambahan')->where('kd_dokter', $kd_dokter);
                  });
            });
        }
        
        $recent_activities = $activitiesQuery->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')
            ->limit(8)
            ->get();

        // Resolve patient names and clinic names separately using whereIn to avoid heavy cross-joins
        $no_rkm_medis = $recent_activities->pluck('no_rkm_medis')->unique()->toArray();
        $kd_polis = $recent_activities->pluck('kd_poli')->unique()->toArray();

        $pasien = !empty($no_rkm_medis) 
            ? DB::table('pasien')->whereIn('no_rkm_medis', $no_rkm_medis)->pluck('nm_pasien', 'no_rkm_medis')
            : collect();

        $poli = !empty($kd_polis)
            ? DB::table('poliklinik')->whereIn('kd_poli', $kd_polis)->pluck('nm_poli', 'kd_poli')
            : collect();

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
            $activity->nm_pasien = $pasien[$activity->no_rkm_medis] ?? '-';
            $activity->nm_poli = $poli[$activity->kd_poli] ?? '-';
            $activity->nm_dokter = $dokters[$activity->kd_dokter] ?? '-';
        }

        return view('dashboard.index', compact('stats', 'recent_activities'));
    }
}
