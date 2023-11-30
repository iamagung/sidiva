<div class="modal fade" id="modalForm" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="font-size: 14pt; color: #000" class="text-center">Pilih Tenaga Medis</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="formTenagaMedis">
					<input type="hidden" name="id" id="id" value="{{$permintaan->id_permintaan_hc}}">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Layanan Home Care <small style="color: red">*)</small></label>
                            <select name="layanan_id[]" id="layanan_id" class="form-control single-select" multiple="multiple" disabled>
                                <option value="">-Pilih-</option>
                                @if (count($dtLayanan) > 0)
                                    @foreach ($dtLayanan as $key => $l)
                                        <option @if(in_array($l->id_layanan_hc,$setLayanan)) selected @endif value="{{$l->id_layanan_hc}}">{{$l->nama_layanan}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Pilih Tenaga Medis <small style="color: red">*)</small></label>
                            <select name="tenaga_medis_id[]" id="tenaga_medis_id" class="form-control single-select" multiple="multiple" @if($view==1) disabled @endif>
                                <option disabled value="">-Pilih-</option>
                                @if (count($nakes) > 0)
                                    @foreach ($nakes as $key => $nks)
                                        <option @if(in_array($nks->id_tenaga_medis,$setNakes)) selected @endif value="{{$nks->id_tenaga_medis}}">{{$nks->name}}</option>
                                    @endforeach
                                @endif
                            </select>
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
            dropdownParent: $('#formTenagaMedis')
        });
    })();
    $('#modalForm').on('hidden.bs.modal', function() {
        $('.modal-dialog').html('');
    });
    $('#btn-confirm').click(function (e) {
        e.preventDefault();
        var data = new FormData($('#formTenagaMedis')[0]);
        $.ajax({
            url : "{{route('savePermintaanHC')}}",
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
    });
</script>
