@extends('layout.app')
@section('title', 'Detail – ' . $data->nm_pasien)
@section('page-title', 'Clinical Dashboard')
@section('breadcrumb', $data->nm_pasien)

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible py-1 px-3 mb-2" role="alert" style="font-size:12px;">
  {{ session('success') }}<a class="btn-close" data-bs-dismiss="alert"></a>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible py-1 px-3 mb-2" role="alert" style="font-size:12px;">
  {{ session('error') }}<a class="btn-close" data-bs-dismiss="alert"></a>
</div>
@endif

{{-- ============ COMPACT IDENTITY BANNER ============ --}}
<div class="d-flex align-items-center gap-3 px-3 py-2 mb-2 rounded-2 flex-wrap"
     style="background:linear-gradient(135deg,#1a5ba8,#2d7dd2);color:#fff;font-size:12px;">
  <div class="avatar avatar-sm" style="background:rgba(255,255,255,.22);color:#fff;font-weight:700;flex-shrink:0;">
    {{ strtoupper(substr($data->nm_pasien,0,1)) }}
  </div>
  <div>
    <div style="font-size:14px;font-weight:700;line-height:1.2;">{{ $data->nm_pasien }}</div>
    <div style="opacity:.8;">{{ $data->no_rkm_medis }} &bull; {{ $data->jk }} &bull; {{ $data->umur }}&nbsp;thn &bull; {{ $data->agama??'-' }}</div>
  </div>
  <div class="d-flex gap-3 flex-wrap ms-2" style="opacity:.92;">
    <span>📅 <strong>{{ $data->tgl_registrasi }}</strong></span>
    <span>📍 <strong>{{ $data->nm_poli }}</strong></span>
    <span>🩺 <strong>{{ $data->dpjp_1 }}</strong></span>
    <span>📞 <strong>{{ $data->no_tlp??'-' }}</strong></span>
    <span>🏠 <strong>{{ Str::limit($data->alamat,35) }}</strong></span>
  </div>
  <div class="ms-auto d-flex gap-2 align-items-center">
    {{-- PIC & DPJP Dropdown --}}
    <div class="dropdown">
      <button class="btn btn-sm d-flex align-items-center gap-1 dropdown-toggle" type="button" id="dropdownPIC" data-bs-toggle="dropdown" aria-expanded="false" 
              style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4);font-size:11px;padding: 4px 8px;">
        📋 PIC &amp; DPJP Info
      </button>
      <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0" aria-labelledby="dropdownPIC" style="width:340px; border-radius: 8px; font-size:12px; color:#1e293b; z-index: 1050;">
        <div class="fw-bold text-uppercase mb-2 pb-1 border-bottom text-primary" style="font-size:10px; letter-spacing:0.5px;">
          👥 Person In Charge (PIC)
        </div>
        <div class="mb-3">
          @foreach([
            ['Treatment Type', $data->p_jawab??'-'],
            ['Registered Date', $data->tgl_registrasi.' '.$data->jam_reg],
            ['Episode Location', $data->nm_poli],
            ['Doctor In Charge 1', $data->dpjp_1],
          ] as [$label,$val])
          <div class="d-flex justify-content-between mb-1" style="font-size:11px;">
            <span class="text-muted">{{ $label }}:</span>
            <span class="fw-semibold text-end">{{ $val }}</span>
          </div>
          @endforeach
          <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
            <span class="text-muted fw-medium" style="font-size:11px;">Total Invoice:</span>
            <span class="fw-bold text-danger" style="font-size:14px;">Rp {{ number_format($data->total_invoice,0,',','.') }}</span>
          </div>
        </div>

        <div class="fw-bold text-uppercase d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom text-primary" style="font-size:10px; letter-spacing:0.5px;">
          <span>🔬 Research Info &amp; DPJP</span>
          <button onclick="showModal('modal-dpjp')" class="btn btn-xs btn-outline-primary py-0 px-1" style="font-size:9px; border-radius:3px;">+ Kelola</button>
        </div>
        <div style="font-size:11px;">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Jaminan:</span>
            <span class="badge {{ $data->jaminan=='BPJS'?'bg-teal-lt':'bg-secondary-lt' }}">{{ $data->jaminan }}</span>
          </div>
          <div class="d-flex align-items-center justify-content-between py-1 border-bottom">
            <span class="badge bg-blue-lt" style="font-size:9px;">DPJP 1</span>
            <span class="fw-medium text-end">{{ $data->dpjp_1 }}</span>
          </div>
          @foreach($dpjp_tambahan as $urutan => $dpjp)
          <div class="d-flex align-items-center justify-content-between py-1 border-bottom">
            <span class="badge bg-secondary-lt" style="font-size:9px;">DPJP {{ $urutan }}</span>
            <span class="text-end">{{ $dpjp->nm_dokter }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <span class="badge" style="background:{{ $data->jaminan=='BPJS'?'#0ca678':'rgba(255,255,255,.25)' }};color:#fff;">💳 {{ $data->jaminan }}</span>
    <a href="{{ url('/rawat-jalan') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);font-size:11px;">← Kembali</a>
  </div>
</div>

{{-- ============ MAIN 3-COLUMN GRID (fills remaining viewport) ============ --}}
<div class="d-flex gap-2" style="height:calc(100vh - 135px);overflow:hidden;">

  {{-- ======================================================
       COLUMN 1 – CPPT & SOAP (40%)
       ====================================================== --}}
  <div style="flex:0 0 40%;min-width:0;display:flex;flex-direction:column;gap:0;">

    {{-- Main Clinical Tab Nav --}}
    <div style="flex-shrink:0;background:#fff;border-bottom:2px solid #e2e8f0;padding-top:2px;">
      <ul class="nav px-2" id="mainTabNav" style="display:flex;gap:4px;list-style:none;margin:0;padding-bottom:0;">
        <li><a class="nav-tab-link active" href="#" onclick="switchTab('main','tab-cppt',this);return false;" style="display:inline-block;padding:6px 14px;font-size:12px;font-weight:600;color:#206bc4;border:1px solid #c8d8f0;border-bottom:2px solid #fff;background:#fff;border-radius:6px 6px 0 0;text-decoration:none;margin-bottom:-2px;">📋 CPPT</a></li>
        <li><a class="nav-tab-link" href="#" onclick="switchTab('main','tab-assessment',this);return false;" style="display:inline-block;padding:6px 14px;font-size:12px;font-weight:500;color:#64748b;border:1px solid transparent;border-bottom:none;background:transparent;border-radius:6px 6px 0 0;text-decoration:none;margin-bottom:-2px;">🩺 Assessment</a></li>
        <li><a class="nav-tab-link" href="#" onclick="switchTab('main','tab-forms',this);return false;" style="display:inline-block;padding:6px 14px;font-size:12px;font-weight:500;color:#64748b;border:1px solid transparent;border-bottom:none;background:transparent;border-radius:6px 6px 0 0;text-decoration:none;margin-bottom:-2px;">📎 Forms</a></li>
      </ul>
    </div>

    {{-- Main Clinical Tab Content (fills remaining height) --}}
    <div id="main-tab-content" style="flex:1;min-height:0;overflow:hidden;display:flex;flex-direction:column;margin-top:8px;">

      {{-- CPPT TAB --}}
      <div class="ctab-panel" id="tab-cppt" style="flex:1;display:flex;flex-direction:column;gap:8px;overflow:hidden;">

        {{-- CPPT Sub-tab nav --}}
        <div class="card card-sm p-0" style="flex-shrink:0;background:#f8fafc;">
          <ul class="nav nav-pills px-2 py-1 gap-1" id="subTabNav">
            <li class="nav-item"><a class="nav-link active py-0 px-2" style="font-size:11px;" href="#" onclick="switchTab('sub','sub-review',this);return false;">Review &amp; Entry</a></li>
            <li class="nav-item"><a class="nav-link py-0 px-2" style="font-size:11px;" href="#" onclick="switchTab('sub','sub-edit',this);return false;">Entry &amp; Edit</a></li>
            <li class="nav-item"><a class="nav-link py-0 px-2" style="font-size:11px;" href="#" onclick="switchTab('sub','sub-student',this);return false;">Student Notes</a></li>
          </ul>
        </div>

        <div id="sub-tab-content" style="flex:1;min-height:0;display:flex;flex-direction:column;overflow:hidden;">

          {{-- Review & Entry --}}
          <div class="ctab-panel stab-panel" id="sub-review" style="flex:1;display:flex;flex-direction:column;gap:8px;overflow:hidden;">

            {{-- SOAP Form (compact) --}}
            <div class="card card-sm" style="flex-shrink:0;border-top:2px solid #206bc4 !important;">
              <div class="card-header py-1 px-2" style="background:#f0f7ff;">
                <span class="fw-bold text-uppercase" style="font-size:10px;color:#1a5ba8;letter-spacing:.5px;">📝 New SOAP Entry</span>
              </div>
              <div class="card-body px-2 pt-2 pb-1">
                <form action="{{ url('/rawat-jalan/pemeriksaan/store') }}" method="POST">
                  @csrf
                  <input type="hidden" name="no_rawat" value="{{ $data->no_rawat }}">
                  <div class="row g-1 mb-1">
                    <div class="col-12">
                      <label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Subjective (Keluhan)</label>
                      <textarea name="keluhan" rows="2" class="form-control form-control-sm" placeholder="Keluhan utama..." required></textarea>
                    </div>
                  </div>
                  <div class="row g-1 mb-1">
                    <div class="col-4"><label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Tensi</label><input type="text" name="tensi" class="form-control form-control-sm" placeholder="mmHg"></div>
                    <div class="col-4"><label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Nadi</label><input type="text" name="nadi" class="form-control form-control-sm" placeholder="x/mnt"></div>
                    <div class="col-4"><label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Suhu</label><input type="text" name="suhu_tubuh" class="form-control form-control-sm" placeholder="°C"></div>
                  </div>
                  <div class="row g-1 mb-1">
                    <div class="col-12">
                      <label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Objective</label>
                      <textarea name="pemeriksaan" rows="2" class="form-control form-control-sm" placeholder="Pemeriksaan fisik..." required></textarea>
                    </div>
                  </div>
                  <div class="row g-1 mb-1">
                    <div class="col-6">
                      <label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Assessment</label>
                      <textarea name="penilaian" rows="2" class="form-control form-control-sm" required></textarea>
                    </div>
                    <div class="col-6">
                      <label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Plan / RTL</label>
                      <textarea name="rtl" rows="2" class="form-control form-control-sm" required></textarea>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <div class="flex-fill">
                      <label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">ICD-10</label>
                      <input type="text" name="kd_penyakit" class="form-control form-control-sm" placeholder="Cari kode diagnosa...">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mt-3" style="white-space:nowrap;">💾 Simpan</button>
                  </div>
                </form>
              </div>
            </div>

            {{-- SOAP History (fills leftover height) --}}
            <div class="card card-sm" style="flex:1;min-height:0;display:flex;flex-direction:column;">
              <div class="card-header py-1 px-2 d-flex justify-content-between align-items-center" style="background:#f8fafc;flex-shrink:0;">
                <span class="fw-bold text-uppercase" style="font-size:10px;color:#64748b;letter-spacing:.5px;">📋 Riwayat SOAP</span>
                <span class="badge bg-secondary-lt" style="font-size:10px;">{{ $soap_history->count() }} records</span>
              </div>
              <div style="flex:1;overflow-y:auto;">
                @forelse($soap_history as $soap)
                <div class="px-2 py-1 border-bottom" style="font-size:11px;">
                  <div class="d-flex justify-content-between">
                    <span class="fw-bold text-primary" style="font-size:11px;">{{ $soap->tgl_registrasi }}</span>
                    <span class="text-muted" style="font-size:10px;">{{ $soap->examiner }}</span>
                  </div>
                  <div style="color:#334155;line-height:1.4;">
                    <b>S:</b> {{ Str::limit($soap->keluhan,70)??'-' }} &nbsp;
                    <b>O:</b> {{ Str::limit($soap->pemeriksaan,60)??'-' }}<br>
                    <b>A:</b> {{ Str::limit($soap->penilaian,50)??'-' }} &nbsp;
                    <b>P:</b> {{ Str::limit($soap->rtl,50)??'-' }}
                  </div>
                </div>
                @empty
                <div class="p-3 text-center text-muted" style="font-size:12px;">Belum ada riwayat SOAP.</div>
                @endforelse
              </div>
            </div>

          </div>{{-- end sub-review --}}

          <div class="ctab-panel stab-panel" id="sub-edit" style="display:none;">
            <div class="p-3 text-center text-muted" style="font-size:12px;">Entry &amp; Edit – Pilih entri dari riwayat SOAP untuk diedit.</div>
          </div>
          <div class="ctab-panel stab-panel" id="sub-student" style="display:none;">
            <div class="p-3 text-center text-muted" style="font-size:12px;">Student Notes – Catatan mahasiswa klinik.</div>
          </div>

        </div>{{-- end cppt sub tab-content --}}
      </div>{{-- end #tab-cppt --}}

      <div class="ctab-panel" id="tab-assessment" style="display:none;flex:1;overflow-y:auto;">
        <div class="p-4 text-center text-muted" style="font-size:13px;">Initial Assessment – Formulir asesmen awal pasien.</div>
      </div>
      <div class="ctab-panel" id="tab-forms" style="display:none;flex:1;overflow-y:auto;">
        <div class="p-4 text-center text-muted" style="font-size:13px;">Forms Attachment – Upload dan kelola dokumen klinis.</div>
      </div>

    </div>{{-- end main tab-content --}}
  </div>{{-- end col 2 --}}

  {{-- ======================================================
       COLUMN 2 – Clinical Details & Actions (60%)
       ====================================================== --}}
  <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:8px;overflow:hidden;">

    {{-- Quick Actions --}}
    <div class="card card-sm" style="flex-shrink:0;">
      <div class="card-body p-2 d-flex flex-column gap-1">
        <div class="d-flex gap-1">
          <a href="{{ url('/rawat-jalan/pemeriksaan/'.urlencode($data->no_rawat)) }}" class="btn btn-primary btn-sm flex-fill" style="font-size:11px;">SOAP</a>
          <a href="{{ url('/rawat-jalan/resep/'.urlencode($data->no_rawat)) }}" class="btn btn-success btn-sm flex-fill" style="font-size:11px;">Resep</a>
          <a href="{{ url('/laboratorium/request/'.urlencode($data->no_rawat)) }}" class="btn btn-info btn-sm flex-fill" style="font-size:11px;">Lab</a>
        </div>
        <div class="d-flex gap-1">
          <a href="{{ url('/radiologi/request/'.urlencode($data->no_rawat)) }}" class="btn btn-warning btn-sm flex-fill" style="font-size:11px;">Radiologi</a>
          <a href="{{ url('/billing/'.urlencode($data->no_rawat)) }}" class="btn btn-danger btn-sm flex-fill" style="font-size:11px;">Billing</a>
        </div>
      </div>
    </div>

    {{-- Invoice --}}
    <div class="card card-sm" style="flex-shrink:0;background:#1e293b;color:#fff;">
      <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
        <div>
          <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Total Invoice</div>
          <div style="font-size:16px;font-weight:800;color:#f87171;">Rp {{ number_format($data->total_invoice,0,',','.') }}</div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.25)" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </div>
    </div>

    {{-- Lab Results --}}
    <div class="card card-sm" style="flex:1;min-height:0;display:flex;flex-direction:column;border-color:#fca5a5 !important;">
      <div class="card-header py-1 px-2 d-flex justify-content-between align-items-center" style="background:#fef2f2;border-color:#fca5a5;flex-shrink:0;">
        <span class="fw-bold text-uppercase" style="font-size:10px;color:#dc2626;letter-spacing:.5px;">🔬 Laboratory</span>
        <span class="badge bg-danger-lt" style="font-size:10px;">{{ $lab_pending->count() + $lab_results->count() }}</span>
      </div>
      <div style="flex:1;overflow-y:auto;">
        {{-- Pending Orders --}}
        @if($lab_pending->count() > 0)
        <div style="background:#fff7ed;border-bottom:1px solid #fed7aa;padding:3px 8px;">
          <span style="font-size:10px;font-weight:700;color:#c2410c;text-transform:uppercase;letter-spacing:.4px;">⏳ Menunggu Input</span>
        </div>
        @foreach($lab_pending as $pending)
        <div class="d-flex align-items-start gap-1 px-2 py-1 border-bottom" style="font-size:11px;background:#fffbf7;">
          <div class="flex-fill">
            <div class="fw-medium" style="color:#374151;">{{ $pending->test_names ?: '-' }}</div>
            <div class="text-muted" style="font-size:10px;">{{ $pending->noorder }} · {{ $pending->tgl_permintaan }}</div>
          </div>
          <a href="{{ url('/laboratorium/input/'.$pending->noorder) }}"
             class="btn py-0 px-1 ms-1" style="font-size:10px;white-space:nowrap;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;">Input</a>
        </div>
        @endforeach
        @endif
        {{-- Completed Results --}}
        @if($lab_results->count() > 0)
        <div style="background:#fef2f2;border-bottom:1px solid #fecaca;padding:3px 8px;">
          <span style="font-size:10px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.4px;">✅ Hasil</span>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:11px;">
          <tbody>
            @foreach($lab_results as $lab)
            <tr>
              <td style="padding:4px 8px;border-bottom:1px solid #f1f5f9;color:#64748b;white-space:nowrap;">{{ $lab->tgl_periksa }}</td>
              <td style="padding:4px 8px;border-bottom:1px solid #f1f5f9;font-weight:500;">{{ $lab->item_name }}</td>
              <td style="padding:4px 8px;border-bottom:1px solid #f1f5f9;">
                <a href="{{ url('/laboratorium/view-hasil/'.urlencode($lab->no_rawat).'/'.urlencode($lab->tgl_periksa).'/'.urlencode($lab->jam)) }}"
                   style="font-size:10px;color:#2563eb;">Lihat</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
        @if($lab_pending->count() === 0 && $lab_results->count() === 0)
        <div style="padding:16px;text-align:center;color:#94a3b8;font-size:11px;font-style:italic;">Belum ada permintaan atau hasil lab.</div>
        @endif
      </div>
    </div>

    {{-- Radiology Results --}}
    <div class="card card-sm" style="flex:1;min-height:0;display:flex;flex-direction:column;border-color:#86efac !important;">
      <div class="card-header py-1 px-2 d-flex justify-content-between align-items-center" style="background:#f0fdf4;border-color:#86efac;flex-shrink:0;">
        <span class="fw-bold text-uppercase" style="font-size:10px;color:#15803d;letter-spacing:.5px;">📡 Radiology</span>
        <span class="badge bg-success-lt" style="font-size:10px;">{{ $rad_results->count() }}</span>
      </div>
      <div style="flex:1;overflow-y:auto;">
        @forelse($rad_results as $rad)
        <div class="px-2 py-1.5 border-bottom d-flex align-items-center" style="font-size:11px;">
          <div style="flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
            <span class="text-muted me-2" style="font-size:10px;">{{ $rad->tgl_periksa }}</span>
            <span class="fw-semibold text-dark">{{ $rad->item_name }}</span>
            <span class="text-muted ms-2" style="font-size:10px;">({{ $rad->examiner }})</span>
          </div>
        </div>
        @empty
        <div class="p-3 text-center text-muted fst-italic" style="font-size:11px;">Belum ada data radiologi.</div>
        @endforelse
      </div>
    </div>

    {{-- Allergies --}}
    <div class="card card-sm" style="flex-shrink:0;border-color:#fca5a5 !important;">
      <div class="card-header py-1 px-2 d-flex justify-content-between align-items-center" style="background:#fef2f2;border-color:#fca5a5;">
        <span class="fw-bold text-uppercase" style="font-size:10px;color:#dc2626;letter-spacing:.5px;">⚠ Allergies</span>
        <button onclick="showModal('modal-allergy')" class="btn btn-xs py-0 px-1" style="font-size:10px;color:#dc2626;border:1px solid #fca5a5;background:#fff5f5;">+ New</button>
      </div>
      <div style="max-height:110px;overflow-y:auto;">
        @forelse($allergies as $alg)
        <div class="d-flex align-items-center gap-1 px-2 py-1 border-bottom" style="font-size:11px;">
          <span class="fw-medium flex-fill">{{ $alg->allergies }}</span>
          <span class="text-muted" style="font-size:10px;">{{ $alg->type_reaction }}</span>
          <span class="badge bg-{{ $alg->severity==='Severe'?'danger':($alg->severity==='Moderate'?'warning':'success') }}-lt ms-1" style="font-size:9px;">{{ $alg->severity }}</span>
          <form action="{{ url('/rawat-jalan/registered/delete-allergy/'.$alg->id) }}" method="POST" class="ms-1" onsubmit="return confirm('Hapus?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm p-0" style="color:#ef4444;font-size:13px;line-height:1;background:none;border:none;">×</button>
          </form>
        </div>
        @empty
        <div class="p-2 text-muted fst-italic" style="font-size:11px;">Tidak ada alergi tercatat.</div>
        @endforelse
      </div>
    </div>

    {{-- Patient Identification --}}
    <div class="card card-sm" style="flex:1;min-height:0;display:flex;flex-direction:column;">
      <div class="card-header py-1 px-2 d-flex justify-content-between align-items-center" style="background:#f8fafc;flex-shrink:0;">
        <span class="fw-bold text-uppercase" style="font-size:10px;color:#64748b;letter-spacing:.5px;">Identification</span>
        <button onclick="showModal('modal-id')" class="btn btn-xs py-0 px-1" style="font-size:10px;color:#206bc4;border:1px solid #bfdbfe;background:#eff6ff;">+ New</button>
      </div>
      <div style="flex:1;overflow-y:auto;">
        @forelse($identifications as $pid)
        <div class="px-2 py-1 border-bottom" style="font-size:11px;">
          <div class="d-flex align-items-start gap-1">
            <div class="flex-fill">
              <div class="fw-medium">{{ $pid->type }}</div>
              <div class="text-muted">{{ $pid->result??'-' }} &bull; {{ $pid->examiner_name }}</div>
              <div class="text-muted" style="font-size:10px;">{{ $pid->transaction_date }}</div>
            </div>
            <form action="{{ url('/rawat-jalan/registered/delete-identification/'.$pid->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm p-0 mt-1" style="color:#ef4444;font-size:14px;line-height:1;background:none;border:none;">×</button>
            </form>
          </div>
        </div>
        @empty
        <div class="p-2 text-muted fst-italic" style="font-size:11px;">Belum ada data identifikasi.</div>
        @endforelse
      </div>
    </div>

</div>{{-- end main grid --}}

{{-- ============================================================
     MODALS
     ============================================================ --}}

{{-- Modal: Add Identification --}}
<div id="modal-id" class="modal-overlay" style="display:none;">
  <div class="modal-content-sm" style="width:440px;">
    <h5 class="mb-3" style="font-size:15px;">Tambah Patient Identification</h5>
    <form action="{{ url('/rawat-jalan/registered/store-identification') }}" method="POST">
      @csrf
      <input type="hidden" name="no_rawat" value="{{ $data->no_rawat }}">
      <div class="mb-2"><label class="form-label" style="font-size:12px;">Transaction Date &amp; Time</label><input type="datetime-local" name="transaction_date" class="form-control form-control-sm" required></div>
      <div class="mb-2"><label class="form-label" style="font-size:12px;">Examiner Name</label><input type="text" name="examiner_name" class="form-control form-control-sm" required></div>
      <div class="mb-2"><label class="form-label" style="font-size:12px;">Type</label><input type="text" name="type" class="form-control form-control-sm" required></div>
      <div class="mb-2"><label class="form-label" style="font-size:12px;">Result</label><input type="text" name="result" class="form-control form-control-sm"></div>
      <div class="mb-3"><label class="form-label" style="font-size:12px;">Notes</label><textarea name="notes" class="form-control form-control-sm" rows="2"></textarea></div>
      <div class="d-flex justify-content-end gap-2">
        <button type="button" onclick="hideModal('modal-id')" class="btn btn-sm btn-outline-secondary">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: Add Allergy --}}
