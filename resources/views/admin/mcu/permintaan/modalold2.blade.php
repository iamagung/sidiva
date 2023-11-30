<div class="modal fade" id="modalForm" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" style="padding:15px 50px;">
				<h5 style="font-size: 14pt" class="text-center">Pembayaran Langsung - Medical Check Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				{{-- <button type="button" class="btn-close float-end" data-bs-dismiss="modal">&times;</button> --}}
			</div>
			<div class="modal-body">
				<form id="formAddModal">
					<input type="hidden" name="id" id="id" value="{{$data->id_permintaan}}">
					<div class="row">
                        <div class="col-md-12">
                            <table class="table" style="background: #EDE2CB">
                                <tr>
                                    <td>Nama Pasien</td>
                                    <td>:</td>
                                    <td>{{$data->nama}}</td>
                                </tr>
                                <tr>
                                    <td>Jenis MCU</td>
                                    <td>:</td>
                                    <td>
                                        <table>
                                            @foreach ($layanan as $key => $v)
                                            <tr>
                                                <td>
                                                    <li>{{$v->jenis_layanan}}</li>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pemeriksaan</td>
                                    <td>:</td>
                                    <td>
                                        <table>
                                            @foreach ($layanan as $key => $v)
                                            <tr>
                                                <td>
                                                    <li>{{$v->nama_layanan}}</li>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tanggal MCU</td>
                                    <td>:</td>
                                    <td>{{$data->tanggal_kunjungan}}</td>
                                </tr>
                            </table>
                        </div>
					</div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label>Total pembayaran sebesar :</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                </div>
                                <input type="text" name="total" id="total" class="form-control" placeholder="000 000 000" autocomplete="off" value="{{number_format($data->biaya,0,',','.')}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label>Terima pembayaran sebesar :</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                </div>
                                <input type="text" name="jumlah_bayar" id="jumlah_bayar" class="form-control" placeholder="000 000 000" autocomplete="off" onkeyup="ubahFormat(this)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Uang kembalian sebesar :</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                </div>
                                <input type="text" name="kembalian" id="kembalian" class="form-control" placeholder="000 000 000" autocomplete="off" disabled>
                            </div>
                        </div>
                    </div>
				</form>
			</div>
			<div class="modal-footer float-end">
                <button type="button" class="btn btn-sm btn-secondary" class="close" data-bs-dismiss="modal">Kembali</button>
                <button type="button" class="btn btn-sm btn-primary" id="btn-confirm">Bayar</button>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
    var onLoad = (function() {
        $('#modalForm').find('.modal-dialog').css({
            'width': '40%'
        });
        $('#modalForm').modal('show');
        $('#kembalian').html(0);
    })();
    $('#modalForm').on('hidden.bs.modal', function() {
        $('.modal-dialog').html('');
    });
    $('#btn-confirm').click(function (e) {
        e.preventDefault();
        var totalBayar = $('#total').val();
        var totalCash = $('#jumlah_bayar').val();
        var totalKembalian = $('#kembalian').val();
        var total = (totalBayar) ? totalBayar.replace(/\D/g,'') : 0;
		var bayar = (totalCash) ? totalCash.replace(/\D/g,'') : 0;
        var kembali = (totalKembalian) ? totalKembalian.replace(/\D/g,'') : 0;

        if (!totalCash) {
            Swal.fire('Maaf!', 'Terima pembayaran sebesar wajib diisi', 'warning');
        } else if(parseInt(bayar) < parseInt(total)) {
            Swal.fire('Maaf!', 'Nominal yang anda inputkan kurang dari jumlah yang harus dibayar', 'warning');
        } else {
            var data = new FormData($('#formAddModal')[0]);
                data.append("total", totalBayar);
                data.append("kembalian", totalKembalian);

            $.ajax({
                url : "{{route('simpanBayarPermintaanMcu')}}",
                type: "POST",
                data: data,
                contentType : false,
                processData: false
            }).done(function(data) {
                if (data.code == 200) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    $('#modalForm').modal('hide');
                    location.reload();
                }else{
                    Swal.fire('Error!', data.message, 'error');
                }
            }).fail(function() {
                Swal.fire('Oops!!',"Terjadi kesalahan sistem!","error");
            });
        }
    });

    function ubahFormat(val){
		$('#jumlah_bayar').val(formatRupiah(val.value,''))

        // hitung jumlah bayar dan kembalian
		var totalbyr = $('#total').val();
        var cash = $('#jumlah_bayar').val();
		var bayar = (cash) ? cash.replace(/\D/g,'') : 0;
        var total = (totalbyr) ? totalbyr.replace(/\D/g,'') : 'Kurang';

		if(parseInt(bayar)<parseInt(total)){
			$('#kembalian').val('Kurang');
		}else{
			var kembalian = parseInt(total)-parseInt(bayar);
			$('#kembalian').val(formatRupiah(kembalian, 'Rp. '));
		}
	}

    function formatRupiah(angka, prefix) {
		var number_string = angka.toString().replace(/[^,\d]/g, '')
		// var number_string = angka.replace(/[^,\d]/g, "").toString()
		split = number_string.split(',')
		sisa = split[0].length % 3
		rupiah = split[0].substr(0, sisa)
		ribuan = split[0].substr(sisa).match(/\d{3}/gi)

		// tambahkan titik jika yang di input sudah menjadi angka ribuan
		if (ribuan) {
			separator = sisa ? '.' : ''
			rupiah += separator + ribuan.join('.')
		}
		rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah
		return prefix == undefined ? rupiah : rupiah ? rupiah : ''
	}
</script>
