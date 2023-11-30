@extends('layouts.index')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Or for RTL support -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
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
                <div class="col-md-6"></div>
                <div class="col-md-3">
                    <label>Pilih Status</label>
                    <select name="pilih_status" id="pilih_status" class="form-control single-select">
                        <option value="all">Semua</option>
                        <option value="belum">Belum</option>
                        <option value="proses">Proses</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="batal">Batal</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Tanggal Homecare</label>
                    <input type="date" id="tanggal" class="form-control">
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td class="text-center">No</td>
                                <td>Tanggal Order</td>
                                <td class="text-center">No. RM</th>
                                <td>Nama Pasien</td>
                                <td>Alamat Pasien</td>
                                <td>Layanan Homecare</td>
                                <td>Tanggal Mulai</td>
                                <td>Pembayaran</td>
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
    <div class="col-12 modal-dialog"></div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
        loadTable(today,$('#pilih_status').val());
        filterByTwo();
	});
    $("#pilih_status").select2({
        theme: 'bootstrap-5'
    });
    function loadTable(tanggal=null, status=null){
        var table = $('#datatabel').DataTable({
            scrollX: true,
            searching: true,
            ordering: true,
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
                url: "{{route('mainPermintaanHC')}}",
                data: {
                    tanggal : tanggal,
                    status : status
                }
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", class: "text-center"},
                { data: "tanggal_order", name: "tanggal_order"},
                { data: "noRm", name: "noRm", class: "text-center"},
                { data: "nama", name: "nama"},
                { data: "alamat", name: "alamat"},
                { data: "layanan", name: "layanan"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "pembayaran", name: "pembayaran"},
                { data: "status", name: "status", class: "text-center"},
                { data: "opsi", name: "opsi", class: "text-center"},
            ],
        })
    }

    function filterByTwo() {
        $("#tanggal").change(function (e) {
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable($(this).val(), $("#pilih_status").val());
        });
        $("#pilih_status").change(function (e) {
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable($("#tanggal").val(), $(this).val());
        });
    }
    function pilih(id) {
        $.post('{{ route("formPermintaanHC") }}',{id:id})
        .done(function(data) {
            if (data.status == 'success') {
                $('.modal-dialog').html(data.content);
            } else {
                Swal.fire('Maaf',data.message,"warning");
            }
        }).fail(function() {
            Swal.fire('Oops!!',"Terjadi kesalahan sistem!","error");
        });
    }

    function detail(id) {
        $.post('{{ route("formPermintaanHC") }}',{id:id,view:1})
        .done(function(data) {
            if (data.status == 'success') {
                $('.modal-dialog').html(data.content);
            } else {
                Swal.fire('Maaf',data.message,"warning");
            }
        }).fail(function() {
            Swal.fire('Oops!!',"Terjadi kesalahan sistem!","error");
        });
    }

    function tolak(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah anda yakin?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#5A6268',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{route('tolakPermintaanHc')}}",{id:id})
                .done((data) => {
                    if(data.status == "success"){
                        Swal.fire('Berhasil!', data.message, 'success');
                        $('#datatabel').DataTable().ajax.reload();
                    }else{
                        Swal.fire('Maaf!', data.message, 'error');
                    }
                }).fail(() => {
                    Swal.fire('Maaf!', 'Terjadi Kesalahan!', 'warning');
                })
            }
        })
    }
    function terima(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah anda yakin?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#5A6268',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{route('terimaPermintaanHc')}}",{id:id})
                .done((data) => {
                    if(data.status == "success"){
                        Swal.fire('Berhasil!', data.message, 'success');
                        $('#datatabel').DataTable().ajax.reload();
                    }else{
                        Swal.fire('Maaf!', data.message, 'error');
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
