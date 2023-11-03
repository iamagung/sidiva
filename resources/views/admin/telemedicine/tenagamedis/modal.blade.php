<div class="modal fade" id="modalFormNakes" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="font-size: 14pt" class="text-center pt-2">{{$title}} Telemedicine</h4>
				<button type="button" data-bs-dismiss="modal" class="btn btnCancel"><i class='bx bx-x-circle' style="font-size: 30px; width: 15px;" undefined ></i></button>
			</div>
			<div class="modal-body">
				<form id="formTenagaMedis">
                    <?php
                        function rupiah($money){
                            echo number_format($money,0,",",".");
                        }
                    ?>
                    <input type="hidden" name="id" id="id" value="{{!empty($tenaga_medis)?$tenaga_medis->id_tenaga_medis:''}}">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Jenis Tenaga Kesehatan <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-md-12">
                            <select name="jenis_nakes" id="jenis_nakes" class="form-control select2">
                                <option value="">-Pilih-</option>
                                @if (count($jenisNakes) > 0)
                                    @foreach ($jenisNakes as $key => $tm)
                                        <option @if($tenaga_medis && $tenaga_medis->jenis_nakes==$tm) selected @endif value="{{$tm}}">{{$tm}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Pilih Poli Layanan <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-md-12">
                            <select name="poli_id" id="poli_id" class="form-control select2">
                                <option value="">-Pilih-</option>
                                @if (count($getPoliLayanan) > 0)
                                    @foreach ($getPoliLayanan as $key => $tm)
                                        <option @if($tenaga_medis && $tenaga_medis->poli_id==$tm->id_poli) selected @endif value="{{$tm->id_poli}}">{{$tm->nama_poli}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Pilih Tenaga Medis <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-md-12">
                            <select name="nakes_id" id="nakes_id" class="form-control select2">
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3" id="tarif_form">
                        <div class="col-md-12">
                            <label>Tarif <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-md-12">
                            <input type="text" id="tarif" name="tarif" class="form-control" placeholder="Tarif" @if($tenaga_medis ) value="{{$tenaga_medis->tarif}}" @else value="0" @endif data-prefix="Rp " data-thousands="." data-decimal="," data-precision="0" data-allow-zero="false">
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
                            <span class="btn btn-sm text-white" style="background-color: #7F0E89;" onclick="addJadwal()">Tambah Jadwal</span>
                        </div>
                        <div class="col-md-12 mt-3" id="jadwal_area">
                            <table class="table table-striped">
                                <tbody id="table_jadwal">
                                    @if($jadwal_tenaga_medis)
                                    @foreach($jadwal_tenaga_medis as $key => $jadwal)
                                    <tr>
                                        <td>
                                            <input type="time" name="jadwal[{{$key}}][awal]" id="jadwal[{{$key}}][akhir]" value="{{$jadwal->jam_awal}}" class="d-none">
                                            <span>{{date_format(date_create($jadwal->jam_awal), 'H:i')}}</span>
                                        </td>
                                        <td>
                                            <input type="time" name="jadwal[{{$key}}][akhir]" id="jadwal[{{$key}}][akhir]" value="{{$jadwal->jam_akhir}}" class="d-none">
                                            <span>{{date_format(date_create($jadwal->jam_akhir), 'H:i')}}</span>
                                        </td>
                                        <td>
                                            <button class='btn btn-sm btn-danger' title='Delete' onclick='hapusJadwal(this)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>No. Telepon <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-md-12">
                            <input type="text" id="no_telepon" name="no_telepon" class="form-control" placeholder="081234567890" @if($tenaga_medis) value="{{$tenaga_medis->no_telepon ? $tenaga_medis->no_telepon : ''}}" @endif>
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
    $('#modalFormNakes').modal('show');
    $('document').ready(function () {
        // $("#tarif").maskMoney({thousands:'.', decimal:',', allowZero:false, prefix: 'Rp ',precision: 0});
        loadNakes();
        $("#tarif").maskMoney();
        $("#tarif").maskMoney('mask');
        var jenisNakes = '{{$tenaga_medis?$tenaga_medis->jenis_nakes:""}}';
        jadwalMedis = <?php echo json_encode($jadwal_tenaga_medis); ?>;
        // var jadwalDokter = []
        // jadwalMedis.forEach(element => {
        //     jadwalDokter.push({jam_awal: element.jam_awal, jam_akhir: element.jam_akhir});
        // });
        // jadwalMedis = jadwalDokter;
        // renderJadwalToTable();
        console.log(jadwalMedis);
        if(jenisNakes == 'perawat') {
            $('#tarif_form').hide();
            $('#jadwal_form').hide();
        }
    })
    $(".select2").select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalFormNakes')
    });
    $('#modalFormNakes').on('hidden.bs.modal', function() {
        $('.modal-dialog').html('');
    });

    $('#btn-confirm').click(function (e) { 
        e.preventDefault();
        
            var data = new FormData($('#formTenagaMedis')[0]);
            data.set('tarif',Number($("#tarif").val().replace(/[^0-9,-]+/g,"")));
            $.ajax({
                url : "{{route('saveTenagaMedisTelemedicine')}}",
                type: 'POST',
                data: data,
                async: true,
                cache: false,
                contentType: false,
                processData: false
            }).done(function(data) {
                if (data.status == 'success') {
                    Swal.fire('Berhasil', data.message, 'success');
                    $('#modalFormNakes').modal('hide');
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

    $('#jenis_nakes').change(() => {
        loadNakes();
    });

    function loadNakes() {
        var jenis = $('#jenis_nakes').val();
        
        // jika edit nakes
		// Selected nakes
		var selectedNakes = "{{ !empty($tenaga_medis) ? $tenaga_medis->nakes_id:'' }}";
        var jenisNakes = '';
        $.post("{{route('getNakesTelemedicine')}}",{jenis:jenis,selectedNakes:selectedNakes})
        .done(function( data ) {
            var nama = '<option value="">-Pilih-</option>';
            if(data.metaData.code==200){
                if(data.response.length>0){
                    $.each(data.response,function(v,k){
                        nama+='<option value="'+k.id+'">'+k.name+'</option>';
                        jenisNakes = k.level_user;
                    });
                }

                $('#nakes_id').html(nama);
                if(selectedNakes && (jenisNakes == jenis)){
                    $('#nakes_id').val(selectedNakes).trigger('change');
                }
            }
        })
        .fail(function(error) {
            var nama = '<option value="">-Pilih-</option>';
            $('#nakes_id').html(nama);
        });
    }

    $('#jenis_nakes').on('change', () => {
        if($('#jenis_nakes option:selected').text() == 'perawat') {
            $('#tarif_form').hide();
            $('#jadwal_form').hide();

        } else {
            $('#tarif_form').show();
            $('#jadwal_form').show();
        }
    })

    function addJadwal() {
        if($('#jam_awal').val() == '' || $('#jam_awal').val() == null || $('#jam_akhir').val() == '' || $('#jam_akhir').val() == null) {
            Swal.fire('Maaf!', 'Format jam tidak sesuai', 'warning');
        } else {
            jadwalMedis.push({jam_awal:$('#jam_awal').val(),jam_akhir:$('#jam_akhir').val()});
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
        jadwalMedis.forEach(function (value, index) {
            var html = '<tr><td>'+
                '<input class="d-none" type="time" name="jadwal['+index+'][awal]" id="jadwal['+index+'][awal]" value="'+value.jam_awal+'">'+
                '<span>'+value.jam_awal.substring(0,5)+'</span></td><td>'+
                '<input class="d-none" type="time" name="jadwal['+index+'][akhir]" id="jadwal['+index+'][akhir]" value="'+value.jam_akhir+'">'+
                '<span>'+value.jam_akhir.substring(0,5)+'</span></td>'+
                '<td><a class="btn btn-danger" title="Delete" onclick="hapusJadwal($(this).parent().parent(), '+index+')"><i class="fadeIn animated bx bxs-trash" aria-hidden="true"></i></a>'
            '</td></tr>';
            $('#table_jadwal').append(html);
        });
    }
</script>