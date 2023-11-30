<div class="modal fade" id="modalFormJadwal" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="font-size: 14pt" class="text-center pt-2">Jadwal Dokter Telemedicine</h4>
				<button type="button" data-bs-dismiss="modal" class="btn btnCancel"><i class='bx bx-x-circle' style="font-size: 30px; width: 15px;" undefined ></i></button>
			</div>
			<div class="modal-body">
				<form id="formJadwalMedis">
                    <input type="hidden" name="id" id="id" value="{{!empty($tenaga_medis)?$tenaga_medis->id_tenaga_medis:''}}">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Hari</label>
                        </div>
                        <div class="col-md-12">
                            <select name="hari" id="hari" class="form-control select2">
                                <option value="0">Minggu</option>
                                <option value="1">Senin</option>
                                <option value="2">Selasa</option>
                                <option value="3">Rabu</option>
                                <option value="4">Kamis</option>
                                <option value="5">Jumat</option>
                                <option value="6">Sabtu</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12 mt-3" id="jadwal_area">
                            <table class="table table-striped">
                                <tbody id="table_jadwal">
                                    <?php
                                        $no=1;
                                    ?>
                                    @if($jadwal_medis)
                                    @foreach($jadwal_medis as $key => $jadwal)
                                    <tr id="jadwal_{{$key}}" @if ($jadwal->hari != 0)
                                        style="display: none;"
                                    @endif>
                                        <td>
                                            <input type="text" name="jadwal[{{$key}}][hari]" id="jadwal[{{$key}}][hari]" value="{{$jadwal->hari}}" class="d-none">
                                            <input type="time" name="jadwal[{{$key}}][awal]" id="jadwal[{{$key}}][awal]" value="{{$jadwal->jam_awal}}" class="d-none">
                                            <span>{{$no++ . '. ' . date_format(date_create($jadwal->jam_awal), 'H:i')}}-</span>
                                            <input type="time" name="jadwal[{{$key}}][akhir]" id="jadwal[{{$key}}][akhir]" value="{{$jadwal->jam_akhir}}" class="d-none">
                                            <span>{{date_format(date_create($jadwal->jam_akhir), 'H:i')}}</span>
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-danger" title="Delete" onclick="hapusJadwal($(this).parent().parent(), '{{$key}}')"><i class="fadeIn animated bx bxs-trash" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-3" id="jadwal_form">
                        <div class="col-md-12">
                            <label>Jadwal <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-sm-4">
                            <input type="time" id="jam_awal" name="jam_awal" class="form-control" placeholder="Jam Awal">
                        </div>
                        <div class="col-sm-4">
                            <input type="time" id="jam_akhir" name="jam_akhir" class="form-control" placeholder="Jam Akhir">
                        </div>
                        <div class="col-sm-4">
                            <span class="btn btn-sm text-white" style="background-color: #9EAA17;
                            ;" onclick="addJadwal()">Tambah Jadwal</span>
                        </div>
                    </div>
				</form>
			</div>
			<div class="modal-footer float-end">
                <button type="button" class="btn btn-sm btn-secondary" class="close" data-bs-dismiss="modal">KEMBALI</button>
				<button type="button" class="btn btn-sm btn-primary" id="btn-confirm">SIMPAN</button>
			</div>
		</div>

	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- format rupiah --}}
<script src="https://cdn.jsdelivr.net/npm/jquery-maskmoney@3.0.2/dist/jquery.maskMoney.min.js"></script>
{{-- <script type="text/javascript" src="{{asset('assets/js/jquery.maskMoney.js')}}"></script> --}}
<script type="text/javascript">
    var jadwalMedis = [];
    $('#modalFormJadwal').modal('show');
    $('document').ready(function () {
        var jenisNakes = '{{$tenaga_medis?$tenaga_medis->jenis_nakes:""}}';
        jadwalMedis = <?php echo json_encode($jadwal_medis); ?>;
        // var jadwalDokter = []
        // jadwalMedis.forEach(element => {
        //     jadwalDokter.push({jam_awal: element.jam_awal, jam_akhir: element.jam_akhir});
        // });
        // jadwalMedis = jadwalDokter;
        // renderJadwalToTable();
        console.log(jadwalMedis);
    })
    $(".select2").select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalFormJadwal')
    });
    $('#modalFormJadwal').on('hidden.bs.modal', function() {
        $('.modal-dialog').html('');
    });
    $('#hari').change(function() {
        renderJadwalToTable();
    });

    $('#btn-confirm').click(function (e) {
        e.preventDefault();

            var data = new FormData($('#formJadwalMedis')[0]);
            $.ajax({
                url : "{{route('saveJadwalTenagaMedisTelemedicine')}}",
                type: 'POST',
                data: data,
                async: true,
                cache: false,
                contentType: false,
                processData: false
            }).done(function(data) {
                if (data.status == 'success') {
                    Swal.fire('Berhasil', data.message, 'success');
                    $('#modalFormJadwal').modal('hide');
                    location.reload();
                } else if(data.status == 'error'){
                    if(data.code == 500){
                        $('#simpan').removeAttr('disabled');
                        Swal.fire('Maaf!', data.message, 'info');
                    } else {
                        for(let value of Object.values(data.message)){
                            var name = value[0];
                            break;
                        }

                        $('#simpan').removeAttr('disabled');
                        Swal.fire('Maaf!', name, 'info');
                    }
                }
            }).fail(function() {
                Swal.fire("MAAF!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
                $('#simpan').removeAttr('disabled');
            });
    });

    function addJadwal() {
        if($('#jam_awal').val() == '' || $('#jam_awal').val() == null || $('#jam_akhir').val() == '' || $('#jam_akhir').val() == null) {
            Swal.fire('Maaf!', 'Format jam tidak sesuai', 'warning');
        } else {
            jadwalMedis.push({jam_awal:$('#jam_awal').val(),jam_akhir:$('#jam_akhir').val(),hari:$('#hari').val()});
            console.log(jadwalMedis);
            renderJadwalToTable();
            $('#jam_awal').val('');
            $('#jam_akhir').val('');
        }
    }

    function hapusJadwal(button, index) {
        button.remove();
        jadwalMedis.splice(index, 1);
        console.log(jadwalMedis);
        renderJadwalToTable();
    }

    function renderJadwalToTable() {
        $('#table_jadwal').html('');
        var hari = $('#hari').val();
        var no = 1;
        jadwalMedis.forEach(function (value, index) {
            var html = '<tr id="jadwal_'+index+'"><td>'+
                '<input class="d-none" type="text" name="jadwal['+index+'][hari]" id="jadwal['+index+'][hari]" value="'+value.hari+'">'+
                '<input class="d-none" type="time" name="jadwal['+index+'][awal]" id="jadwal['+index+'][awal]" value="'+value.jam_awal+'">'+
                '<span>'+no+'. '+value.jam_awal.substring(0,5)+'-</span>'+
                '<input class="d-none" type="time" name="jadwal['+index+'][akhir]" id="jadwal['+index+'][akhir]" value="'+value.jam_akhir+'">'+
                '<span>'+value.jam_akhir.substring(0,5)+'</span></td><td class="text-end">'+
                '<a class="btn btn-sm btn-danger" title="Delete" onclick="hapusJadwal($(this).parent().parent(), '+index+')"><i class="fadeIn animated bx bxs-trash" aria-hidden="true"></i></a>'
            '</td></tr>';
            $('#table_jadwal').append(html);
            if(hari != value.hari){
                $('#jadwal_'+index).hide();
            } else {
                no++;
            }
        });
    }
</script>
