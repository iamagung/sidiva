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
                <div class="col-md-6"></div>
                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" id="status" class="form-control single-select">
                        <option value="all" selected>Semua</option>
                        <option value="belum">Belum</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="proses">Proses</option>
                        <option value="batal">Batal</option>
                        <option value="ditolak">Tolak</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Tanggal MCU</label>
                    <input type="date" id="tanggal" class="form-control">
                </div>
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
                                <td>Nama Layanan</td>
                                <td>Pilihan Tanggal</td>
                                <td>Pembayaran</td>
                                <td>Status</td>
                                <td>Opsi</td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
		$(".knob").knob();
        $(".single-select").select2({
            theme: 'bootstrap-5'
        });
        var date = new Date();
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;

        var today = year + "-" + month + "-" + day ;
        $("#tanggal").attr("value", today);

        var status = $("#status").val();

        loadTable(today,status);
        filterByTwo();
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
                url: "{{route('mainPermintaanMcu')}}",
                data: {
                    tanggal: tanggal,
                    status: status
                }
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "tanggal_order", name: "tanggal_order"},
                { data: "no_rm", name: "no_rm"},
                { data: "nama", name: "nama"},
                { data: "jenis_mcu", name: "jenis_mcu"},
                { data: "layanan_mcu", name: "layanan_mcu"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "payment", name: "payment"},
                { data: "modifyStatus", name: "modifyStatus"},
                { data: "opsi", name: "opsi", class: "text-center"},
            ],
        });
    }
    function filterByTwo() {
        $("#tanggal").change(function (e) {
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable($(this).val(), $("#status").val());
        });
        $("#status").change(function (e) {
            e.preventDefault();
            $('#datatabel').DataTable().destroy();
            loadTable($("#tanggal").val(), $(this).val());
        });
    }
    function pilih(id) {
        $.post('{{ route("formPermintaanMcu") }}',{id:id})
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
        $.post('{{ route("formPermintaanMcu") }}',{id:id,view:1})
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
                $.post("{{route('terimaPermintaanMcu')}}",{id:id})
                .done((data) => {
                    console.log(data)
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
                $.post("{{route('tolakPermintaanMcu')}}",{id:id})
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
</script>
@endpush
