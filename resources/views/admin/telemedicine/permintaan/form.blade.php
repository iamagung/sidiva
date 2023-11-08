<div class="row">
    <div class="col-md-12">
        <form id="form-data" class="card">
            <div class="card-header">
                <h5 class="text-center">{{$title}}</h5>
            </div>
            <div class="card-body">
                <input type="hidden" name="id" id="id" value="{{ !empty($permintaan->id_permintaan_hc) ? $permintaan->id_permintaan_hc : ''}}">
                <div class="row mb-3">
                    <label>Nama Dokter *</label>
                    <input type="text" name="nama_dokter" id="nama_dokter" value="{{ !empty($permintaan->nama_dokter) ? $permintaan->nama_dokter : ''}}">
                </div>
                <div class="row mb-3">
                    <label>Pilih Tenaga Medis</label>
                    <select name="tenaga_medis_id" id="tenaga_medis_id" class="form-control select2">
                        <option value="">-Pilih-</option>
                        @if (count($tenagaMedis) > 0)
                            @foreach ($tenagaMedis as $key => $tm)
                                <option value="{{$tm->id_tenaga_medis}}">{{$tm->nama_nakes}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                </div><div class="row mb-3">
                    <div class="col-md-10">
                        <label>Link Meet *</label>
                        <input type="text" name="link_meet" id="link_meet" value="{{ !empty($permintaan->link_meet) ? $permintaan->link_meet : ''}}">
                    </div>
                    <div class="col-md-2">
                        <label>Generate</label>
                        <button id="generate_btn">Buat Link Meet</button>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        <button type="button" class="btn mb-2" style="width: 100%; background: #3EC396; color: #fff;" id="simpan">PROSES</button>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <button type="button" class="btn" style="width: 100%; background: #5A6268; color: #fff;" id="kembali">KEMBALI</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        // Large using Select2 properties
        $(".select2").select2({
            theme: 'bootstrap-5'
        });
    });

    $('#generate_btn').click(function (e) {
        e.preventDefault();
        window.open('http://meet.google.com/new');
    });

    $('#simpan').click(function (e) {
        e.preventDefault();
        var data = new FormData($("#form-data")[0]);

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

    $('#kembali').click(()=>{
		$('.other-page').fadeOut(function(){
			hideForm()
		})
	})
</script>