<div id="modal-allergy" class="modal-overlay" style="display:none;">
  <div class="modal-content-sm">
    <h5 class="mb-3" style="font-size:15px;">Tambah Alergi</h5>
    <form action="{{ url('/rawat-jalan/registered/store-allergy') }}" method="POST">
      @csrf
      <input type="hidden" name="no_rkm_medis" value="{{ $data->no_rkm_medis }}">
      <div class="mb-2"><label class="form-label" style="font-size:12px;">Allergen</label><input type="text" name="allergies" class="form-control form-control-sm" required></div>
      <div class="mb-2"><label class="form-label" style="font-size:12px;">Type / Reaction</label><input type="text" name="type_reaction" class="form-control form-control-sm" required></div>
      <div class="mb-3"><label class="form-label" style="font-size:12px;">Severity</label>
        <select name="severity" class="form-select form-select-sm">
          <option value="Mild">Mild</option><option value="Moderate">Moderate</option><option value="Severe">Severe</option>
        </select>
      </div>
      <div class="d-flex justify-content-end gap-2">
        <button type="button" onclick="hideModal('modal-allergy')" class="btn btn-sm btn-outline-secondary">Batal</button>
        <button type="submit" class="btn btn-sm btn-danger">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: Manage DPJP --}}
<div id="modal-dpjp" class="modal-overlay" style="display:none;">
  <div class="modal-content-sm" style="width:440px;">
    <h5 class="mb-3" style="font-size:15px;">Kelola Doctor In Charge (DPJP)</h5>
    <form action="{{ url('/rawat-jalan/registered/store-dpjp') }}" method="POST">
      @csrf
      <input type="hidden" name="no_rawat" value="{{ $data->no_rawat }}">
      <div class="mb-2 p-2 rounded" style="background:#f0f7ff;font-size:12px;">
        <span class="text-muted">DPJP 1 (Utama):</span> <strong>{{ $data->dpjp_1 }}</strong>
      </div>
      <div id="dpjp-modal-container" class="mb-2">
        @foreach($dpjp_tambahan as $urutan => $dpjp)
        <div class="dpjp-row-modal d-flex gap-2 mb-2 align-items-center">
          <span class="text-muted" style="font-size:11px;width:60px;flex-shrink:0;">DPJP {{ $urutan }}</span>
          <select name="dpjp[{{ $urutan }}]" class="form-select form-select-sm flex-fill">
            @foreach($dokters as $dok)
              <option value="{{ $dok->kd_dokter }}" @if($dpjp->kd_dokter == $dok->kd_dokter) selected @endif>{{ $dok->nm_dokter }}</option>
            @endforeach
          </select>
          <button type="button" onclick="this.closest('.dpjp-row-modal').remove()" class="btn btn-sm btn-outline-danger">×</button>
        </div>
        @endforeach
      </div>
      <button type="button" onclick="addNewDpjpRow()" class="btn btn-sm btn-outline-primary w-100 mb-3">+ Tambah Dokter</button>
      <div class="d-flex justify-content-end gap-2">
        <button type="button" onclick="hideModal('modal-dpjp')" class="btn btn-sm btn-outline-secondary">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function showModal(id) { document.getElementById(id).style.display = 'flex'; }
