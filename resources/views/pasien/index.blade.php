@extends('layout.app')

@section('content')

<div class="card p-4">

<div class="d-flex justify-content-between mb-3">

<h4>Data Pasien</h4>

<a href="/pasien/create" class="btn btn-primary">
Tambah Pasien
</a>

<input type="text"
id="searchPasien"
class="form-control"
placeholder="Cari pasien..."
style="max-width:250px">

</div>

<table class="table table-striped table-hover">

<thead class="table-dark">

<tr>

<th>No RM</th>
<th>Nama Pasien</th>
<th>Alamat</th>
<th>Tanggal Lahir</th>

</tr>

</thead>

<tbody id="tablePasien">

@foreach($pasien as $p)

<tr>

<td>{{ $p->no_rkm_medis }}</td>
<td>{{ $p->nm_pasien }}</td>
<td>{{ $p->alamat }}</td>
<td>{{ $p->tgl_lahir }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>

<script>

document.getElementById("searchPasien")
.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#tablePasien tr");

rows.forEach(function(row){

row.style.display =
row.innerText.toLowerCase().includes(value)
? ""
: "none";

});

});

</script>

@endsection
