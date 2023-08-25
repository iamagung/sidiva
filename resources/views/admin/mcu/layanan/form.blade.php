@php
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
</script>