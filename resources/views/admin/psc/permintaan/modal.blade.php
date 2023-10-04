<div class="modal fade" id="modalFormDialog" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<!-- Modal content-->
		<div class="modal-content">
            <form id="saveForm" method="POST">
                <div class="modal-header bg-card">
                    <h5 class="text-card">{{!empty($data)?'Edit':'Tambah'}} Ambulance</h5>
                    <button type="button" class="btn btnCancel">&times;</button>
                </div>
                <div class="modal-body">
                        <input type="hidden" name="id" id="id" value="{{!empty($data)?$data->id_layanan_ambulance:''}}">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Nama Layanan <small>*</small></label>
                                <input type="text" name="nama_layanan" id="nama_layanan" class="form-control" value="{{!empty($data)?$data->nama_layanan:''}}" autocomplete="off">
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary btnCancel">KEMBALI</button>
                    <button type="button" class="btn btn-sm btn-success btnSimpan">SIMPAN</button>
                </div>
            </form>
		</div>
	</div>
</div> 
<script type="text/javascript">
    $('#modalFormDialog').modal('show');
    $('.btnSimpan').click(function (e) { 
        e.preventDefault();
        var nama_layanan = $('#nama_layanan').val();
        if(!nama_layanan) {
            Swal.fire('Maaf!!', 'Nama Layanan Wajib Diisi.', 'warning')
        } else{
            var data = new FormData($('#saveForm')[0]);
            $.ajax({
                url: '{{route("saveLayananPsc")}}',
                type: 'POST',
                data: data,
                async: true,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data){
                    if(data.code==200){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1200
                        })
                        location.reload()
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Whoops',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1300,
                        })
                    }
                    $('.btnSimpan').attr('disabled',false).html('SIMPAN')
                }
            }).fail(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Whoops..',
                    text: 'Terjadi kesalahan silahkan ulangi kembali',
                    showConfirmButton: false,
                    timer: 1300,
                })
                $('.btnSimpan').attr('disabled',false).html('SIMPAN')
            })
        }
    });
    $('.btnCancel').click(()=>{
		$('#modalForm').fadeOut(function(){
			location.reload()
		})
	})
</script>