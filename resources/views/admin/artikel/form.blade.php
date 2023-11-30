<div class="row">
    <div class="col-md-12">
        <form id="form-data" class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <input type="hidden" name="id" id="id" value="{{!empty($data->id_artikel_kesehatan)?$data->id_artikel_kesehatan:''}}">
                    <div class="col-md-12">
                        <label>Judul Artikel <small>*</small></label>
                        <input type="text" name="judul" id="judul" class="form-control" placeholder="Judul Artikel"
                        value="{{!empty($data->judul)?$data->judul:''}}" autocomplete="off">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>Deskripsi <small>*</small></label><br>
                        <textarea name="deskripsi" id="editor1" cols="90" rows="10" placeholder="Deskripsi" class="form-control" autocomplete="off">{!! !empty($data->isi)?$data->isi:'' !!}</textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-2">
                        <button type="button" class="btn" style="width: 100%; background: #5A6268; color: #fff;" id="kembali">KEMBALI</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn" style="width: 100%; background: #007BFF; color: #fff;" id="simpan">SIMPAN</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var editor = CKEDITOR.replace('editor1', {
		toolbarCanCollapse:false,
	});
    $('#simpan').click(function (e) {
        e.preventDefault();
        var data = new FormData($("#form-data")[0]);
        var judul = $('#judul').val();
        var deskripsi = CKEDITOR.instances.editor1.getData();
            data.append('deskripsi',deskripsi);
        if (!judul) {
            Swal.fire({
                icon: 'error',
                title: 'Whoops..',
                text: 'Deskripsi Wajib Diisi',
                showConfirmButton: false,
                timer: 1300,
            })
        } else if (!deskripsi) {
            Swal.fire({
                icon: 'error',
                title: 'Whoops..',
                text: 'Deskripsi Wajib Diisi',
                showConfirmButton: false,
                timer: 1300,
            })
        } else {
            $.ajax({
                url : "{{route('storeArtikelKesehatan')}}",
                type: 'POST',
                data: data,
                async: true,
                cache: false,
                contentType: false,
                processData: false
            }).done(function(data) {
                if (data.status=='success') {
                    Swal.fire('Berhasil', data.message, 'success');
                    location.reload();
                } else {
                    Swal.fire('Maaf!', data.message, 'info');
                }
            }).fail(function() {
                Swal.fire("MAAF!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
            });
        }
    });
    $('#kembali').click(()=>{
		$('.other-page').fadeOut(function(){
			hideForm()
		})
	})
</script>
