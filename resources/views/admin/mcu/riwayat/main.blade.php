@extends('layouts.index')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
@section('content')
<div class="page-content">
    <!-- judul dan link -->
    <div class="row page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="pe-3 col-md-10">
            <span style="font-weight: bold;">{{$title}}<span>
        </div>
        <div class="pe-3 col-md-2 justify-content-end">
            <span style="color: #787878;">Medical Check Up<span>
        </div>
    </div>

    <!-- main content -->
    <div class="card main-layer">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2" style="margin-top: 5px;">
                    <span class="float-left">Pilih Tanggal</span>
                </div>
                <div class="col-md-3 twodate">
                    <input type="date" id="min" class="form-control float-left">
                </div>
                <div class="col-md-3 twodate">
                    <input type="date" id="max" class="form-control float-left">
                </div>
                <div class="col-md-4"></div>
            </div>
            <hr>
            <div class="row" style="margin-top: 2rem">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Tanggal Order</td>
                                <td>No. RM</td>
                                <td>Nama Pemesan</td>
                                <td>Jenis MCU</td>
                                <td>Layanan MCU</td>
                                <td>Pilihan Tanggal</td>
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
        $("#min").attr("value", today);
        $("#max").attr("value", today);

        loadTable(today , today);
        filterByDate();
	});

    function loadTable(min = null, max = null){
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
                url: "{{route('mainRiwayatMcu')}}",
                data: {
                    min : min,
                    max : max
                }
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "tanggal_order", name: "tanggal_order"},
                { data: "modifyNorm", name: "modifyNorm"},
                { data: "nama", name: "nama"},
                { data: "modifyJenis", name: "modifyJenis"},
                { data: "modifyLayanan", name: "modifyLayanan"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "modifyStatus", name: "modifyStatus"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"}
            ],
        })
    }

    function filterByDate() {
        $("#min").change(function (e) { 
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable( $(this).val() , $("#max").val() );
        });

        $("#max").change(function (e) { 
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable( $("#min").val() , $(this).val() );
        });
    }
        
    function downloadHasil(id) {
        alert('Maintenance')
    }
</script>
@endpush