@extends('layouts.index')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
@php
	function rupiah($angka){
		$hasil_rupiah = " " . number_format((int)$angka);
		$hasil_rupiah = str_replace(',', '.', $hasil_rupiah);
		return $hasil_rupiah;
	}
@endphp
@section('content')
<div class="page-content">
    <!-- judul dan link -->
    <div class="row page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="pe-3 col-md-11">
            <span style="font-weight: bold;">{{$title}}<span>
        </div>
        <div class="pe-3 col-md-1 justify-content-end">
            <span style="color: #787878;">PSC<span>
        </div>
    </div>

    <!-- main content -->
    <div class="card main-layer">
        <div class="card-header">
            <h5>Riwayat Ambulance</h5>
        </div>
        <div class="card-body">
            <div class="row" style="margin-top: 2rem">
                <div class="col">
                  <input class="form-control" type="hidden">
                </div>
                <div class="col">
                    <input class="form-control" type="hidden">
                </div>
                <div class="col">
                    <div class="row mb-3">
                        <label  class="col-sm-4 col-form-label">Tanggal</label>
                        <div class="col-sm-8">
                            <input type="date" id="tanggal" class="form-control">
                        </div>
                      </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Nama Peminta</td>
                                <td>No. Telepon</td>
                                <td>Tanggal Order</td>
                                <td>Jenis Layanan</td>
                                <td>Titik Jemput</td>
                                <td>Titik Antar</td>
                                <td>Status</td>
                                <td>Tanggal Selesai</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="other-page"></div>
    <div class="col-12 modal-dialog"></div>
</div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
		$(".knob").knob();
        var date = new Date();
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;

        var today = year + "-" + month + "-" + day ;      
        $("#tanggal").attr("value", today);

        loadTable(today);
        filterByDate();
	});

    function loadTable(tanggal = null){
        var table = $('#datatabel').DataTable({
            scrollX: true,
            searching: true, 
            paging: true,
            processing: true,
            serverSide: true,
            columnDefs: [
                {
                    sortable: false,
                    'targets': [0]
                }, {
                    searchable: false,
                    'targets': [0]
                },
            ],
            ajax: {
                url: "{{route('mainRiwayatPsc')}}",
                data: {
                    tanggal : tanggal
                }
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", class: "text-center"},
                { data: "nama", name: "nama", class: "text-center"},
                { data: "no_telepon", name: "no_telepon", class: "text-center"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan", class: "text-center"},
                { data: "jenis_layanan", name: "jenis_layanan", class: "text-center"},
                { data: "longitude_jemput", name: "longitude_jemput", class: "text-center"},
                { data: "longitude_antar", name: "longitude_antar", class: "text-center"},
                { data: "status_pasien", name: "status_pasien", class: "text-center"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan", class: "text-center"},
            ],
        })
    }

    function filterByDate() {
        $("#tanggal").change(function (e) { 
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable( $("#tanggal").val() );
        });
    }

    function downloadHasil(id) {
        alert('Maintenance')
    }
</script>
@endpush