{{-- @php
function rupiah($angka){
    $hasil_rupiah = "Rp. " . number_format((int)$angka);
    $hasil_rupiah = str_replace(',', '.', $hasil_rupiah);
    return $hasil_rupiah;
}
@endphp
<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <form id="form-data" class="card">
                <div class="card-header">
                    <h5>{{$title}}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <input type="hidden" name="id" id="id" value="{{ !empty($layanan->id_layanan) ? $layanan->id_layanan : ''}}">
                        <div class="col-4">
                            <label>Kategori Layanan <small>*)</small></label>
                            <select name="kategori_layanan" id="kategori_layanan" class="form-control">
                                <option value="">- Pilih -</option>
                                <option @if(isset($layanan->jenis_layanan) && $layanan->jenis_layanan == 'aps') selected @endif value="aps">APS</option>
                                <option @if(isset($layanan->jenis_layanan) && $layanan->jenis_layanan == 'paket') selected @endif value="paket">Paket</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label>Nama Layanan <small>*)</small></label>
                            <input type="text" name="nama_layanan" id="nama_layanan" class="form-control" placeholder="Nama Layanan"
                            value="{{ !empty($layanan->nama_layanan) ? $layanan->nama_layanan : ''}}" autocomplete="off">
                        </div>
                        <div class="col-4">
                            <label>Harga / Biaya <small>*)</small></label>
                            <input type="text" name="harga" id="harga" class="form-control" placeholder="Rp." onkeyup="ubahFormat(this)"
                            value="{{!empty($layanan->harga) ? rupiah($layanan->harga) : ''}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Deskripsi <small>*)</small></label><br>
                            <textarea name="deskripsi" id="deskripsi" cols="90" rows="10" placeholder="Deskripsi" autocomplete="off" class="form-control">{{!empty($layanan->deskripsi)?$layanan->deskripsi:''}}</textarea>
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
</div>

<script type="text/javascript">
    $('#simpan').click(function (e) { 
        e.preventDefault();
        var data = new FormData($("#form-data")[0]);

        $.ajax({
            url : "{{route('saveLayananMcu')}}",
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
                        if(name=='deskripsi'){name='Dezskripsi'}
                        else if(name=='harga'){name='Harga'}
                        else if(name=='nama_layanan'){name='Nama Layanan'}
                        else if(name=='kategori_layanan'){name='Kategori Pelayanan'}
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

    function ubahFormat(val){
		$('#harga').val(formatRupiah(val.value,'Rp. '))
	}

    /* Fungsi formatRupiah */
	function formatRupiah(angka, prefix) {
		var number_string = angka.toString().replace(/[^,\d]/g, "");
		split = number_string.split(",");
		sisa = split[0].length % 3;
		rupiah = split[0].substr(0, sisa);
		ribuan = split[0].substr(sisa).match(/\d{3}/gi);

		// tambahkan titik jika yang di input sudah menjadi angka ribuan
		if (ribuan) {
			separator = sisa ? "." : "";
			rupiah += separator + ribuan.join(".");
		}

		rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
		return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
	}

    $('#kembali').click(()=>{
		$('.other-page').fadeOut(function(){
			hideForm()
		})
	})
</script> --}}

@php
function rupiah($angka){
    $hasil_rupiah = "Rp. " . number_format((int)$angka);
    $hasil_rupiah = str_replace(',', '.', $hasil_rupiah);
    return $hasil_rupiah;
}
@endphp
<style>
.custom-checkbox {
    display: inline-block;
    position: relative;
    padding-left: 30px;
    margin-bottom: 15px;
    cursor: pointer;
    font-size: 14px;
  }

  .custom-checkbox input {
    display: none;
  }

  .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    border: 2px solid #ccc; /* Border grey */
    background-color: #fff;
    border-radius: 5px; /* Rounded corners */
  }

  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
  }

  .custom-checkbox input:checked + .checkmark:after {
    display: block;
  }

  .custom-checkbox .checkmark:after {
    left: 6px;
    bottom: 3px;
    width: 9px;
    height: 20px;
    border: solid black; /* Black checklist */
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
  }

  .custom-select-wrapper {
    display: inline-block;
    width: 150px;
    vertical-align: top;
    margin-left: 20px;
  }

  .custom-select {
    width: 100%;
    padding: 10px;
    border: 2px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
  }


