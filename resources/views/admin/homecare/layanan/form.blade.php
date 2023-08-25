<div class="row">
    <div class="col-md-12">
        <form id="form-data" class="card">
            <div class="card-header">
                <h5>{{$title}}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <input type="hidden" name="id" id="id" value="{{ !empty($layanan) ? $layanan->id_layanan_hc : ''}}">
                    <div class="col-md-12">
                        <label>Nama Layanan <small>*)</small></label>
                        <input type="text" name="nama_layanan" id="nama_layanan" class="form-control" placeholder="Nama Layanan"
                        value="{{!empty($layanan->nama_layanan) ? $layanan->nama_layanan : ''}}" autocomplete="off">
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
    $('#simpan').click(function (e) { 
        e.preventDefault();
        var data = new FormData($("#form-data")[0]);
        var nama_layanan = $('#nama_layanan').val();

        if (!nama_layanan) {
            Swal.fire('Maaf!', 'Nama Layanan Wajib Diisi', 'warning');
        } else {
            $.ajax({
                url : "{{route('saveLayananHC')}}",
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
                    Swal.fire('Maaf!', data.message, 'error');
                }
            }).fail(function() {
                Swal.fire("MAAF!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
                $('#simpan').removeAttr('disabled');
            }); 
        } 
    });

    $('#kembali').click(()=>{
		$('.other-page').fadeOut(function(){
			hideForm()
		})
	})
</script>