<div class="modal fade" id="modalForm" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" style="padding:15px 50px; background: #25720A;">
				<h4 style="font-size: 14pt" class="text-center text-white">PILIH TENAGA MEDIS HOME CARE</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="formTenagaMedis">
					<input type="hidden" name="id" id="id" value="{{$permintaan->id_permintaan_hc}}">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Layanan Home Care</label>
                            <select name="layanan_id" id="layanan_id" class="form-control select2">
                                <option value="">-Pilih-</option>
                                @if (count($dtLayanan) > 0)
                                    @foreach ($dtLayanan as $key => $l)
                                        <option @if($l->id_layanan_hc == $layanan->id_layanan_hc) selected @endif value="{{$l->id_layanan_hc}}">{{$l->nama_layanan}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @if ($view!=1)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label><input type="checkbox" name="btnEditLayanan" id="btnEditLayanan"> Edit Layanan</label>
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Pilih Tenaga Medis</label>
                            <select name="tenaga_medis_id[]" id="tenaga_medis_id" class="form-control select2" multiple="multiple">
                                <option value="">-Pilih-</option>
                                @if (count($nakes) > 0)
                                    @foreach ($nakes as $key => $nks)
                                        <option value="{{$nks->id_tenaga_medis}}">{{$nks->name}}</option>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    var onLoad = (function() {
        $('#modalForm').find('.modal-dialog').css({
            'width': '60%'
        });
        $('#modalForm').modal('show');
        $(".select2").select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalForm')
        });
        var view = "{{$view}}";
        if (view==1) { //Jika klik detail
            var arrNakes = @json($selectedNakes);
            $('#layanan_id').prop("disabled", true);
            $('#tenaga_medis_id').prop("disabled", true);
            $('#tenaga_medis_id').val(arrNakes);
            $('#tenaga_medis_id').trigger('change');
        } else {
            $('#layanan_id').prop("disabled", false);
            $('#tenaga_medis_id').prop("disabled", false);
        }
    })();
    $('#modalForm').on('hidden.bs.modal', function() {
        $('.modal-dialog').html('');
    });
    $('#btnEditLayanan').click(function (e) { 
        e.preventDefault();
        if ($(this).is(':checked')) {
            $('#layanan_id').prop("disabled", false);
        }else{
            $('#layanan_id').prop("disabled", true);
        }
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
                            else if(name=='layanan_id'){name='Layanan'}
                            n++
                        }
                        $('#simpan').removeAttr('disabled');
                        Swal.fire('Maaf!', name+' Wajib Diisi', 'info');
                    }
                }
            }).fail(function() {
                Swal.fire("MAAF!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
                $('#simpan').removeAttr('disabled');
            });
    });
    $('#btn-selesaikan').click(function (e) { 
        e.preventDefault();
        alert('Pengembangan')
    });
</script>