</style>
<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <form id="form-data" class="card">
                <div class="card-header">
                    <h5>{{$title}}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <input type="hidden" name="id" id="id" value="{{ !empty($layanan->id_layanan) ? $layanan->id_layanan : ''}}">
                        <div class="col-4">
                            <label>Kategori Layanan <small>*)</small></label>
                            <select name="kategori_layanan" id="kategori_layanan" class="form-control">
                                <option value="">- Pilih -</option>
                                <option @if(isset($layanan->jenis_layanan) && $layanan->jenis_layanan == 'aps') selected @endif value="aps">APS</option>
                                <option @if(isset($layanan->jenis_layanan) && $layanan->jenis_layanan == 'paket') selected @endif value="paket">Paket</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label>Nama Layanan <small>*)</small></label>
                            <input type="text" name="nama_layanan" id="nama_layanan" class="form-control" placeholder="Nama Layanan"
                            value="{{ !empty($layanan->nama_layanan) ? $layanan->nama_layanan : ''}}" autocomplete="off">
                        </div>
                        <div class="col-4">
                            <label>Harga / Biaya <small>*)</small></label>
                            <input type="text" name="harga" id="harga" class="form-control" placeholder="Rp." onkeyup="ubahFormat(this)"
                            value="{{!empty($layanan->harga) ? rupiah($layanan->harga) : ''}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Deskripsi <small>*)</small></label><br>
                            <textarea name="deskripsi" id="deskripsi" cols="90" rows="10" placeholder="Deskripsi" autocomplete="off" class="form-control">{{!empty($layanan->deskripsi)?$layanan->deskripsi:''}}</textarea>
                        </div>
                    </div>
                    <label>Jenis Layanan</label><br><br>
                    <label class="custom-checkbox">Perorangan
                        <input type="checkbox" checked="checked">
                        <span class="checkmark"></span>
                      </label><br>
                      <label class="custom-checkbox">Kelompok, &nbsp;
                        <input type="checkbox">
                        <span class="checkmark"></span>
                      </label>
                      <div class="custom-select-wrapper">
                        <label for="">Maksimal Peserta</label>
                        <select class="custom-select">
                          <option value="option1">1</option>
                          <option value="option2">2</option>
                          <option value="option3">3</option>
                          <option value="option1">4</option>
                          <option value="option2">5</option>
                          <option value="option3">6</option>
                          <option value="option1">7</option>
                          <option value="option2">8</option>
                          <option value="option3">9</option>
                          <option value="option3">10</option>
                        </select>
                      </div>
                      <br><br>
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
</div>

<script type="text/javascript">
    $('#simpan').click(function (e) {
        e.preventDefault();
        var data = new FormData($("#form-data")[0]);

        $.ajax({
            url : "{{route('saveLayananMcu')}}",
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
                        if(name=='deskripsi'){name='Dezskripsi'}
                        else if(name=='harga'){name='Harga'}
                        else if(name=='nama_layanan'){name='Nama Layanan'}
                        else if(name=='kategori_layanan'){name='Kategori Pelayanan'}
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

    function ubahFormat(val){
		$('#harga').val(formatRupiah(val.value,'Rp. '))
	}

    /* Fungsi formatRupiah */
	function formatRupiah(angka, prefix) {
		var number_string = angka.toString().replace(/[^,\d]/g, "");
		split = number_string.split(",");
		sisa = split[0].length % 3;
		rupiah = split[0].substr(0, sisa);
		ribuan = split[0].substr(sisa).match(/\d{3}/gi);

		// tambahkan titik jika yang di input sudah menjadi angka ribuan
		if (ribuan) {
			separator = sisa ? "." : "";
			rupiah += separator + ribuan.join(".");
		}

		rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
		return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
	}

    $('#kembali').click(()=>{
		$('.other-page').fadeOut(function(){
			hideForm()
		})
	})
</script>
