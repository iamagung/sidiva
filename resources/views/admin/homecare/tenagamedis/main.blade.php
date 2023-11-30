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
                <div class="col-md-3">
                    <button type="button" class="btn btn-sm" style="background: #4E5FBC; color: #ffffff;" onclick="formAdd()"><i class="bx bxs-plus-square"></i> Tambah</button>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
            </div>
            <hr>
            <div class="row">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td class="text-center">No</td>
                                <td>Nama Tenaga Kesehatan</td>
                                <td>Jenis Nakes</td>
                                {{-- <td>No. Telepon</td> --}}
                                <td>Layanan Homecare</td>
                                <td class="text-center">Status</td>
                                <td class="text-center">Opsi</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="modalForm"></div>
</div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            ordering: false,
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
                url: "{{route('nakesHomecare')}}",
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "nama_nakes", name: "nama_nakes"},
                { data: "jenis_nakes", name: "jenis_nakes"},
                // { data: "telepon", name: "telepon"},
                { data: "layanan", name: "layanan"},
                { data: "stts", name: "stts"},
                { data: "actions", name: "actions", class: "text-center"},
            ],
        })
    }

    function formAdd(id='') {
        $.post("{{route('formNakesHomecare')}}",{id:id},function(data){
			$("#modalForm").html(data.content);
		});
    }

    function hapus(id) {
        Swal.fire({
            title: 'Apakah Kamu Yakin?',
            text: "Data Tenaga Medis Akan Dihapus Permanen",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#5A6268',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{route('deleteNakesHomecare')}}",{id:id})
                .done((data) => {
                    if(data.status == "success"){
                        Swal.fire('Berhasil!', 'Berhasil Menghapus Data', 'success');
                        location.reload();
                    }else{
                        Swal.fire('Maaf!', 'Gagal Menghapus Data', 'error');
                    }
                }).fail(() => {
                    Swal.fire('Maaf!', 'Terjadi Kesalahan!', 'warning');
                })
            }
        })
    }

    function hideForm(){
        $('.other-page').empty()
        $('.main-layer').show()
    }
</script>
@endpush
