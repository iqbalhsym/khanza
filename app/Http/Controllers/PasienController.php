<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasienController extends Controller
{
    /**
     * Daftar pasien (digunakan di /pendaftaran dan /pendaftaran/pasien-lama)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);

        $fields = [
            'no_rkm_medis', 'nm_pasien', 'no_ktp', 'jk', 'tmp_lahir', 'tgl_lahir',
            'nm_ibu', 'alamat', 'gol_darah', 'pekerjaan', 'stts_nikah', 'agama',
            'tgl_daftar', 'no_tlp', 'umur', 'pnd', 'keluarga', 'namakeluarga',
            'kd_pj', 'no_peserta', 'kd_kel', 'kd_kec', 'kd_kab', 'pekerjaanpj',
            'alamatpj', 'kelurahanpj', 'kecamatanpj', 'kabupatenpj',
            'perusahaan_pasien', 'suku_bangsa', 'bahasa_pasien', 'cacat_fisik',
            'email', 'nip', 'kd_prop', 'propinsipj', 'data_pendukung', 'special_precautions', 'special_precautions_color'
        ];

        $query = DB::table('pasien')
            ->select($fields)
            ->orderByRaw('CAST(no_rkm_medis AS UNSIGNED) DESC');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_rkm_medis', 'like', '%' . $search . '%')
                  ->orWhere('nm_pasien', 'like', '%' . $search . '%')
                  ->orWhere('no_ktp', 'like', '%' . $search . '%')
                  ->orWhere('alamat', 'like', '%' . $search . '%');
            });
        }

        $pasien = $query->paginate($perPage);

        // Tambahkan parameter ke link pagination
        if ($search) {
            $pasien->appends(['search' => $search]);
        }
        if ($perPage != 20) {
            $pasien->appends(['per_page' => $perPage]);
        }

        return view('pendaftaran.index', compact('pasien', 'search', 'perPage'));
    }

    /**
     * Form pendaftaran pasien baru — generate nomor RM berikutnya
     */
    public function create()
    {
        $nextRm = $this->generateNextRm();
        return view('pendaftaran.pasien_baru', compact('nextRm'));
    }

    /**
     * Simpan data pasien baru ke database
     */
    public function store(Request $request)
    {
        // Validasi field wajib
        $request->validate([
            'no_rkm_medis' => 'required|string|max:20|unique:pasien,no_rkm_medis',
            'nm_pasien'    => 'required|string|max:100',
            'jk'           => 'required|in:L,P',
            'tgl_lahir'    => 'required|date',
            'nm_ibu'       => 'required|string|max:100',
            'kd_pj'        => 'required|string',
            'data_pendukung' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png',
        ], [
            'no_rkm_medis.required' => 'Nomor Rekam Medis wajib diisi.',
            'no_rkm_medis.unique'   => 'Nomor Rekam Medis sudah terdaftar.',
            'nm_pasien.required'    => 'Nama Pasien wajib diisi.',
            'jk.required'           => 'Jenis Kelamin wajib dipilih.',
            'tgl_lahir.required'    => 'Tanggal Lahir wajib diisi.',
            'nm_ibu.required'       => 'Nama Ibu Kandung wajib diisi.',
            'kd_pj.required'        => 'Kode Jaminan wajib dipilih.',
            'data_pendukung.max'    => 'Ukuran dokumen data pendukung maksimal 2 MB.',
            'data_pendukung.mimes'  => 'Format dokumen harus berupa pdf, doc, docx, jpg, jpeg, atau png.',
        ]);

        try {
            $fileName = null;
            if ($request->hasFile('data_pendukung')) {
                $file = $request->file('data_pendukung');
                $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/data_pendukung'), $fileName);
            }

            // Kolom-kolom wajib dan varchar aman selalu dimasukkan
            $data = [
                'no_rkm_medis' => $request->no_rkm_medis,
                'nm_pasien'    => $request->nm_pasien,
                'no_ktp'       => $request->no_ktp    ?: null,
                'jk'           => $request->jk,
                'tmp_lahir'    => $request->tmp_lahir  ?: null,
                'tgl_lahir'    => $request->tgl_lahir,
                'nm_ibu'       => $request->nm_ibu,
                'alamat'       => $request->alamat     ?: null,
                'gol_darah'    => $request->gol_darah  ?: '-',      // ENUM: A,B,O,AB,-
                'pekerjaan'    => $request->pekerjaan  ?: null,
                'stts_nikah'   => $request->stts_nikah ?: null,     // ENUM: BELUM MENIKAH,...
                'agama'        => $request->agama      ?: null,
                'tgl_daftar'   => $request->tgl_daftar ?: now()->format('Y-m-d'),
                'no_tlp'       => $request->no_tlp     ?: null,
                'umur'         => $request->umur        ?: '',
                'pnd'          => $request->pnd         ?: '-',     // ENUM includes '-'
                'keluarga'     => $request->keluarga   ?: null,     // ENUM: AYAH,IBU,ISTRI,...
                'namakeluarga' => $request->namakeluarga ?: '',
                'kd_pj'        => $request->kd_pj,
                'no_peserta'   => $request->no_peserta  ?: null,
                'pekerjaanpj'  => $request->pekerjaanpj  ?: '',
                'alamatpj'     => $request->alamatpj     ?: '',
                'kelurahanpj'  => $request->kelurahanpj  ?: '',
                'kecamatanpj'  => $request->kecamatanpj  ?: '',
                'kabupatenpj'  => $request->kabupatenpj  ?: '',
                'propinsipj'   => $request->propinsipj   ?: '',
                'email'        => $request->email        ?: '',
                'nip'          => $request->nip          ?: '',
                'data_pendukung' => $fileName,
                'special_precautions' => $request->special_precautions ?: null,
                'special_precautions_color' => $request->special_precautions_color ?: null,
            ];

            // Kolom FK NOT NULL — wajib selalu dimasukkan dengan nilai valid dari tabel referensi
            // Nilai fallback adalah nilai terkecil/default yang valid di masing-masing tabel FK
            $data['kd_kel']        = $request->filled('kd_kel')        ? (int) $request->kd_kel        : 1;  // min valid: 1
            $data['kd_kec']        = $request->filled('kd_kec')        ? (int) $request->kd_kec        : 0;  // min valid: 0
            $data['kd_kab']        = $request->filled('kd_kab')        ? (int) $request->kd_kab        : 1;  // min valid: 1
            $data['kd_prop']       = $request->filled('kd_prop')       ? (int) $request->kd_prop       : 1;  // min valid: 1
            $data['suku_bangsa']   = $request->filled('suku_bangsa')   ? (int) $request->suku_bangsa   : 1;  // min valid: 1
            $data['bahasa_pasien'] = $request->filled('bahasa_pasien') ? (int) $request->bahasa_pasien : 1;  // min valid: 1
            $data['cacat_fisik']   = $request->filled('cacat_fisik')   ? (int) $request->cacat_fisik   : 1;  // min valid: 1

            // Pastikan kd_pj ada di tabel penjab (FK)
            if ($request->filled('kd_pj')) {
                $existsPj = DB::table('penjab')->where('kd_pj', $request->kd_pj)->exists();
                $data['kd_pj'] = $existsPj ? $request->kd_pj : 'BPJ'; // fallback ke BPJ jika salah
            } else {
                $data['kd_pj'] = 'BPJ';
            }

            // Pastikan perusahaan_pasien ada di tabel perusahaan_pasien (FK)
            if ($request->filled('perusahaan_pasien')) {
                $existsCorp = DB::table('perusahaan_pasien')->where('kode_perusahaan', $request->perusahaan_pasien)->exists();
                $data['perusahaan_pasien'] = $existsCorp ? $request->perusahaan_pasien : '-'; // fallback ke '-' jika salah/rsui
            } else {
                $data['perusahaan_pasien'] = '-';
            }

            DB::table('pasien')->insert($data);


            return redirect('/pendaftaran')
                ->with('success', 'Pasien ' . $request->nm_pasien . ' berhasil didaftarkan dengan No. RM: ' . $request->no_rkm_medis);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mendaftarkan pasien: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor RM berikutnya berdasarkan data di database.
     * Format: XXXXXX (6 digit angka, e.g. 000001, 000002, ...)
     */
    private function generateNextRm(): string
    {
        $last = DB::table('pasien')
            ->whereRaw("no_rkm_medis REGEXP '^[0-9]+$'")
            ->orderByRaw('CAST(no_rkm_medis AS UNSIGNED) DESC')
            ->value('no_rkm_medis');

        if ($last) {
            $next = (int) $last + 1;
        } else {
            $next = 1;
        }

        return str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Tampilkan form edit pasien
     */
    public function edit($id)
    {
        $pasien = DB::table('pasien')->where('no_rkm_medis', $id)->first();
        if (!$pasien) {
            return redirect('/pendaftaran')->with('error', 'Pasien tidak ditemukan.');
        }

        return view('pendaftaran.pasien_edit', compact('pasien'));
    }

    /**
     * Update data pasien
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nm_pasien'    => 'required|string|max:100',
            'jk'           => 'required|in:L,P',
            'tgl_lahir'    => 'required|date',
            'nm_ibu'       => 'required|string|max:100',
            'kd_pj'        => 'required|string',
            'data_pendukung' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png',
        ], [
            'data_pendukung.max'   => 'Ukuran dokumen data pendukung maksimal 2 MB.',
            'data_pendukung.mimes' => 'Format dokumen harus berupa pdf, doc, docx, jpg, jpeg, atau png.',
        ]);

        try {
            $data = [
                'nm_pasien'    => $request->nm_pasien,
                'no_ktp'       => $request->no_ktp    ?: null,
                'jk'           => $request->jk,
                'tmp_lahir'    => $request->tmp_lahir  ?: null,
                'tgl_lahir'    => $request->tgl_lahir,
                'nm_ibu'       => $request->nm_ibu,
                'alamat'       => $request->alamat     ?: null,
                'gol_darah'    => $request->gol_darah  ?: '-',
                'pekerjaan'    => $request->pekerjaan  ?: null,
                'stts_nikah'   => $request->stts_nikah ?: null,
                'agama'        => $request->agama      ?: null,
                'no_tlp'       => $request->no_tlp     ?: null,
                'umur'         => $request->umur        ?: '',
                'pnd'          => $request->pnd         ?: '-',
                'keluarga'     => $request->keluarga   ?: null,
                'namakeluarga' => $request->namakeluarga ?: '',
                'no_peserta'   => $request->no_peserta  ?: null,
                'pekerjaanpj'  => $request->pekerjaanpj  ?: '',
                'alamatpj'     => $request->alamatpj     ?: '',
                'kelurahanpj'  => $request->kelurahanpj  ?: '',
                'kecamatanpj'  => $request->kecamatanpj  ?: '',
                'kabupatenpj'  => $request->kabupatenpj  ?: '',
                'propinsipj'   => $request->propinsipj   ?: '',
                'email'        => $request->email        ?: '',
                'nip'          => $request->nip          ?: '',
                'special_precautions' => $request->special_precautions ?: null,
                'special_precautions_color' => $request->special_precautions_color ?: null,
            ];

            if ($request->hasFile('data_pendukung')) {
                // Delete old file if exists
                $oldFile = DB::table('pasien')->where('no_rkm_medis', $id)->value('data_pendukung');
                if ($oldFile && file_exists(public_path('uploads/data_pendukung/' . $oldFile))) {
                    @unlink(public_path('uploads/data_pendukung/' . $oldFile));
                }

                $file = $request->file('data_pendukung');
                $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/data_pendukung'), $fileName);
                $data['data_pendukung'] = $fileName;
            }

            if ($request->filled('kd_pj')) {
                $existsPj = DB::table('penjab')->where('kd_pj', $request->kd_pj)->exists();
                $data['kd_pj'] = $existsPj ? $request->kd_pj : 'BPJ';
            } else {
                $data['kd_pj'] = 'BPJ';
            }

            DB::table('pasien')->where('no_rkm_medis', $id)->update($data);

            return redirect('/pendaftaran')
                ->with('success', 'Data Pasien ' . $request->nm_pasien . ' berhasil diupdate.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate pasien: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data pasien
     */
    public function destroy($id)
    {
        try {
            DB::table('pasien')->where('no_rkm_medis', $id)->delete();
            return redirect('/pendaftaran')->with('success', 'Data Pasien berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect('/pendaftaran')->with('error', 'Gagal menghapus pasien: Mungkin data sedang digunakan. (' . $e->getMessage() . ')');
        }
    }
}
