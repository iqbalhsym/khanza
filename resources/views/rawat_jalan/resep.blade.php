@extends('layout.app')

@section('title', 'E-Resep')
@section('page-title', 'Resep Obat')
@section('breadcrumb', 'Input Resep')

@push('styles')
<style>
/* ── Drug Search Autocomplete ── */
.drug-search-wrap {
  position: relative;
}
.drug-search-input {
  width: 100%;
  padding: 7px 10px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 13px;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.drug-search-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}
.drug-search-input.has-value {
  border-color: #10b981;
  background: #f0fdf4;
}
.drug-dropdown {
  position: absolute;
  top: calc(100% + 3px);
  left: 0; right: 0;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  box-shadow: 0 8px 28px rgba(0,0,0,0.13);
  z-index: 9999;
  max-height: 280px;
  overflow-y: auto;
  display: none;
}
.drug-dropdown.open { display: block; }
.drug-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  cursor: pointer;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.1s;
}
.drug-item:last-child { border-bottom: none; }
.drug-item:hover, .drug-item.highlighted { background: #eff6ff; }
.drug-icon {
  width: 30px; height: 30px;
  border-radius: 6px;
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; flex-shrink: 0;
}
.drug-name { font-weight: 600; font-size: 13px; color: #1e293b; line-height: 1.3; }
.drug-meta { font-size: 11px; color: #64748b; margin-top: 1px; }
.drug-code-badge {
  margin-left: auto;
  font-size: 10px; font-weight: 600;
  background: #eff6ff; color: #2563eb;
  padding: 2px 7px; border-radius: 20px;
  white-space: nowrap; flex-shrink: 0;
}
.drug-empty, .drug-hint {
  padding: 14px 12px;
  text-align: center;
  color: #9ca3af;
  font-size: 12px;
}
.drug-spinner {
  position: absolute;
  right: 10px; top: 50%;
  transform: translateY(-50%);
  display: none;
}
.drug-spinner.active { display: block; }
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

/* Row style */
.obat-row td { vertical-align: middle; padding: 8px 6px; }
.obat-row td:first-child { min-width: 320px; }
</style>
@endpush

@section('content')

@if(session('error'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;">
  {{ session('error') }}
</div>
@endif

<div class="page-header">
  <div class="page-header-left">
    <h1>Input E-Resep</h1>
    <p>Resep untuk: <strong class="text-primary">{{ $reg->nm_pasien }}</strong> &bull; No. Rawat: <code>{{ $reg->no_rawat }}</code></p>
  </div>
  @php $backUrl = request('ref') === 'index' ? url('/rawat-jalan') : url('/rawat-jalan/registered/'.urlencode($reg->no_rawat)); @endphp
  <a href="{{ $backUrl }}" class="btn btn-ghost">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div class="card">
  <div class="card-header">
    <h3>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg>
      Daftar Obat &amp; Aturan Pakai
    </h3>
    <p class="text-sm text-muted" style="margin-top:4px;">Ketik nama obat di kolom "Pilih Obat" untuk mencari. Minimal 2 karakter.</p>
  </div>
  <div class="card-body">
    <form action="{{ url('/rawat-jalan/resep/store') }}" method="POST" id="form-resep">
      @csrf
      <input type="hidden" name="no_rawat" value="{{ $reg->no_rawat }}">

      <table class="table" id="obat-table" style="table-layout:fixed;">
        <colgroup>
          <col style="width:42%;">
          <col style="width:13%;">
          <col style="width:13%;">
          <col style="width:25%;">
          <col style="width:7%;">
        </colgroup>
        <thead>
          <tr>
            <th>Nama Obat</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Aturan Pakai</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="obat-tbody">
          {{-- Row 1 (template) --}}
          <tr class="obat-row">
            <td>
              <div class="drug-search-wrap">
                {{-- Hidden: kode_brng untuk form submit --}}
                <input type="hidden" name="obat[]" class="drug-hidden-val">
                {{-- Search box --}}
                <div style="position:relative;">
                  <input type="text" class="drug-search-input" placeholder="Cari nama atau kode obat..." autocomplete="off">
                  <span class="drug-spinner">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="animation:spin 0.8s linear infinite;"><circle cx="12" cy="12" r="10" stroke-dasharray="30 10"/></svg>
                  </span>
                </div>
                <div class="drug-dropdown">
                  <div class="drug-hint">Ketik minimal 2 karakter untuk mencari obat</div>
                </div>
              </div>
            </td>
            <td>
              <input type="number" class="form-control form-control-sm" name="jumlah[]" required min="0.5" step="0.5" value="1" style="width:80px;">
            </td>
            <td>
              <input type="text" class="drug-satuan form-control form-control-sm" name="satuan[]" readonly placeholder="-" style="background:#f8fafc;width:80px;">
            </td>
            <td>
              <input type="text" class="form-control form-control-sm" name="aturan[]" required placeholder="3x1 sesudah makan">
            </td>
            <td>
              <button type="button" class="btn btn-ghost btn-sm remove-row" style="color:#ef4444;padding:4px 8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center;">
        <button type="button" class="btn btn-ghost btn-sm" id="add-row">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Tambah Obat Lain
        </button>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Kirim Resep ke Farmasi
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const SEARCH_OBAT_URL = '{{ url("/rawat-jalan/search-obat") }}';
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function escHtml(t) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(t || ''));
  return d.innerHTML;
}

function initDrugSearch(row) {
  const wrap      = row.querySelector('.drug-search-wrap');
  const input     = row.querySelector('.drug-search-input');
  const hidden    = row.querySelector('.drug-hidden-val');
  const dropdown  = row.querySelector('.drug-dropdown');
  const spinner   = row.querySelector('.drug-spinner');
  const satuanBox = row.querySelector('.drug-satuan');

  let timer = null;
  let results = [];
  let hlIdx = -1;

  function showHint() {
    dropdown.innerHTML = '<div class="drug-hint">Ketik minimal 2 karakter untuk mencari obat</div>';
    dropdown.classList.add('open');
  }

  function showEmpty() {
    dropdown.innerHTML = '<div class="drug-empty">Obat tidak ditemukan.</div>';
    dropdown.classList.add('open');
  }

  function renderResults(data) {
    results = data; hlIdx = -1;
    dropdown.innerHTML = '';
    if (!data.length) { showEmpty(); return; }
    data.forEach((d, i) => {
      const el = document.createElement('div');
      el.className = 'drug-item';
      el.dataset.i = i;
      const satuan = d.kode_sat || '-';
      el.innerHTML = `
        <div class="drug-icon">💊</div>
        <div style="flex:1;min-width:0;">
          <div class="drug-name">${escHtml(d.nama_brng)}</div>
          <div class="drug-meta">Satuan: ${escHtml(satuan)}</div>
        </div>
        <span class="drug-code-badge">${escHtml(d.kode_brng)}</span>
      `;
      el.addEventListener('mousedown', e => { e.preventDefault(); pick(d); });
      dropdown.appendChild(el);
    });
    dropdown.classList.add('open');
  }

  function pick(d) {
    hidden.value      = d.kode_brng;
    input.value       = d.nama_brng;
    satuanBox.value   = d.kode_sat || '-';
    input.classList.add('has-value');
    dropdown.classList.remove('open');
    spinner.classList.remove('active');
  }

  function clearPick() {
    hidden.value    = '';
    satuanBox.value = '';
    input.classList.remove('has-value');
  }

  function doSearch(q) {
    spinner.classList.add('active');
    fetch(`${SEARCH_OBAT_URL}?q=${encodeURIComponent(q)}`, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    })
    .then(data => { spinner.classList.remove('active'); renderResults(data); })
    .catch(err => { spinner.classList.remove('active'); console.error('search-obat error:', err); showEmpty(); });
  }

  input.addEventListener('input', function() {
    clearPick();
    const q = this.value.trim();
    clearTimeout(timer);
    if (q.length < 2) { showHint(); return; }
    timer = setTimeout(() => doSearch(q), 260);
  });

  input.addEventListener('focus', function() {
    if (this.value.trim().length >= 2 && results.length) dropdown.classList.add('open');
    else if (this.value.trim().length < 2) showHint();
  });

  input.addEventListener('keydown', function(e) {
    const items = dropdown.querySelectorAll('.drug-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); hlIdx = Math.min(hlIdx+1, items.length-1); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); hlIdx = Math.max(hlIdx-1, 0); }
    else if (e.key === 'Enter' && hlIdx >= 0 && results[hlIdx]) { e.preventDefault(); pick(results[hlIdx]); return; }
    else if (e.key === 'Escape') { dropdown.classList.remove('open'); return; }
    items.forEach((el, i) => el.classList.toggle('highlighted', i === hlIdx));
    if (items[hlIdx]) items[hlIdx].scrollIntoView({ block:'nearest' });
  });

  document.addEventListener('click', function(e) {
    if (!wrap.contains(e.target)) dropdown.classList.remove('open');
  });
}

// Remove row
function bindRemove() {
  document.querySelectorAll('.remove-row').forEach(btn => {
    btn.onclick = function() {
      const rows = document.querySelectorAll('.obat-row');
      if (rows.length > 1) this.closest('tr').remove();
      else alert('Minimal harus ada 1 obat dalam resep.');
    };
  });
}

// Add row
document.getElementById('add-row').addEventListener('click', function() {
  const tbody = document.getElementById('obat-tbody');
  const template = document.querySelector('.obat-row');
  const newRow = template.cloneNode(true);

  // Reset values
  newRow.querySelector('.drug-hidden-val').value = '';
  newRow.querySelector('.drug-search-input').value = '';
  newRow.querySelector('.drug-search-input').classList.remove('has-value');
  newRow.querySelector('input[type="number"]').value = '1';
  newRow.querySelector('input[name="aturan[]"]').value = '';
  newRow.querySelector('.drug-satuan').value = '';
  newRow.querySelector('.drug-dropdown').innerHTML =
    '<div class="drug-hint">Ketik minimal 2 karakter untuk mencari obat</div>';
  newRow.querySelector('.drug-dropdown').classList.remove('open');

  tbody.appendChild(newRow);
  initDrugSearch(newRow);
  bindRemove();
  newRow.querySelector('.drug-search-input').focus();
});

// Form validation: all hidden drug vals must be set
document.getElementById('form-resep').addEventListener('submit', function(e) {
  const empties = [...document.querySelectorAll('.drug-hidden-val')].filter(h => !h.value);
  if (empties.length) {
    e.preventDefault();
    empties[0].closest('.obat-row').querySelector('.drug-search-input').focus();
    empties[0].closest('.drug-search-wrap').querySelector('.drug-search-input').style.borderColor = '#ef4444';
    setTimeout(() => empties[0].closest('.drug-search-wrap').querySelector('.drug-search-input').style.borderColor = '', 2000);
    alert('Silakan pilih obat terlebih dahulu dari hasil pencarian.');
  }
});

// Init first row
initDrugSearch(document.querySelector('.obat-row'));
bindRemove();
</script>
@endpush

@endsection
