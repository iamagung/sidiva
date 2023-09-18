@extends('layouts.index')

@push('style')
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
            <h5>{{$title}} Ambulance</h5>
        </div>
        <div class="card-body">
            <form id="commentForm">
                <div class="row mb-3">
                    <input type="hidden" name="id" id="id" value="{{ !empty($syarat) ? $syarat->id_syarat_mcu : ''}}">
                    <div class="col-md-12">
                        <label>Syarat dan Ketentuan</label><br>
                        <textarea id="editor1" name="isi">{{isset($syarat) ? $syarat->isi : ''}}</textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button class="btn mb-2" style="background: #6C757D; color: #fff; width: 10%">Kembali</button>
                <button class="btn mb-2" type="button" style="background: #007BFF; color: #fff; width: 10%" id="simpan">Simpan</button>
            </div>
        </div>
    </div>
    <div class="other-page"></div>
</div>
@endsection

@push('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".knob").knob();
    });

    var editor = CKEDITOR.replace('editor1', {
		toolbarCanCollapse:false,
	});

    $('#simpan').click(function (e) { 
        e.preventDefault();
        var data = new FormData($('#commentForm')[0]);
        var isi = CKEDITOR.instances.editor1.getData();
        data.append('isi',isi);

        if(isi==''){
            Swal.fire('Peringatan', 'Syarat & Aturan Wajib Diisi.', 'warning');
        }else{
            $.ajax({
                url : "{{route('saveSyaratMcu')}}",
                type: "POST",
                data: data,
                contentType : false,
                processData: false
            }).done(function(data) {
                if (data.code==200) {
                    Swal.fire('Berhasil', data.message, 'success');
                    location.reload()
                }else{
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
        }
    });
</script>
@endpush