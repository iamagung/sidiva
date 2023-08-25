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
            <h5>Riwayat MCU</h5>
        </div>
        <div class="card-body">
            <div class="row" style="margin-top: 2rem">
                <div class="col-md-2" style="margin-top: 5px;">
                    <span class="float-left">Pilih Tanggal</span>
                </div>
                <div class="col-md-3 twodate">
                    <input type="date" id="min" class="form-control float-left">
                </div>
                <div class="col-md-3 twodate">
                    <input type="date" id="max" class="form-control float-left">
                </div>
                <div class="col-md-4" style="margin-top: 10px;">
                    <button type="button" class="btn btn-success btn-sm float-end" onclick="exportExcel()"><i class='bx bxs-file-export'></i> Export To Excel</button>
                </div>
            </div>
            <div class="row" style="margin-top: 2rem">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td class="text-center">No</td>
                                <td class="text-center">No. RM</td>
                                <td class="text-center">Tanggal Homecare</td>
                                <td class="text-center">Nama Pasien</td>
                                <td class="text-center">Alamat</td>
                                <td class="text-center">Alergi Pasien</td>
                                <td class="text-center">status</td>
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
                url: "{{route('mainRiwayatHC')}}",
                data: {
						min : min,
						max : max
					}
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", class: "text-center"},
                { data: "rm", name: "rm", class: "text-center"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan", class: "text-center"},
                { data: "nama", name: "nama", class: "text-center"},
                { data: "alamat", name: "alamat", class: "text-center"},
                { data: "alergi", name: "alergi", class: "text-center"},
                { data: "status", name: "status", class: "text-center"},
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

    function exportExcel() {
        var startDate = $('#min').val();
        var endDate = $('#max').val();

        if (startDate <= endDate) {
            $.post("{!! route('exportRiwayatHC') !!}", {
                startDate: startDate,
                endDate: endDate,
            }, function(data) {
                var newWin = window.open('', 'Print-Window');
                newWin.document.open();
                newWin.document.write(
                    '<html><head><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css"></head><body>' +
                    data.content + '</body></html>');
                setTimeout(() => {
                    newWin.document.close();
                    newWin.close();
                }, 3000);
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