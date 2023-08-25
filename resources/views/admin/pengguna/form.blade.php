<div class="row">
    <div class="col-md-12">
        <form id="form-data" class="card">
            <div class="card-header">
                <h5>{{$title}}</h5>
            </div>
            <div class="card-body">
                <input type="hidden" name="id" id="id" value="{{ (!empty($data)) ? $data->id : '' }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Nama</label>
                                <select name="name_user" id="name_user" class="form-control select2">
                                    <option value="">.:: PILIH ::.</option>
                                    @if(count($dokter) > 0)
                                    @foreach($dokter as $d)
                                    <option @if (!empty($data) && $data->kode_dokter == $d->setupall_id) selected @endif value="{{$d->setupall_id}}">{{$d->nilaichar}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Username <small>*)</small></label>
                                <input type="username" name="username" class="form-control" placeholder="Username" autocomplete="off"
                                value="{{!empty($data) ? $data->email : ''}}">
                            </div>
                            <div class="col-md-4">
                                <label>Password <small>*)</small></label>
                                <input type="Password" name="password" class="form-control" placeholder="Password" autocomplete="off">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-4">
                                <label>POLI</label>
                                <select name="poli" id="poli" class="form-control select2">
                                    <option value="">.:: PILIH ::.</option>
                                    @if(count($poli) > 0)
                                    @foreach($poli as $p)
                                    <option @if(!empty($data->poli_id) && $data->poli_id == $p->KodePoli) selected @endif value="{{$p->KodePoli}}">{{$p->NamaPoli}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>No. Telepon</label>
                                <input type="telp" name="telp" class="form-control" placeholder="Nomor Telepon" autocomplete="off"
                                value="{{!empty($data->phone) ? $data->phone : ''}}">
                            </div>
                            <div class="col-md-4">
                                <label>Alamat</label>
                                <textarea name="alamat" id="alamat" cols="70" rows="5" class="form-control">{{!empty($data->address_user) ? $data->address_user : ''}}</textarea>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-4">
                                <label for="">Status</label><br>
                                <label style="margin-right: 20px"><input type="radio" name="is_active" value="active" checked> Active</label>
                                <label style="margin-right: 20px"><input type="radio" name="is_active" value="nonactive"> Non Active</label>
                            </div>
                            <div class="col-md-4">
                                <label>Photo</label>
                                <input type="file" onchange="loadFilePhoto(event)" name="photo_user" accept="image/*" class="form-control upload">
                            </div>
                            <div class="col-md-4 crop-edit">
                                <center>
                                    @if(!empty($data->photo_user))
                                        <img id="preview-photo" src="{!! url('uploads/users/'.$data->photo_user) !!}" class="img-polaroid" width="100">
                                    @else
                                        <img id="preview-photo" src="{!! url('assets/img/default_image.jpg') !!}" class="img-polaroid" width="100">
                                    @endif
                                </center>
                            </div>
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
            url : "{{route('savePengguna')}}",
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
                        if(name=='jenis_dokter'){name='Jenis Dokter'}
                        else if(name=='poli'){name='Nama Poli'}
                        else if(name=='password'){name='Password'}
                        else if(name=='username'){name='Username'}
                        else if(name=='name_user'){name='Nama'}
                        n++
                    }
                    
                    $('#simpan').removeAttr('disabled');
                    Swal.fire('Maaf!', name+' Wajib Diisi', 'info');
                }
            } else {
                Swal.fire('Peringatan!', data.message, data.status);
            }
        }).fail(function() {
            Swal.fire("MAAF!", "Terjadi Kesalahan, Silahkan Ulangi Kembali !!", "warning");
            $('#simpan').removeAttr('disabled');
        });  
    });

    function loadFilePhoto(event) {
        var image = URL.createObjectURL(event.target.files[0]);
        $('#preview-photo').fadeOut(function(){
        	$(this).attr('src', image).fadeIn().css({
        		'-webkit-animation' : 'showSlowlyElement 700ms',
        		'animation'         : 'showSlowlyElement 700ms'
        	});
        });
    };

    $('#kembali').click(()=>{
		$('.other-page').fadeOut(function(){
			hideForm()
		})
	})
</script>