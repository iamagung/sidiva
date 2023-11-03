<div class="modal fade" id="modalFormTelemedicine" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="font-size: 14pt" class="text-center pt-2">PILIH TENAGA MEDIS TELEMEDICINE</h4>
				<button type="button" class="btn btnClose" data-bs-dismiss="modal"><i class='bx bx-x-circle' style="font-size: 30px; width: 15px;" undefined ></i></button>
			</div>
			<div class="modal-body">
				<form id="formTenagaMedis">
					<input type="hidden" name="id" id="id" value="{{$permintaan->id_permintaan_telemedicine}}">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Nama Dokter <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="nama_dokter" id="nama_dokter" value="{{ !empty($nama_dokter->name) ? $nama_dokter->name : ''}}" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Pilih Perawat <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-md-12">
                            <select name="perawat_id" id="perawat_id" class="form-control select2" @if ($form_detail) disabled @endif>
                                <option value="">-Pilih-</option>
                                @if (count($tenagaMedis) > 0)
                                    @foreach ($tenagaMedis as $key => $tm)
                                        <option value="{{$tm->id_tenaga_medis}}" @if ($permintaan->perawat_id == $tm->id_tenaga_medis) selected @endif>{{$tm->nama_nakes}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Pilih Jadwal <span class="text-danger">*)</span></label>
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="tanggal_kunjungan" id="tanggal_kunjungan" value="{{ !empty($permintaan->tanggal_kunjungan) ? $permintaan->tanggal_kunjungan : ''}}" @if ($form_detail) disabled @endif>
                        </div>
                        @php
                            if (!empty($permintaan->jadwal_dokter)) {
                                $jadwal_dokter = explode('-',$permintaan->jadwal_dokter);
                                if(count($jadwal_dokter) > 0) {
                                    $jadwal_dokter = $jadwal_dokter[0];
                                    // $jadwal_dokter = '10:00';
                                } else {
                                    $jadwal_dokter = '';
                                }
                            } else {
                                $jadwal_dokter = '';
                            }
                        @endphp
                        <div class="col-md-6">
                            <input type="time" class="form-control" name="jadwal_dokter" id="jadwal_dokter" value="{{$jadwal_dokter}}" @if ($form_detail) disabled @endif>
                        </div>
                    </div>
				</form>
			</div>
			<div class="modal-footer float-end">
                <button type="button" class="btn btn-sm btn-secondary" class="close" data-bs-dismiss="modal">KEMBALI</button>
				@if (!$form_detail)
                <button type="button" class="btn btn-sm btn-success" id="btn-confirm">PROSES</button>
                @endif
			</div>
		</div>

	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    // $('#modalForm').find('.modal-dialog').css({
    //     'width': '60%'
    // });
    $('#modalFormTelemedicine').modal('show');
    $(".select2").select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#formTenagaMedis')
    }); 
    $('#modalFormTelemedicine').on('hidden.bs.modal', function() {
        $('.modal-dialog').html('');
    });
    $('#btn-confirm').click(function (e) { 
        e.preventDefault();
        
            var data = new FormData($('#formTenagaMedis')[0]);
            $.ajax({
                url : "{{route('savePermintaanTelemedicine')}}",
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
                        for(let value of Object.values(data.message)){
                            var name = value[0];
                            break;
                        }

                        $('#simpan').removeAttr('disabled');
                        Swal.fire('Maaf!', name, 'info');;
                    }
                }
            }).fail(function() {
                Swal.fire("MAAF!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
                $('#simpan').removeAttr('disabled');
            });
    });
</script>