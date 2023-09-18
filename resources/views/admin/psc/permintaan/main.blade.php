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
            <h5>Permintaan PSC</h5>
        </div>
        <div class="card-body">
            <div class="row" style="margin-top: 0rem">
                <div class="col-md-3" style="margin-bottom:-30px; margin-left: 160px">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label>Tanggal PSC</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" id="tanggal" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
                <div class="table-responsive" style="margin-top: 10px">
                    <table id="datatabel" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Nama Peminta</td>
                                <td>No. Telepone</td>
                                <td>Tanggal Order</td>
                                <td>Jenis Layanan</td>
                                <td>Titik Jemput</td>
                                <td>Titik Antar</td>
                                <td>Pembayaran</td>
                                <td>Opsi</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>No</td>
                                <td>STEPANUS SIMANJUNTAK</td>
                                <td>081234432234</td>
                                <td>22-06-2024</td>
                                <td>ANTAR JENAZAH</td>
                                <td>Kota Mojokerto, Jawa Timur 61315</td>
                                <td>Kota Mojokerto</td>
                                <td>LUNAS</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-primary shadow btn-xs sharp me-1">Proses</a>
                                        <a href="" class="btn btn-danger shadow sharp me-1" onclick="return confirm('Anda yakin ingin menghapus data?')"><i class='bx bx-x' style="font-size: 30px"></i></a>
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
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Permintaan Ambulance</h5>
          <button type="button" class="btn" data-bs-dismiss="modal"><i class='bx bx-x-circle' style="font-size: 40px" undefined ></i></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="recipient-name" class="col-form-label">Nama Peminta*</label>
              <input type="text" class="form-control" id="recipient-name">
            </div>
            <div class="mb-3">
              <label for="message-text" class="col-form-label">Pilih Driver*</label>
              <select class="form-select" aria-label="Default select example">
                <option selected>Pilih</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
            <div class="mb-3">
                <label for="message-text" class="col-form-label">Pilih Jadwal*</label>
                <div class="row">
                    <div class="col">
                      <input type="date" class="form-control" placeholder="First name" aria-label="First name">
                    </div>
                    <div class="col">
                      <input type="time" class="form-control" placeholder="Last name" aria-label="Last name">
                    </div>
                  </div>
              </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
          <button type="button" class="btn btn-success">Proses</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
{{-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">New message</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="recipient-name" class="col-form-label">Recipient:</label>
              <input type="text" class="form-control" id="recipient-name">
            </div>
            <div class="mb-3">
              <label for="message-text" class="col-form-label">Message:</label>
              <textarea class="form-control" id="message-text"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Send message</button>
        </div>
      </div>
    </div>
  </div>
</div> --}}

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