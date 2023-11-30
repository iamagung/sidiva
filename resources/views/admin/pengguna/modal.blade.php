<div class="modal fade" id="modalFormDialog" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-card">
				<h5 class="text-card">{{!empty($data)?'Edit':'Tambah'}} Pengguna</h5>
				<button type="button" class="btn btnCancel">&times;</button>
			</div>
			<div class="modal-body">
				<form id="saveForm">
					<input type="hidden" name="id" id="id" value="{{!empty($data)?$data->id:''}}">
                    <input type="hidden" name="level_user" id="level_user" value="{{!empty($data)?$data->level:''}}">
                    <div class="row mb-3" id="notNakes">
						<div class="col-md-12">
                            <label>Nama Pengguna <small>*</small></label>
                            <input type="text" name="nama" id="nama" class="form-control" value="{{!empty($data)?$data->name:''}}" autocomplete="off">
						</div>
					</div>
                    <div class="row mb-3" id="isNakes">
						<div class="col-md-12">
                            <label>Nama Pengguna <small>*</small></label>
                            <select class="single-select" name="nama_nakes" id="nama_nakes" @if(!empty($data)) disabled @endif></select>
						</div>
					</div>
                    <div class="row mb-3">
						<div class="col-md-12">
                            <label>Pilih Level <small>*</small></label>
                            <select name="level" id="level" class="form-control" @if(!empty($data)) disabled @endif>
                                <option value="">.:: Pilih ::.</option>
                                <option @if(!empty($data) && $data->level=='admin') selected @endif value="admin">Admin Utama</option>
                                <option @if(!empty($data) && $data->level=='adminmcu') selected @endif value="adminmcu">Admin MCU</option>
                                <option @if(!empty($data) && $data->level=='adminhomecare') selected @endif value="adminhomecare">Admin Homecare</option>
                                <option @if(!empty($data) && $data->level=='admintelemedis') selected @endif value="admintelemedis">Admin Telemedicine</option>
                                <option @if(!empty($data) && $data->level=='perawat') selected @endif value="perawat">Perawat</option>
                                <option @if(!empty($data) && $data->level=='dokter') selected @endif value="dokter">Dokter</option>
                            </select>
						</div>
					</div>
                    <div class="row mb-3">
						<div class="col-md-12">
                            <label>No. Telepon <small>*</small></label>
                            <input type="text" name="telepon" id="telepon" class="form-control" value="{{!empty($data)?$data->telepon:''}}" autocomplete="off">
						</div>
					</div>
                     <div class="row mb-3">
						<div class="col-md-12">
                            <label>Username <small>*</small></label>
                            <input type="text" name="username" id="username" class="form-control" value="{{!empty($data)?$data->username:''}}" autocomplete="off">
						</div>
					</div>
                    <div class="row mb-3">
						<div class="col-md-12">
                            <label>Password <small>*</small></label>
                            <input type="text" name="password" id="password" class="form-control" autocomplete="off">
						</div>
					</div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Foto Diri</label>
                            <input class="form-control" type="file" onchange="loadFilePhoto(event)" name="foto" id="foto" value="{{!empty($data->foto)? $data->foto:''}}" placeholder="Upload Foto">
                        </div>
                        <div class="col-md-6">
                            <center>
                                @if(!empty($data->foto))
                                    <img id="preview-photo" src="{!! url('storage/pengguna/'.$data->foto) !!}" class="img-polaroid" width="200">
                                @else
                                    <img id="preview-photo" src="{!! url('assets/images/no-image.jpg') !!}" class="img-polaroid" width="200">
                                @endif
                            </center>
                        </div>
                    </div>
				</form>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary btnCancel">KEMBALI</button>
                <button type="button" class="btn btn-sm btn-success btnSimpan">SIMPAN</button>
            </div>
		</div>

	</div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#isNakes').hide();
        var level = $('#level').val();
        if(level=='perawat'||level=='dokter') {
            $("#nama").prop('disabled', true);
        } else {
            $("#nama").prop('disabled', false);
        }
    });
    $(".single-select").select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#saveForm')
    });
    $('#level').change(function (e) {
        e.preventDefault();
        var level = $('#level').val();
        if(level=='perawat'||level=='dokter') {
            $('#notNakes').hide();
            $('#isNakes').show();
            setNamaNakes(level);
        } else {
            $('#isNakes').hide();
            $('#notNakes').show();
        }
    });
    function setNamaNakes(param) {
        var selectNakes = $('#nama_nakes').empty()
        $.get('{{ route("searchNakes") }}',{level:param})
        .done(function(data) {
            if (data.code==200) {
                selectNakes.append($('<option disabled/>').val('').text('-Pilih-')).val('').trigger('change')
                $.each(data.data,async(key,val)=>{
                    selectNakes.append($('<option/>').val(val.name).text(val.name))
                })
            } else {
                Swal.fire('Whoopss!', 'Data not found', 'error');
            }
        }).fail(function() {
            Swal.fire('Oops!!',"Terjadi kesalahan sistem","error");
        });
    }
    function loadFilePhoto(event) {
        var image = URL.createObjectURL(event.target.files[0]);
        $('#preview-photo').fadeOut(function(){
            $(this).attr('src', image).fadeIn().css({
                '-webkit-animation' : 'showSlowlyElement 700ms',
                'animation'         : 'showSlowlyElement 700ms'
            });
        });
    };
    $('#modalFormDialog').modal('show');
    $('.btnSimpan').click(function (e) {
        e.preventDefault();
        var id = $('#id').val();
        var pilih = $('#level').val();
        var nama = $('#nama').val();
        var namaNakes = $('#nama_nakes').find(":selected").text();
        var telepon = $('#telepon').val();
        var username = $('#username').val();
        var password = $('#password').val();
        if(!nama) {
            Swal.fire('Maaf!!', 'Nama Pengguna Wajib Diisi.', 'warning')
        } else if(!namaNakes && !id && (level=='perawat'||level=='perawat')) {
            Swal.fire('Maaf!!', 'Level Wajib Diisi.', 'warning')
        } else if(!pilih) {
            Swal.fire('Maaf!!', 'Level Wajib Diisi.', 'warning')
        } else if(!telepon) {
            Swal.fire('Maaf!!', 'No. Telepon Wajib Diisi.', 'warning')
        } else if(!username) {
            Swal.fire('Maaf!!', 'Username Wajib Diisi.', 'warning')
        } else if(!id && !password) {
            Swal.fire('Maaf!!', 'Password Wajib Diisi.', 'warning')
        } else{
            var data = new FormData($('#saveForm')[0]);
            $.ajax({
                url: '{{route("savePengguna")}}',
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
