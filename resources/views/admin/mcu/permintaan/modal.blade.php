<div class="modal fade" id="modalForm" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="font-size: 14pt; color: #000" class="text-center">Jadwal Medical Check Up</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="formSave">
					<input type="hidden" name="id" id="id" value="{{$permintaan->id_permintaan}}">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Nama Pemeriksaan MCU <small>*</small></label>
                            <select name="layanan_id[]" id="layanan_id" class="form-control single-select" multiple="multiple" disabled>
                                <option value="">-Pilih-</option>
                                @if (count($dtLayanan) > 0)
                                    @foreach ($dtLayanan as $key => $l)
                                        <option @if(in_array($l->id_layanan,$setLayanan)) selected @endif value="{{$l->id_layanan}}">{{$l->nama_layanan}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tempat Pelayanan <small>*</small></label>
                            <input type="text" id="tempat_mcu" name="tempat_mcu" class="form-control" value="{{ !empty($permintaan->tempat_mcu)&&$permintaan->tempat_mcu=='RS'?'Rumah Sakit':'Tempat Pasien'}}" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label>Pilih Jadwal</label>
                        <div class="col-md-6">
                            <input type="date" name="date_choice" id="date_choice" class="form-control" value="{{!empty($permintaan->date_mcu)?$permintaan->date_mcu:''}}" @if($view==1) disabled @endif>
                        </div>
                        <div class="col-md-6">
                            <input type="time" name="time_choice" id="time_choice" class="form-control" value="{{!empty($permintaan->time_mcu)?$permintaan->time_mcu:''}}" @if($view==1) disabled @endif>
                        </div>
                    </div>
                    @if ($view==1)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Status</label>
                            <input type="text" id="status" name="status" value="PROSES" class="form-control" readonly>
                        </div>
                    </div>
                    @endif
				</form>
			</div>
			<div class="modal-footer float-end">
                <button type="button" class="btn btn-sm btn-secondary" class="close" data-bs-dismiss="modal">KEMBALI</button>
				@if ($view!=1)
                <button type="button" class="btn btn-sm btn-success" id="btn-confirm">PROSES</button>
                @endif
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
    var onLoad = (function() {
        $('#modalForm').find('.modal-dialog').css({
            'width': '60%'
        });
        $('#modalForm').modal('show');
        $(".single-select").select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalForm')
        });
    })();
    $('#modalForm').on('hidden.bs.modal', function() {
        $('.modal-dialog').html('');
    });
    $('#btn-confirm').click(function (e) {
        e.preventDefault();
        var data = new FormData($('#formSave')[0]);
        var date = $('#date_choice').val();
        var time = $('#time_choice').val();
        if (!date) {
            Swal.fire('Maaf', 'Tanggal wajib diisi', 'warning');
        } else if (!time) {
            Swal.fire('Maaf', 'Waktu wajib diisi', 'warning');
        } else {
            $.ajax({
                url : "{{route('savePermintaanMcu')}}",
                type: 'POST',
                data: data,
                async: true,
                cache: false,
                contentType: false,
                processData: false
            }).done(function(data) {
                if (data.status == 'success') {
                    Swal.fire('Berhasil', data.message, 'success');
                    $('#modalForm').modal('hide');
                    location.reload();
                } else {
                    if(data.code == 500){
                        $('#simpan').removeAttr('disabled');
                        Swal.fire('Maaf!', data.message, 'info');
                    } else {
                        var n = 0
                        for(key in data.message){
                            var  name = key
                            if(name=='tenaga_medis_id'){name='Tenaga Medis'}
                            n++
                        }
                        Swal.fire('Maaf!', name+' Wajib Diisi', 'info');
                    }
                }
            }).fail(function() {
                Swal.fire("MAAF!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
            });
        }
    });
</script>
