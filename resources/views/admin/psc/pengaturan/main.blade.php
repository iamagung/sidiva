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
        <form id="commentForm">
            <div class="card-body">
                <div class="row mb-3">
                    <input type="hidden" name="id" id="id" value="{{ !empty($pengaturan) ? $pengaturan->id_pengaturan_ambulance : ''}}">
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
                                        {{-- <form> --}}
                                            <div class="mb-3 col-md-3">
                                                <label for="exampleInputEmail1" class="form-label">Jarak Maksimum Pelayanan (KM)</label>
                                                <input class="form-control" type="text" id="jarak_maksimal" name="jarak_maksimal" value="{{!empty($pengaturan)?$pengaturan->jarak_maksimal:''}}">
                                            </div>
                                        {{-- </form> --}}
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
                                        {{-- <form> --}}
                                        <div class="mb-3 col-md-3">
                                            <label for="exampleInputEmail1" class="form-label">Perhitungan (Per-1KM)</label>
                                            {{-- <input class="form-control" type="text" id="biaya_per_km" name="biaya_per_km" value="{{!empty($pengaturan)?$pengaturan->biaya_per_km:''}}"> --}}
                                            <input type="text" name="biaya_per_km" id="biaya_per_km" class="form-control" autocomplete="off"
                                                onkeyup="ubahFormat(this)" placeholder="Rp.xx.xxx" value="Rp. <?php echo number_format(!empty($pengaturan) ? $pengaturan->biaya_per_km : 0, 0, ',', '.'); ?>">
                                        </div>
                                        {{-- </form> --}}
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
                                        {{-- <form> --}}
                                            <div class="mb-3">
                                                <textarea id="editor1" name="informasi_pembatalan">{{isset($pengaturan) ? $pengaturan->informasi_pembatalan : ''}}</textarea>
                                            </div>
                                        {{-- </form> --}}
                                    </div>
                                </div>
                              </div>
                        </div>                        
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <button class="btn mb-2" style="background: #6C757D; color: #fff; width: 10%" id="kembali">Kembali</button>
                    <button class="btn mb-2" type="button" style="background: #007BFF; color: #fff; width: 10%" id="simpan">Simpan</button>
                </div>
            </div>
        </form>
        
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
        var jarak_maksimal = $('#jarak_maksimal').val();
        var biaya_per_km = $('#biaya_per_km').val();
        var informasi_pembatalan = CKEDITOR.instances.editor1.getData();
        data.append('informasi_pembatalan',informasi_pembatalan);

        if(!jarak_maksimal){
            Swal.fire('Peringatan', 'Jarak Maksimal Wajib Diisi.', 'warning');
        }else if(!biaya_per_km){
            $('#biaya_per_km').val(formatRupiah(biaya, 'Rp. '));
            Swal.fire('Peringatan', 'Biaya Wajib Diisi.', 'warning');
        }else if(informasi_pembatalan==''){
            Swal.fire('Peringatan', 'Informasi Pembatalan Wajib Diisi.', 'warning');
        }else{
            $.ajax({
                url : "{{route('savePengaturanPsc')}}",
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
    function formatRupiah(angka, prefix) {
		var number_string = angka.toString().replace(/[^,\d]/g, "");
		split = number_string.split(",");
		sisa = split[0].length % 3;
		rupiah = split[0].substr(0, sisa);
		ribuan = split[0].substr(sisa).match(/\d{3}/gi);
		// tambahkan titik jika yang di input sudah menjadi angka ribuan
		if (ribuan) {
			separator = sisa ? "." : "";
			rupiah += separator + ribuan.join(".");
		}
		rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
		return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
	}
    function ubahFormat(val) {
		$('#biaya_per_km').val(formatRupiah(val.value, 'Rp. '))
	}
    $('#kembali').click(function (e) { 
        e.preventDefault();
        window.history.back();
    });
</script>
@endpush