<div class="row">
    <div class="col-md-12">
        <form id="form-data" class="card">
            <div class="card-header">
                <h5>{{$title}}</h5>
            </div>
            <div class="card-body">
                <input type="hidden" name="id" id="id" value="{{ !empty($tenaga_medis->id_tenaga_medis) ? $tenaga_medis->id_tenaga_medis : ''}}">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label>Nama</label>
                    </div>
                    <div class="col-md-10">
                        {{-- <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama" autocomplete="off"
                        value="{{ !empty($tenaga_medis->nama) ? $tenaga_medis->nama : ''}}"> --}}
                        <select name="kode_dokter" id="kode_dokter" class="form-control select2">
                            <option value="">-Pilih-</option>
                            @if ($dokter->count() != 0)
                                @foreach ($dokter as $key => $dok)
                                <option @if(!empty($tenaga_medis->kode_dokter) && $tenaga_medis->kode_dokter==$dok->setupall_id) selected @endif value="{{$dok->setupall_id}}">{{$dok->nilaichar}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                {{-- <div class="row mb-3">
                    <div class="col-md-2">
                        <label>No. Telepon</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="telepon" id="telepon" class="form-control" placeholder="08xxxxxxxx" autocomplete="off"
                        value="{{ !empty($tenaga_medis->telepon) ? $tenaga_medis->telepon : ''}}">
                    </div>
                </div> --}}
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label>Jenis Layanan</label>
                    </div>
                    <div class="col-md-10">
                        <select name="layanan_id" id="layanan_id" class="form-control select2">
                            <option value="">- Pilih -</option>
                            @if (count($layanan) > 0)
                            @foreach ($layanan as $l)
                            <option @if(!empty($tenaga_medis->layanan_id) && $l->id_layanan_hc == $tenaga_medis->layanan_id) selected @endif value="{{$l->id_layanan_hc}}">{{$l->nama_layanan}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-check-label" for="flexSwitchCheckChecked">Status</label>
                    </div>
                    <div class="col-md-10">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="is_melayani" 
                            @if(!empty($tenaga_medis->status) && $tenaga_medis->status == 'MELAYANI') checked @endif>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="col-md-12">
                    <button type="button" class="btn mb-2" style="width: 100%; background: #3EC396; color: #fff;" id="simpan">SIMPAN</button>
                </div>
                <div class="col-md-12">
                    <button type="button" class="btn" style="width: 100%; background: #5A6268; color: #fff;" id="kembali">KEMBALI</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".select2").select2({
            theme: 'bootstrap-5'
        });
    });
    $('#simpan').click(function (e) {
        e.preventDefault();
        var data = new FormData($("#form-data")[0]);

        $.ajax({
            url : "{{route('saveTenagaMedis')}}",
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
            } else if(data.status == 'error'){
                if(data.code == 500){
                    $('#simpan').removeAttr('disabled');
                    Swal.fire('Maaf!', data.message, 'info');
                } else {
                    var n = 0
                    for(key in data.message){
                        var  name = key
                        if(name=='kode_dokter'){name='kode_dokter'}
                        else if(name=='telepon'){name='No. Telepon'}
                        else if(name=='layanan_id'){name='Jenis Layanan'}
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

    $('#kembali').click(()=>{
		$('.other-page').fadeOut(function(){
			hideForm()
		})
	})
</script>
