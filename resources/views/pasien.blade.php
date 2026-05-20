<!DOCTYPE html>
<html>
<head>
<title>Data Pasien</title>

<style>
table{
border-collapse:collapse;
width:100%;
}

th,td{
border:1px solid #ccc;
padding:8px;
}

th{
background:#eee;
}

</style>

</head>
<body>

<h2>Data Pasien SIMRS Khanza</h2>

<table>

<tr>
<th>No RM</th>
<th>Nama Pasien</th>
<th>Alamat</th>
<th>Tanggal Lahir</th>
</tr>

@foreach($pasien as $p)

<tr>
<td>{{ $p->no_rkm_medis }}</td>
<td>{{ $p->nm_pasien }}</td>
<td>{{ $p->alamat }}</td>
<td>{{ $p->tgl_lahir }}</td>
</tr>

@endforeach

</table>

</body>
</html>
