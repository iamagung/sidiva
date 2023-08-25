@extends('layouts.index')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .without_ampm::-webkit-datetime-edit-ampm-field {
        display: none;
    }
    input[type=time]::-webkit-clear-button {
        -webkit-appearance: none;
        -moz-appearance: none;
        -o-appearance: none;
        -ms-appearance:none;
        appearance: none;
        margin: -10px; 
    }
    .form-check-input:checked {
		background-color: #5BB75B;
		border-color: #888181;
	}
	.form-check-input:focus{
		box-shadow: none;
	}
	.form-check-input:active{
		filter: brightness(100%);
	}
</style>
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
            <h5>{{$title}} Homecare
                <button type="button" class="btn btn-success btn-sm float-end" id="lihat">LIHAT JADWAL</button>
            </h5>
        </div>
        <div class="card-body">
            <form id="dayForm">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label style="font-weight: bold">Senin</label><br>
                        <div class="form-check form-switch">
                            <input class="form-check-input without_ampm" onchange="cekDay(1)" type="checkbox"  name="senin" id="1status"> Ada
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Dibuka</label>
                        <input type="time" readonly id="buka1" name="buka1" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Ditutup</label>
                        <input type="time" readonly id="tutup1" name="tutup1" class="form-control">
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label style="font-weight: bold">Selasa</label><br>
                        <div class="form-check form-switch">
                            <input class="form-check-input without_ampm" onchange="cekDay(2)" type="checkbox" name="selasa" id="2status"> Ada
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Dibuka</label>
                        <input type="time" readonly id="buka2" name="buka2" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Ditutup</label>
                        <input type="time" readonly id="tutup2" name="tutup2" class="form-control">
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label style="font-weight: bold">Rabu</label><br>
                        <div class="form-check form-switch">
                            <input class="form-check-input without_ampm" onchange="cekDay(3)" type="checkbox" name="rabu" id="3status"> Ada
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Dibuka</label>
                        <input type="time" readonly id="buka3" name="buka3" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Ditutup</label>
                        <input type="time" readonly id="tutup3" name="tutup3" class="form-control">
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label style="font-weight: bold">Kamis</label><br>
                        <div class="form-check form-switch">
                            <input class="form-check-input without_ampm" onchange="cekDay(4)" type="checkbox" name="kamis" id="4status"> Ada
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Dibuka</label>
                        <input type="time" readonly id="buka4" name="buka4" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Ditutup</label>
                        <input type="time" readonly id="tutup4" name="tutup4" class="form-control">
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label style="font-weight: bold">Jum'at</label><br>
                        <div class="form-check form-switch">
                            <input class="form-check-input without_ampm" onchange="cekDay(5)" type="checkbox" name="jumat" id="5status"> Ada
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Dibuka</label>
                        <input type="time" readonly id="buka5" name="buka5" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Ditutup</label>
                        <input type="time" readonly id="tutup5" name="tutup5" class="form-control">
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label style="font-weight: bold">Sabtu</label><br>
                        <div class="form-check form-switch">
                            <input class="form-check-input without_ampm" onchange="cekDay(6)" type="checkbox" name="sabtu" id="6status"> Ada
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Dibuka</label>
                        <input type="time" readonly id="buka6" name="buka6" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Ditutup</label>
                        <input type="time" readonly id="tutup6" name="tutup6" class="form-control">
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label style="font-weight: bold">Minggu</label><br>
                        <div class="form-check form-switch">
                            <input class="form-check-input without_ampm" onchange="cekDay(7)" type="checkbox" name="minggu" id="7status"> Ada
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Dibuka</label>
                        <input type="time" readonly id="buka7" name="buka7" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Ditutup</label>
                        <input type="time" readonly id="tutup7" name="tutup7" class="form-control">
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="col-md-12">
                <button type="button" class="btn mb-2" style="width: 100%; background: #3EC396; color: #fff;" id="simpan">SIMPAN</button>
            </div>
            <div class="col-md-12">
                <button type="button" class="btn mb-2" style="width: 100%; background: #5A6268; color: #fff;" id="refresh">REFRESH</button>
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
    

    function cekDay(hari){
        if ($("#"+hari+"status").is(":checked")) {
            $("#buka"+hari).attr("readonly",false)
            $("#tutup"+hari).attr("readonly",false)
        } else {
            $("#buka"+hari).attr("readonly",true)
            $("#tutup"+hari).attr("readonly",true)
        }
	}

    $('#simpan').click(function (e) { 
        e.preventDefault();

        var data = new FormData($("#dayForm")[0]);

        if ($("#1status").is(":checked") && ($('#buka1').val() == '' || $('#tutup1').val() == '')) {
            Swal.fire({title: 'Peringatan!',text: 'Jam buka atau tutup hari Senin wajib diisi',icon: 'warning',timer: 2000,buttons: false})
        } else if ($("#2status").is(":checked") && ($('#buka2').val() == '' || $('#tutup2').val() == '')) {
            Swal.fire({title: 'Peringatan!',text: 'Jam buka atau tutup hari Selasa wajib diisi',icon: 'warning',timer: 2000,buttons: false})
        } else if ($("#3status").is(":checked") && ($('#buka3').val() == '' || $('#tutup3').val() == '')) {
            Swal.fire({title: 'Peringatan!',text: 'Jam buka atau tutup hari Rabu wajib diisi',icon: 'warning',timer: 2000,buttons: false})
        } else if ($("#4status").is(":checked") && ($('#buka4').val() == '' || $('#tutup4').val() == '')) {
            Swal.fire({title: 'Peringatan!',text: 'Jam buka atau tutup hari Kamis wajib diisi',icon: 'warning',timer: 2000,buttons: false})
        } else if ($("#5status").is(":checked") && ($('#buka5').val() == '' || $('#tutup5').val() == '')) {
            Swal.fire({title: 'Peringatan!',text: 'Jam buka atau tutup hari Jumat wajib diisi',icon: 'warning',timer: 2000,buttons: false})
        } else if ($("#6status").is(":checked") && ($('#buka6').val() == '' || $('#tutup6').val() == '')) {
            Swal.fire({title: 'Peringatan!',text: 'Jam buka atau tutup hari Sabtu wajib diisi',icon: 'warning',timer: 2000,buttons: false})
        } else {
            $.ajax({
                url : "{{route('savePengaturanMcu')}}",
                type: 'POST',
                data: data,
                async: true,
                cache: false,
                contentType: false,
                processData: false
            }).done(function(data) {
                if (data.status == 'success') {
                    Swal.fire('Berhasil', data.message, 'success');
                    location.reload();
                } else {
                    $('#simpan').removeAttr('disabled');
                    Swal.fire('Maaf!', data.message, 'info');
                }
            }).fail(function() {
                Swal.fire("Maaf!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
                $('#simpan').removeAttr('disabled');
            });
        }
    });

    $('#lihat').click(function (e) { 
        e.preventDefault();
        $.get("{{ route('getPengaturanMCU') }}").done(function(result){
            var buka1 = result.jadwal.seninBuka;
            var tutup1 = result.jadwal.seninTutup;
            var buka2 = result.jadwal.selasaBuka;
            var tutup2 = result.jadwal.selasaTutup;
            var buka3 = result.jadwal.rabuBuka;
            var tutup3 = result.jadwal.rabuTutup;
            var buka4 = result.jadwal.kamisBuka;
            var tutup4 = result.jadwal.kamisTutup;
            var buka5 = result.jadwal.jumatBuka;
            var tutup5 = result.jadwal.jumatTutup;
            var buka6 = result.jadwal.sabtuBuka;
            var tutup6 = result.jadwal.sabtuTutup;
            var buka7 = result.jadwal.mingguBuka;
            var tutup7 = result.jadwal.mingguTutup;
            if (result.code == 200) {
                if (buka1 != null && tutup1 != null) {
                    $("#1status").prop("checked", true);
                    $("#buka1").attr("readonly",false)
                    $("#tutup1").attr("readonly",false)
                    $('#buka1').val(buka1);
                    $('#tutup1').val(tutup1);
                }
                if (buka2 != null && tutup2 != null) {
                    $("#2status").prop("checked", true);
                    $("#buka2").attr("readonly",false)
                    $("#tutup2").attr("readonly",false)
                    $('#buka2').val(buka2);
                    $('#tutup2').val(tutup2);
                }
                if (buka3 != null && tutup3 != null) {
                    $("#3status").prop("checked", true);
                    $("#buka3").attr("readonly",false)
                    $("#tutup3").attr("readonly",false)
                    $('#buka3').val(buka3);
                    $('#tutup3').val(tutup3);
                }
                if (buka4 != null && tutup4 != null) {
                    $("#4status").prop("checked", true);
                    $("#buka4").attr("readonly",false)
                    $("#tutup4").attr("readonly",false)
                    $('#buka4').val(buka4);
                    $('#tutup4').val(tutup4);
                }
                if (buka5 != null && tutup5 != null) {
                    $("#5status").prop("checked", true);
                    $("#buka5").attr("readonly",false)
                    $("#tutup5").attr("readonly",false)
                    $('#buka5').val(buka5);
                    $('#tutup5').val(tutup5);
                }
                if (buka6 != null && tutup6 != null) {
                    $("#6status").prop("checked", true);
                    $("#buka6").attr("readonly",false)
                    $("#tutup6").attr("readonly",false)
                    $('#buka6').val(buka6);
                    $('#tutup6').val(tutup6);
                }
                if (buka7 != null && tutup7 != null) {
                    $("#7status").prop("checked", true);
                    $("#buka7").attr("readonly",false)
                    $("#tutup7").attr("readonly",false)
                    $('#buka7').val(buka7);
                    $('#tutup7').val(tutup7);
                }
                Swal.fire({
                    title: 'Berhasil',
                    text: result.message,
                    icon: 'success',
                    timer: 1000,
                    buttons: false,
                })
            } else {
                Swal.fire({
                    title: 'Maaf',
                    text: result.message,
                    icon: 'error',
                    timer: 1000,
                    buttons: false,
                })
            }
        });
    });

    $('#refresh').click(function (e) { 
        e.preventDefault();
        location.reload()
    });
</script>
@endpush