function hideModal(id) { document.getElementById(id).style.display = 'none'; }
document.querySelectorAll('.modal-overlay').forEach(el =>
  el.addEventListener('click', e => { if (e.target === el) el.style.display = 'none'; })
);

function addNewDpjpRow() {
  const container = document.getElementById('dpjp-modal-container');
  const index = container.querySelectorAll('.dpjp-row-modal').length + 2;
  let options = '';
  @foreach($dokters as $dok)
    options += `<option value="{{ $dok->kd_dokter }}">{{ $dok->nm_dokter }}</option>`;
  @endforeach
  const div = document.createElement('div');
  div.className = 'dpjp-row-modal d-flex gap-2 mb-2 align-items-center';
  div.innerHTML = `<span class="text-muted" style="font-size:11px;width:60px;flex-shrink:0;">DPJP ${index}</span><select name="dpjp[${index}]" class="form-select form-select-sm flex-fill">${options}</select><button type="button" onclick="this.closest('.dpjp-row-modal').remove()" class="btn btn-sm btn-outline-danger">×</button>`;
  container.appendChild(div);
}

// Custom tab switching – avoids Bootstrap tab CSS conflicts with flex layout
function switchTab(group, targetId, clickedLink) {
  const isMain = (group === 'main');
  // Hide all panels in this group
  document.querySelectorAll(isMain ? '.ctab-panel:not(.stab-panel)' : '.stab-panel').forEach(p => {
    p.style.display = 'none';
  });
  // Show target
  const target = document.getElementById(targetId);
  if (target) target.style.display = 'flex';

  if (isMain) {
    // Custom main tab style reset (nav-tab-link)
    const navId = 'mainTabNav';
    document.querySelectorAll('#' + navId + ' .nav-tab-link').forEach(a => {
      a.style.fontWeight = '500';
      a.style.color = '#64748b';
      a.style.border = '1px solid transparent';
      a.style.borderBottom = 'none';
      a.style.background = 'transparent';
    });
    if (clickedLink) {
      clickedLink.style.fontWeight = '600';
      clickedLink.style.color = '#206bc4';
      clickedLink.style.border = '1px solid #c8d8f0';
      clickedLink.style.borderBottom = '2px solid #fff';
      clickedLink.style.background = '#fff';
    }
  } else {
    // Sub-tab: still uses Tabler nav-link/active
    const navId = 'subTabNav';
    document.querySelectorAll('#' + navId + ' .nav-link').forEach(a => a.classList.remove('active'));
    if (clickedLink) clickedLink.classList.add('active');
  }
}

// Ensure initially visible panels show correctly on load
document.addEventListener('DOMContentLoaded', function() {
  // main: show tab-cppt, hide others
  ['tab-assessment','tab-forms'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });
  const cppt = document.getElementById('tab-cppt');
  if (cppt) cppt.style.display = 'flex';
  // sub: show sub-review, hide others
  ['sub-edit','sub-student'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });
  const review = document.getElementById('sub-review');
  if (review) review.style.display = 'flex';
});
</script>

<style>
/* Suppress page-body padding so grid fills viewport */
.page-body { padding-bottom: 0 !important; }
.container-xl, .container-fluid { padding-bottom: 0 !important; }
/* Scrollbar aesthetics */
::-webkit-scrollbar { width: 4px; height: 4px; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
@endsection
