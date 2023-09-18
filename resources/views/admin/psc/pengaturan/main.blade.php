@extends('layouts.index')

@push('style')
@endpush
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
        <div class="card-body">
            <form id="commentForm">
                <div class="row mb-3">
                    <input type="hidden" name="id" id="id" value="{{ !empty($syarat) ? $syarat->id_syarat_mcu : ''}}">
                    <div class="col-md-12">
                        <label>Pengaturan</label><br>
                        <div class="accordion" id="accordionExample" style="margin-top: 15px">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="background-color: #8F49A9; color: white;">
                                    PENGATURAN PELAYANAN
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <form>
                                            <div class="mb-3 col-md-3">
                                                <label for="exampleInputEmail1" class="form-label">Jarak Maksimum Pelayanan (KM)</label>
                                                <input class="form-control">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item" style="margin-top: 15px">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="background-color: #8F49A9; color: white;">
                                        PENGATURAN BIAYA AMBULANCE
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <form>
                                        <div class="mb-3 col-md-3">
                                            <label for="exampleInputEmail1" class="form-label">Perhitungan (Per-1KM)</label>
                                            <input class="form-control">
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item" style="margin-top: 15px">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="background-color: #8F49A9; color: white;">
                                        INFORMASI PEMBATALAN AMBULANCE
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <form>
                                            <div class="mb-3">
                                                <textarea id="editor1" name="isi">{{isset($syarat) ? $syarat->isi : ''}}</textarea>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                              </div>
                        {{-- <div class="accordion" id="accordionExample" style="margin-top: 15px">
                            <div class="accordion-item">
                              <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseOne" style="background-color: #8F49A9; color: white;">
                                  PENGATURAN BIAYA AMBULANCE
                                </button>
                              </h2>
                              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <form>
                                    <div class="mb-3 col-md-3">
                                        <label for="exampleInputEmail1" class="form-label">Perhitungan (Per-1KM)</label>
                                        <input class="form-control">
                                    </div>
                                    </form>
                                </div>
                              </div>
                            </div>
                        </div> --}}
                        {{-- <div class="accordion" id="accordionExample" style="margin-top: 15px">
                            <div class="accordion-item">
                              <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="background-color: #8F49A9; color: white;">
                                  INFORMASI PEMBATALAN AMBULANCE
                                </button>
                              </h2>
                              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <form>
                                    <div class="mb-3">
                                        <textarea id="editor1" name="isi">{{isset($syarat) ? $syarat->isi : ''}}</textarea>
                                    </div>
                                    </form>
                                </div>
                              </div>
                            </div>
                        </div> --}}
                        </div>                        
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