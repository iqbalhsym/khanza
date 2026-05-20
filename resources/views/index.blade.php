@extends('adminlte::page')

@section('title','Data Pasien')

@section('content_header')
<h1>Data Pasien</h1>
@stop

@section('content')

<table id="pasienTable" class="table table-bordered table-striped">

<thead>

<tr>
<th>No RM</th>
<th>Nama Pasien</th>
<th>Alamat</th>
<th>Tanggal Lahir</th>
</tr>

</thead>

<tbody>

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

@stop

@section('js')

<script>

$(function(){

$('#pasienTable').DataTable();

});

</script>

@stop
