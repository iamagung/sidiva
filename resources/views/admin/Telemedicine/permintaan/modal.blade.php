<div class="modal fade" id="modalForm" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" style="padding:15px 50px; background: #25720A;">
				<h4 style="font-size: 14pt" class="text-center text-white">PILIH TENAGA MEDIS TELEMEDICINE</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="formTenagaMedis">
					<input type="hidden" name="id" id="id" value="{{$permintaan->id_permintaan_hc}}">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Layanan Telemedicine</label>
                            <select name="layanan_id" id="layanan_id" class="form-control select2" disabled>
                                <option value="">-Pilih-</option>
                                @if (count($getLayanan) > 0)
                                    @foreach ($getLayanan as $key => $l)
                                        <option @if($l->id_layanan_hc == $layanan->id_layanan_hc) selected @endif value="{{$l->id_layanan_hc}}">{{$l->nama_layanan}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Paket Telemedicine</label>
                            <select name="paket_id" id="paket_id" class="form-control select2" disabled>
                                <option value="">-Pilih-</option>
                                @if (count($getPaket) > 0)
                                    @foreach ($getPaket as $key => $p)
                                        <option @if($p->id_paket_hc == $paket->id_paket_hc) selected @endif value="{{$p->id_paket_hc}}">{{$p->nama_paket}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Pilih Tenaga Medis</label>
                            <select name="tenaga_medis_id" id="tenaga_medis_id" class="form-control select2">
                                <option value="">-Pilih-</option>
                                @if (count($tenagaMedis) > 0)
                                    @foreach ($tenagaMedis as $key => $tm)
                                        <option value="{{$tm->id_tenaga_medis}}">{{$tm->nama}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
					{{-- <div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="form-group mb-3">
                                <label>Layanan Telemedicine</label>
                                <select name="layanan_id" id="layanan_id" class="form-control select2" disabled>
                                    <option value="">-Pilih-</option>
                                    @if (count($getLayanan) > 0)
                                        @foreach ($getLayanan as $key => $l)
                                            <option @if($l->id_layanan_hc == $layanan->id_layanan_hc) selected @endif value="{{$l->id_layanan_hc}}">{{$l->nama_layanan}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label>Paket Telemedicine</label>
                                <select name="paket_id" id="paket_id" class="form-control select2" disabled>
                                    <option value="">-Pilih-</option>
                                    @if (count($getPaket) > 0)
                                        @foreach ($getPaket as $key => $p)
                                            <option @if($p->id_paket_hc == $paket->id_paket_hc) selected @endif value="{{$p->id_paket_hc}}">{{$p->nama_paket}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label>Pilih Tenaga Medis</label>
                                <select name="tenaga_medis_id" id="tenaga_medis_id" class="form-control select2">
                                    <option value="">-Pilih-</option>
                                    @if (count($tenagaMedis) > 0)
                                        @foreach ($tenagaMedis as $key => $tm)
                                            <option value="{{$tm->id_tenaga_medis}}">{{$tm->nama}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
						</div>
					</div> --}}
				</form>
			</div>
			<div class="modal-footer float-end">
                <button type="button" class="btn btn-sm btn-secondary" class="close" data-bs-dismiss="modal">KEMBALI</button>
				<button type="button" class="btn btn-sm btn-success" id="btn-confirm">PROSES</button>
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
            theme: 'bootstrap-5'
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
                } else if(data.status == 'error'){
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

                        $('#simpan').removeAttr('disabled');
                        Swal.fire('Maaf!', name+' Wajib Diisi', 'info');
                    }
                }
            }).fail(function() {
                Swal.fire("MAAF!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
                $('#simpan').removeAttr('disabled');
            });
    });
</script>