@extends('layouts.index')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
@section('content')
<div class="page-content">
    <!-- judul dan link -->
    <div class="row page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="pe-3 col-md-10">
            <span style="font-weight: bold;">{{$title}}<span>
        </div>
        <div class="pe-3 col-md-2 justify-content-end">
            <span style="color: #787878;">Layanan Ambulance<span>
        </div>
    </div>

    <!-- main content -->
    <div class="card main-layer">
        <div class="card-header">
            <h5>Layanan PSC</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <button type="button" class="btn btn-sm" style="background: #4E5FBC; color: #ffffff;" onclick="formAdd()"><i class="bx bxs-plus-square"></i> Tambah Layanan</button>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
            </div>

            <div class="row" style="margin-top: 2rem">
                <div class="table-responsive">
                    <table id="datatabel" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Nama Layanan</td>
                                <td>Status</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Antar Jenazah</td>
                                <td>Aktif</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class='bx bxs-edit' style='color:#0000ff; font-size: 30px'></i></a>
                                        <a href="" onclick="return confirm('Anda yakin ingin menghapus data?')"><i class='bx bxs-trash' style='color:#ff0000; font-size: 30px'></i></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="other-page"></div>
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
              <label for="recipient-name" class="col-form-label">Nama Layanan*</label>
              <input type="text" class="form-control" id="recipient-name">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
          <button type="button" class="btn btn-success">Simpan</button>
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
                url: "{{route('mainLayananMcu')}}",
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "jenis_layanan", name: "jenis_layanan"},
                { data: "nama_layanan", name: "nama_layanan"},
                { data: "deskripsi", name: "deskripsi"},
                { data: "formatHarga", name: "formatHarga"},
                { data: "actions", name: "actions", class: "text-center"},
            ],
        })
    }

    function formAdd(id='') {
        $('.main-layer').hide();
        $.post("{{route('formLayananMcu')}}", {id:id})
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

    function hapus(id) {
        Swal.fire({
            title: 'Apakah Kamu Yakin?',
            text: "Data Layanan MCU Akan Dihapus Permanen",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#5A6268',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{route('deleteLayananMcu')}}",{id:id})
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