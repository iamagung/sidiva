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
        <div class="card-header">
            <h5>Tabel Permintaan Telemedicine</h5>
        </div>
        <div class="card-body">
            {{-- <div class="row">
                <div class="col-md-4">
                    <label>Tanggal MCU</label>
                    <input type="date" id="tanggal" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" id="status" class="form-control single-select">
                        <option value="">-Pilih-</option>
                        <option value="all">Semua</option>
                        <option value="belum">Belum</option>
                        <option value="proses">Proses</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="batal">Batal</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-2"></div>
            </div> --}}
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
    <div class="other-page"></div>
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

        loadTable(today);
        filterByDate();
	});

    function loadTable(tanggal = null){
        var table = $('#datatabel').DataTable({
            "dom": "<'row'<'col-sm-2'l><'col-sm-4 datesearchbox'><'col-sm-3 status'><'col-sm-3'f>>",
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
                { data: "jenis_layanan", name: "jenis_layanan"},
                { data: "nama_layanan", name: "nama_layanan"},
                { data: "jenis_pembayaran", name: "jenis_pembayaran"},
                { data: "deskripsi", name: "deskripsi"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "pembayaran", name: "pembayaran"},
                { data: "status_pasien", name: "status_pasien"},
                { data: "actions", name: "actions", class: "text-center"},
            ],
        })
        $("div.datesearchbox").html('<div class="col"><div class="row mb-3"><label  class="col-sm-3 col-form-label">Tanggal MCU</label><div class="col-sm-6"><input type="date" id="tanggal" class="form-control"></div></div></div>');
        $("div.status").html('<div class="col"><div class="row mb-3"><label  class="col-sm-2 col-form-label">Status</label><div class="col-sm-8"><select name="status" id="status" class="form-control single-select"><option value="">- Pilih -</option><option value="all">Semua</option><option value="belum">Belum</option><option value="proses">Proses</option><option value="menunggu">Menunggu</option><option value="batal">Batal</option><option value="selesai">Selesai</option></select></div>');
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