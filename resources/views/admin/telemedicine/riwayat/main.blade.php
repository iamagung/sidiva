@extends('layouts.index')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
@section('content')
<div class="page-content">
    <!-- judul dan link -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="pe-3">
            <span style="font-weight: bold;">{{$title}}<span>
        </div>
    </div>

    <!-- main content -->
    <div class="card main-layer">
        <div class="card-header">
            <h5>Riwayat Telemedicine</h5>
        </div>
        <div class="card-body">
            <div class="row" style="margin-top: 2rem">
                {{-- <div class="col-md-2" style="margin-top: 5px;"> --}}
                    {{-- </div> --}}
                <div class="col-md-6 twodate">
                    <label class="float-left">Pilih Tanggal</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="date" id="min" class="form-control float-left">
                        </div>
                        <div class="col-md-6">
                            <input type="date" id="max" class="form-control float-left">
                        </div>

                    </div>
                </div>
                {{-- <div class="col-md-3 twodate">
                </div> --}} 
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" id="status" class="form-control">
                        {{-- <option value="">-Pilih-</option> --}}
                        <option value="all" selected>Semua</option>
                        <option value="belum">Belum</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="proses">Proses</option>
                        <option value="batal">Batal</option>
                        <option value="tolak">Tolak</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-md-4 mt-4" >
                    <button type="button" class="btn btn-success btn-sm float-end" onclick="exportExcel()"><i class='bx bxs-file-export'></i> Export To Excel</button>
                </div>
            </div>
            <div class="row" style="margin-top: 2rem">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td class="text-center">No</td>
                                <td class="text-center">Tanggal Order</td>
                                <td class="text-center">No. RM</td>
                                <td class="text-center">Nama Pasien</td>
                                <td class="text-center">Nama Poli</td>
                                <td class="text-center">Nama Dokter</td>
                                <td class="text-center">Nama Perawat</td>
                                <td class="text-center">Tanggal Mulai</td>
                                <td class="text-center">Status</td>
                                <td class="text-center">Tanggal Selesai</td>
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
        var status = $("#status").val();

        loadTable(today , today, status);
        filterByDate();
        filterByStatus();
	});

    function loadTable(min = null, max = null, status = null){
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
                url: "{{route('mainRiwayatTelemedicine')}}",
                data: {
						min : min,
						max : max,
                        status : status
					}
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", class: "text-center"},
                { data: "tanggal_order", name: "tanggal_order", class: "text-center"},
                { data: "no_rm", name: "no_rm", class: "text-center"},
                { data: "nama", name: "nama", class: "text-center"},
                { data: "nama_poli", name: "nama_poli", class: "text-center"},
                { data: "nama_dokter", name: "nama_dokter", class: "text-center"},
                { data: "nama_perawat", name: "nama_perawat", class: "text-center"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan", class: "text-center"},
                { data: "status", name: "status", class: "text-center"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan", class: "text-center"},
            ],
        })
    }

    function filterByDate() {
        $("#min").change(function (e) { 
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable( $(this).val() , $("#max").val() , $("#status").val() );
        });

        $("#max").change(function (e) { 
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable( $("#min").val() , $(this).val() , $("#status").val() );
        });
    }

    function filterByStatus() {
        $('#status').change(function (e) {
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable( $("#min").val() , $("#max").val() , $(this).val() );
        })
    }

    function exportExcel() {
        var startDate = $('#min').val();
        var endDate = $('#max').val();
        var status = $('#status').val();

        if (startDate <= endDate) {
            $.post("{!! route('exportRiwayatTelemedicine') !!}", {
                startDate: startDate,
                endDate: endDate,
                status: status,
            }, function(data) {
                if (data.status == 'success') {
                    var newWin = window.open('', 'Print-Window');
                    newWin.document.open();
                    newWin.document.write(
                        '<html><head><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css"></head><body>' +
                        data.content + '</body></html>');
                    setTimeout(() => {
                        newWin.document.close();
                        newWin.close();
                    }, 3000);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Whoops',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Whoops',
                text: 'Tanggal Tidak Sesuai!',
                showConfirmButton: false,
                timer: 1200
            });
        }
    }
</script>
@endpush