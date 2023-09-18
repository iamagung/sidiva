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
            <h5>Driver Ambulance</h5>
        </div>
        <div class="card-body">
            <div class="row" style="margin-top: 0rem">
                <div class="col-md-3" style="margin-bottom:-30px; margin-left: 160px">
                </div>
            </div>
                <div class="table-responsive" style="margin-top: 10px">
                    <table id="datatabel" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>No. Telepone</td>
                                <td>Nama Driver</td>
                                <td>Alamat</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>No</td>
                                <td>081234432234</td>
                                <td>STEPANUS SIMANJUNTAK</td>
                                <td>Jonggol</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="" class="btn shadow btn-xs sharp me-1" style="background-color: #D9D9D9;"><i class='bx bx-refresh' style="font-size: 40px" undefined></i></a>
                                        <a href="" class="btn btn-secondary shadow btn-xs sharp me-1" data-bs-toggle="modal" data-bs-target="#exampleModalEdit"><i class='bx bxs-edit' style='color:#ffffff; font-size: 30px'></i></a>
                                        <a href="" class="btn btn-danger shadow sharp me-1" data-bs-toggle="modal" data-bs-target="#exampleModalHapus"><i class='bx bx-trash' style='color:#ffffff; font-size: 35px' ></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
    <div class="other-page"></div>
    <div class="col-12 modal-dialog"></div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="exampleModalHapus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda sudah yakin?</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                        <button type="button" class="btn btn-primary">Yakin</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit/Add -->
    <div class="modal fade" id="exampleModalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Driver Ambulance</h5>
                    <button type="button" class="btn" data-bs-dismiss="modal"><i class='bx bx-x-circle' style="font-size: 30px; width: 15px;" undefined ></i></button>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Nama Lengkap*</label>
                            <input type="text" class="form-control" id="exampleInputEmail1">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">No. Telepon*</label>
                            <input type="text" class="form-control" id="exampleInputPassword1">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Alamat*</label>
                            <input type="text" class="form-control" id="exampleInputPassword1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #6C757D; color: white;">Kembali</button>
                        <button type="button" class="btn" style="background-color: #00A006; color: white;">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                url: "{{route('mainPermintaanMcu')}}",
                data: {
                    tanggal : tanggal
                }
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "tanggal_order", name: "tanggal_order"},
                { data: "nama", name: "nama"},
                { data: "jenis_pembayaran", name: "jenis_pembayaran"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "jenis_layanan", name: "jenis_layanan"},
                { data: "nama_layanan", name: "nama_layanan"},
                { data: "deskripsi", name: "deskripsi"},
                { data: "pembayaran", name: "pembayaran"},
                { data: "actions", name: "actions", class: "text-center"},
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

    function bayar(id) {
        $.post('{{ route("formBayarPermintaanMcu") }}',{id:id})
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

    function proses(id) {
        $.post('{{route("prosesPermintaanMcu")}}',{id:id})
        .done((res)=>{
            if(res.code==200){
                Swal.fire('Berhasil', res.message, 'success')
                location.reload()
            } else if(res.code==205) {
                Swal.fire('Peringatan!', res.message, 'warning')
            }else{
                Swal.fire('Gagal', res.message, 'error')
            }
        }).fail(()=>{
            Swal.fire('Maaf!!', 'Terjadi kesalahan sistem', 'error')
        })
    }

</script>
@endpush