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
                                <td>Jumlah Pasien</td>
                                <td>Surat Keterangan</td>
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
        $("#tanggal").attr("value", today);

        loadTable(today);
        filterByDate();
	});

    function loadTable(tanggal = null){
        var table = $('#datatabel').DataTable({
            "dom": "<'row'<'col-sm-2'l><'col-sm-3 datesearchbox'><'col-sm-3 statusbox'><'col-sm-4'f>>" ,
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
                    tanggal : tanggal
                }
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "rm", name: "rm"},
                { data: "nama", name: "nama"},
                { data: "jenis_layanan", name: "jenis_layanan"},
                { data: "nama_layanan", name: "nama_layanan"},
                { data: "nama_layanan", name: "nama_layanan"},
                { data: "nama_layanan", name: "nama_layanan"},
                { data: "nama_layanan", name: "nama_layanan"},
                { data: "nama_layanan", name: "nama_layanan"},
                { data: "actions", name: "actions", class: "text-center"},
            ],
        });
        $("div.datesearchbox").html('<div class="col"><div class="row mb-3"><label  class="col-sm-2 col-form-label">Tanggal</label><div class="col-sm-8"><input type="date" id="tanggal" class="form-control"></div></div></div>');
        $("div.statusbox").html('<div class="col"><div class="row mb-3"><label  class="col-sm-2 col-form-label">Status</label><div class="col-sm-8"><select name="status" id="status" class="form-control single-select"><option value="">-Pilih-</option><option value="all">Semua</option><option value="belum">Belum</option><option value="proses">Proses</option><option value="menunggu">Menunggu</option><option value="batal">Batal</option><option value="selesai">Selesai</option></select></div>');
    }
    $('#tanggal').change(function (e) { 
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable( $("#tanggal").val() );
        });
        
    function downloadHasil(id) {
        alert('Maintenance')
    }
</script>
@endpush