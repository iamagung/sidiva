<!-- Modal Edit/Add -->
<div class="modal fade" id="modalFormDialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="saveForm" method="POST">
                <div class="modal-header">
                    <h5 class="text-card">{{!empty($data)?'Edit':'Tambah'}} Driver Ambulance</h5>
                    <button type="button" class="btn btnCancel" data-bs-dismiss="modal"><i class='bx bx-x-circle' style="font-size: 30px; width: 15px;" undefined ></i></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="{{!empty($data)?$data->id_driver:''}}">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap*</label>
                        <input type="text" class="form-control" name="nama_driver" id="nama_driver" value="{{ !empty($data) ? $data->nama_driver : ''}}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon*</label>
                        <input type="number" class="form-control" name="telepon" id="telepon" value="{{ !empty($data) ? $data->telepon : ''}}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat*</label>
                        <input type="text" class="form-control" name="alamat" id="alamat" value="{{ !empty($data) ? $data->alamat : ''}}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btnCancel" style="background-color: #6C757D; color: white;" id="btnCancel">KEMBALI</button>
                        <button type="button" class="btn btnSimpan" style="background-color: #00A006; color: white;" id="btnSimpan">SIMPAN</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('#modalFormDialog').modal('show');
    $('#btnSimpan').click(function (e) { 
        e.preventDefault();
        // var telepon = $('#telepon').val() || 20;
        var teleponElement = $('#telepon');
        var telepon = teleponElement.length ? teleponElement.val() : 20;
        var nama_driver = $('#nama_driver').val();
        var alamat = $('#alamat').val();

        if (!telepon) {
            Swal.fire('Maaf!', 'No Telepon Wajib Diisi', 'warning');
        } else if(!nama_driver) {
            Swal.fire('Maaf!!', 'Nama Driver Wajib Diisi.', 'warning');
        } else if(!alamat) {
            Swal.fire('Maaf!!', 'Alamat Wajib Diisi.', 'warning');
        } else{
            var data = new FormData($('#saveForm')[0]);
            $.ajax({
                url: '{{route("saveDriverPsc")}}',
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