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
        <div class="card-header">
            <h5>Permintaan Home Care</h5>
        </div>
        <div class="card-body">
            <div class="row" style="margin-top: 2rem">
                <div class="col-md-4"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">-Pilih-</option>
                        <option value="all">Semua</option>
                        <option value="belum" selected>Belum</option>
                        <option value="proses">Proses</option>
                        <option value="batal">Batal</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Tanggal Homecare</label>
                    <input type="date" id="tanggal" class="form-control">
                </div>
            </div>
            <div class="row" style="margin-top: 2rem">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td class="text-center">No</td>
                                <td class="text-center">Tanggal Order</td>
                                <td class="text-center">No. RM</th>
                                <td class="text-center">Nama Pasien</td>
                                <td class="text-center">Lokasi Pasien</td>
                                <td class="text-center">Layanan Home Care</td>
                                <td class="text-center">Tanggal_kunjungan</td>
                                <td class="text-center">Proses</td>
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
    <div class="other-page"></div>
    {{-- <div class="col-12 modal-dialog"></div> --}}
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
        var status = $('#status').val();
        loadTable(today,status);
        filterByDate();
        filterByStatus();
	});

    function loadTable(tanggal=null, status=null){
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
                url: "{{route('mainPermintaanHC')}}",
                data: {
                    tanggal : tanggal,
                    status : status
                }
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", class: "text-center"},
                { data: "tanggal_order", name: "tanggal_order", class: "text-center"},
                { data: "noRm", name: "noRm", class: "text-center"},
                { data: "nama", name: "nama", class: "text-center"},
                { data: "lokasi", name: "lokasi", class: "text-center"},
                { data: "layanan", name: "layanan", class: "text-center"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan", class: "text-center"},
                { data: "proses", name: "proses", class: "text-center"},
                { data: "opsi", name: "opsi", class: "text-center"},
            ],
        })
    }

    function filterByDate() {
        $("#tanggal").change(function (e) {
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable($("#tanggal").val(), $("#status").val());
        });
    }
    function filterByStatus() {
        $("#status").change(function (e) {
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable($("#tanggal").val(), $("#status").val());
        });
    }
    function formAdd(id) {
        $('.main-layer').hide();
        $.post("{{route('formPermintaanHC')}}", {id:id})
        .done(function(data){
			if(data.status == 'success'){
				$('.other-page').html(data.content).fadeIn();
			} else {
				$('.main-layer').show();
			}
		})
        .fail(() => {
            $('.other-page').empty();
            $('.main-layer').show();
        })
    }

    // function formAdd(id) {
    //     $.post('{{ route("formPermintaanHC") }}',{id:id})
    //     .done(function(data) {
    //         if (data.status == 'success') {
    //             $('.modal-dialog').html(data.content);
    //         } else {
    //             Swal.fire('Maaf',data.message,"warning");    
    //         }
    //     }).fail(function() {
    //         Swal.fire('Oops!!',"Terjadi kesalahan sistem!","error");
    //     });
    // }

    function batal(id) {
        Swal.fire({
            title: 'Apakah Kamu Yakin?',
            text: "Pasien Akan Dibatalkan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#5A6268',
            confirmButtonText: 'Konfirmasi, Batal',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{route('batalPermintaanHC')}}",{id:id})
                .done((data) => {
                    if(data.status == "success"){
                        Swal.fire('Berhasil!', data.message, 'success');
                        location.reload();
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
