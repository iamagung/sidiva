<!-- Modal Edit/Add -->
<div class="modal fade" id="modalFormDialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="saveForm">
                <div class="modal-header">
                    <h5 class="text-card">{{!empty($data)?'Edit':'Tambah'}} Tenaga Medis</h5>
                    <button type="button" class="btn btnCancel" data-bs-dismiss="modal"><i class='bx bx-x-circle' style="font-size: 30px; width: 15px;" undefined ></i></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="{{!empty($data)?$data->id_tenaga_medis:''}}">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Jenis Tenaga Kesehatan <small style="color: red">*)</small></label>
                            <select name="jenis_nakes" id="jenis_nakes" class="form-control select2">
                                <option value="">-Pilih-</option>
                                <option @if($data && $data->jenis_nakes=='perawat') selected @endif value="perawat">Perawat</option>
                                <option @if($data && $data->jenis_nakes=='dokter') selected @endif value="dokter">Dokter</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Nama Tenaga Kesehatan <small style="color: red">*)</small></label>
                            <select name="nama_nakes" id="nama_nakes" class="form-control select2">
                                <option value="">-Pilih-</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Pilih Layanan Homecare <small style="color: red">*)</small></label>
                            <select name="layanan_id" id="layanan_id" class="form-control select2">
                                <option value="">-Pilih-</option>
                                @if (count($layanan) > 0)
                                    @foreach ($layanan as $key => $l)
                                        <option @if($data && $l->id_layanan_hc==$data->layanan_id) selected @endif value="{{$l->id_layanan_hc}}">{{$l->nama_layanan}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn" style="background-color: #6C757D; color: white;" class="close" data-bs-dismiss="modal">KEMBALI</button>
                        <button type="button" class="btn btnSimpan" style="background-color: #00A006; color: white;" id="btnSimpan">SIMPAN</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    $('#modalFormDialog').modal('show');
    $(document).ready(function () {
        loadNakes();
    });
    $(".select2").select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalFormDialog')
    });
    $('#jenis_nakes').change(function(){
        var jenis = $('#jenis_nakes').val();
        $.post("{{route('getNakesHomecare')}}",{jenis:jenis},function(data){
            var nama = '<option value="">-Pilih-</option>';
            if(data.metaData.code==200){
                if(data.response.length>0){
                    $.each(data.response,function(v,k){
                        nama+='<option value="'+k.id+'">'+k.name+'</option>';
                    });
                }
            }
            $('#nama_nakes').html(nama);
        });
    });
    function loadNakes() {
        var jenis = $('#jenis_nakes').val();

		// Selected nakes
		var selectedNakes = "{{ !empty($nakes) ? $nakes:'' }}";
        if (selectedNakes!='') {
            $.post("{{route('getNakesHomecare')}}",{jenis:jenis},(data)=>{
                var nama = '<option value="first">-Pilih-</option>';
                if(data.metaData.code==200){
                    if(data.response.length>0){
                        $.each(data.response,function(v,k){
                            nama+='<option value="'+k.id+'">'+k.name+'</option>';
                        });
                    }

                    $('#nama_nakes').html(nama);
                    $('#nama_nakes').val((selectedNakes?selectedNakes:'first')).trigger('change');
                }
            });
        }
    }
    $('#btnSimpan').click(function (e) { 
        e.preventDefault();
        var jenis = $('#jenis_nakes').val();
        var nama = $('#nama_nakes').val();
        var layanan = $('#layanan_id').val();

        if (!jenis) {
            Swal.fire('Maaf!', 'Jenis Tenaga Kesehatan Wajib Diisi', 'warning');
        } else if(!nama) {
            Swal.fire('Maaf!!', 'Nama Tenaga Kesehatan Wajib Diisi', 'warning');
        } else if(!layanan) {
            Swal.fire('Maaf!!', 'Layanan Homecare Wajib Diisi.', 'warning');
        } else{
            var data = new FormData($('#saveForm')[0]);
            $.ajax({
                url: '{{route("saveNakesHomecare")}}',
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
                    $('.btnSimpan').attr('',false).html('SIMPAN')
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
    $('#modalFormDialog').on('hidden.bs.modal', function() {
        $('#modalForm').html('');
    });
</script>