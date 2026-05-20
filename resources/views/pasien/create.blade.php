@extends('layout.app')

@section('content')

<div class="card p-4">

<h4>Tambah Pasien Baru</h4>

<form method="POST" action="/pasien/store">

@csrf

<div class="row">

<div class="col-md-6">

<label>No Rekam Medis</label>

<input class="form-control"
name="no_rm">

</div>

<div class="col-md-6">

<label>Nama Pasien</label>

<input class="form-control"
name="nama">

</div>

</div>

<br>

<div class="row">

<div class="col-md-4">

<label>Jenis Kelamin</label>

<select class="form-control"
name="jk">

<option value="L">Laki-laki</option>
<option value="P">Perempuan</option>

</select>

</div>

<div class="col-md-4">

<label>Tanggal Lahir</label>

<input type="date"
class="form-control"
name="tgl_lahir">

</div>

<div class="col-md-4">

<label>No Telp</label>

<input class="form-control"
name="no_tlp">

</div>

</div>

<br>

<label>Alamat</label>

<textarea class="form-control"
name="alamat"></textarea>

<br>

<button class="btn btn-primary">
Simpan Pasien
</button>

</form>

</div>

@endsection
