<?php 
function rupiah($angka)
{
  $hasil_rupiah = "Rp. " . number_format((int)$angka);
  $hasil_rupiah = str_replace(',', '.', $hasil_rupiah);
  return $hasil_rupiah;
}
?>
<table id="excelDebet" style="height:auto;">
	<thead>
		<tr>
			<td colspan="10"><b><p style="text-align:center;">{{ $judul }}</p></b></td>
		</tr>
		<tr>
			<td colspan="10"><p style="text-align: center;">RSUD WAHIDIN SUDIROHUSODO MOJOKERTO</p></td>
		</tr>
		<tr>
			<td colspan="10"><p style="text-align: center;">{{ $periode }}</p></td>
		</tr>
		<tr>
			<th><p style="font-weight:bold;text-align:center">No</p></th>
			<th><p style="font-weight:bold;text-align:center">No. RM </p></th>
			<th><p style="font-weight:bold;text-align:center">NIK</p></th>
			<th><p style="font-weight:bold;text-align:center">Nama</p></th>
			<th><p style="font-weight:bold;text-align:center">Alamat</p></th>
			<th><p style="font-weight:bold;text-align:center">Alergi Pasien</p></th>
			<th><p style="font-weight:bold;text-align:center">Jenis Layanan</p></th>
            <th><p style="font-weight:bold;text-align:center">Tanggal Kunjungan</p></th>
            <th><p style="font-weight:bold;text-align:center">Jenis Kelamin</p></th>
            <th><p style="font-weight:bold;text-align:center">Tanggal Lahir</p></th>
            <th><p style="font-weight:bold;text-align:center">Telepon</p></th>
            <th><p style="font-weight:bold;text-align:center">Jumlah Biaya</p></th>
		</tr>
	</thead>
	@php
	$no = 1;
	@endphp
	<tbody id='panelHasil'>
		@foreach ($data as $item)
		<tr>
			<td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$no}}</p></td>
			<td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{!empty($item->no_rm) ? $item->no_rm : '-'}}</p></td>
			<td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$item->nik}}</p></td>
			<td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$item->nama}}</p></td>
			<td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$item->alamat}}</p></td>
			<td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{!empty($item->alergi_pasien) ? $item->alergi_pasien : '-'}}</p></td>
			<td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$item->layanan_hc->nama_layanan}}</p></td>
            <td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$item->tanggal_kunjungan}}</p></td>
            <td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$item->jenis_kelamin}}</p></td>
            <td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$item->tanggal_lahir}}</p></td>
            <td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{$item->no_telepon}}</p></td>
            <td><p style="padding: 5px; vertical-align: middle;" align="center" valign="middle">{{!empty($item->biaya_layanan) ? rupiah($item->biaya_layanan) : '0'}}</p></td>
		</tr>
		@php
		$no++;
		@endphp
		@endforeach
		@if($no == '1')
		<tr>
			<td colspan="10" style="text-align: center;padding: 5px;">Tidak Ada Data</td>
		</tr>
		@endif
	</tbody>
</table>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="{{asset('assets/js/jquery.table2excel.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var date = new Date();
		var getYear = date.getFullYear();
		var getMonth = date.getMonth()+1;
		var getDate = date.getDate();
		var output = getYear+'/'+(getMonth<10 ? '0':'')+getMonth+'/'+(getDate<10 ? '0':'')+getDate;
		$('#excelDebet').table2excel({
			exclude: ".noExl",
			name: "Permintaan Homecare",
			filename: "Permintaan Homecare "+output+".xls",
			fileext: ".xls",
			exclude_img: false,
			exclude_links: false,
			exclude_inputs: true,
			preserveColors: true
		});
	});
</script>