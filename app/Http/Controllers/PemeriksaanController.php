<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Session;

class PemeriksaanController extends Controller
{
    public function create($no_rawat)
    {
        // Decode no_rawat if it contains slashes (e.g. 2026/03/26/000001)
        $no_rawat = urldecode($no_rawat);

        $reg = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->select(
                'reg_periksa.*', 
                'pasien.nm_pasien', 'pasien.tgl_lahir', 'pasien.jk',
                'poliklinik.nm_poli'
            )
            ->first();

        if ($reg) {
            // Get doctor name from connection 'dokter'
            if (!empty($reg->kd_dokter)) {
                $doc = DB::connection('dokter')->table('dokter')->where('kd_dokter', $reg->kd_dokter)->first();
                $reg->nm_dokter = $doc->nm_dokter ?? '-';
            } else {
                $reg->nm_dokter = '-';
            }
        }

        if (!$reg) {
            return redirect('/rawat-jalan')->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        // Cek apakah sudah ada pemeriksaan sebelumnya
        $pemeriksaan = DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $no_rawat)
            ->first();

        return view('rawat_jalan.pemeriksaan', compact('reg', 'pemeriksaan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'keluhan'  => 'required',
            'pemeriksaan' => 'required',
            'penilaian' => 'required',
            'rtl' => 'required',
        ]);

        try {
            // Get the registration record to find the assigned doctor (kd_dokter)
            // Clinical records in Khanza must use a valid NIK from 'pegawai' table. 
            // We use the doctor assigned to the registration to ensure integrity.
            $reg = DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->first();
            
            if (!$reg) {
                return redirect()->back()->with('error', 'Gagal: Data registrasi tidak ditemukan.');
            }

            $nip = $reg->kd_dokter;
            
            // Verify if NIP exists in pegawai table to avoid FK violation
            $pegawaiExists = DB::table('pegawai')->where('nik', $nip)->exists();
            if (!$pegawaiExists) {
                // Fallback to '-' if the doctor code is not in pegawai table (edge case)
                $nip = '-';
            }

            $tgl_perawatan = $request->tgl_perawatan ?: date('Y-m-d');
            $jam_rawat = $request->jam_rawat ?: date('H:i:s');

            $data = [
                'no_rawat'      => $request->no_rawat,
                'tgl_perawatan' => $tgl_perawatan,
                'jam_rawat'     => $jam_rawat,
                'suhu_tubuh'    => $request->suhu_tubuh ?: '-',
                'tensi'         => $request->tensi ?: '-',
                'nadi'          => $request->nadi ?: '-',
                'respirasi'     => $request->respirasi ?: '-',
                'tinggi'        => $request->tinggi ?: '-',
                'berat'         => $request->berat ?: '-',
                'spo2'          => $request->spo2 ?: '-',
                'gcs'           => $request->gcs ?: '-',
                'kesadaran'     => $request->kesadaran ?: 'Compos Mentis',
                'keluhan'       => $request->keluhan,
                'pemeriksaan'   => $request->pemeriksaan,
                'alergi'        => $request->alergi ?: '-',
                'lingkar_perut' => $request->lingkar_perut ?: '-',
                'rtl'           => $request->rtl,
                'penilaian'     => $request->penilaian,
                'instruksi'     => $request->instruksi ?: '-',
                'evaluasi'      => $request->evaluasi ?: '-',
                'nip'           => $nip
            ];

            // If both tgl_perawatan and jam_rawat are passed, update that specific record
            if ($request->filled('tgl_perawatan') && $request->filled('jam_rawat')) {
                DB::table('pemeriksaan_ralan')
                    ->where('no_rawat', $request->no_rawat)
                    ->where('tgl_perawatan', $request->tgl_perawatan)
                    ->where('jam_rawat', $request->jam_rawat)
                    ->update($data);
            } else {
                DB::table('pemeriksaan_ralan')->insert($data);
            }

            // Update status di reg_periksa menjadi 'Sudah' (Sudah Diperiksa)
            DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->update(['stts' => 'Sudah']);

            return redirect('/rawat-jalan/registered/' . urlencode($request->no_rawat))->with('success', 'Pemeriksaan SOAP berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan pemeriksaan: ' . $e->getMessage());
        }
    }
}
