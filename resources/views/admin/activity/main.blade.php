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
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Nama Pengguna</td>
                                <td>Level User</td>
                                <td>Aktivitas</td>
                                <td>Waktu</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    $(document).ready(function() {
		$(".knob").knob();
        loadTable();
	});
    function loadTable(){
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
                url: "{{route('mainActivity')}}",
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "modifyPengguna", name: "modifyPengguna"},
                { data: "modifyLevel", name: "modifyLevel"},
                { data: "aktivitas", name: "aktivitas"},
                { data: "modifyWaktu", name: "modifyWaktu"}
            ],
        })
    }
</script>
@endpush
