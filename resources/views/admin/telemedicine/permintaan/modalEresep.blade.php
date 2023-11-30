<div class="modal fade" id="modalEresep" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
        @php
            function rupiah($angka)
            {
                $hasil_rupiah = 'Rp. ' . number_format((int) $angka);
                $hasil_rupiah = str_replace(',', '.', $hasil_rupiah);
                return $hasil_rupiah;
            }
        @endphp
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="font-size: 14pt" class="text-center pt-2">ERESEP TELEMEDICINE</h4>
				<button type="button" class="btn btnClose" data-bs-dismiss="modal"><i class='bx bx-x-circle' style="font-size: 30px; width: 15px;" undefined ></i></button>
			</div>
			<div class="modal-body">
				<form id="formEresep">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label>Nomor Resep</label>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="no_resep" id="no_resep" value="{{ !empty($permintaan->resep_obat) ? $permintaan->resep_obat->no_resep : ''}}" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label>Tanggal Resep</label>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="tanggal_resep" id="tanggal_resep" value="{{ !empty($permintaan->resep_obat) ? $permintaan->resep_obat->tgl_resep : '-'}}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label>Status Pembayaran</label>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="status_pembayaran" id="status_pembayaran" value="{{ !empty($permintaan->resep_obat) ? $permintaan->resep_obat->status_pembayaran : ''}}" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label>Tanggal Lunas</label>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="status_pembayaran" id="status_pembayaran" value="{{ !empty($permintaan->payment_permintaan_eresep) ? $permintaan->payment_permintaan_eresep->tgl_lunas : '-'}}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label>Obat Diantar</label>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="diantar" id="diantar" value="{{ !empty($permintaan->resep_obat) ? ($permintaan->resep_obat->diantar ? $permintaan->resep_obat->diantar : 'belum memilih' ) : 'belum memilih'}}" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label>Ongkos Kirim</label>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="ongkos_kirim" id="ongkos_kirim" value="{{ !empty($permintaan->payment_permintaan_eresep) ? rupiah($permintaan->payment_permintaan_eresep->ongkos_kirim) : '-'}}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{-- </div>
                        <div class="col-md-6"> --}}
                            <div class="row px-2">
                                <table class="px-4 table table-success table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Obat</th>
                                            <th style="width: 40%;">Nama Obat</th>
                                            <th>Jumlah</th>
                                            <th>Signa</th>
                                            <th>Harga</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no=1;$total=0; ?>
                                        @foreach ($permintaan->resep_obat->resep_obat_detail as $obat)
                                        <tr>
                                            <td>{{$no++}}</td>
                                            <td>{{$obat->kode_obat}}</td>
                                            <td>{{$obat->nama_obat}}</td>
                                            <td>{{$obat->qty}}</td>
                                            <td>{{$obat->signa}}</td>
                                            <td>{{rupiah($obat->harga)}}</td>
                                            <td>{{rupiah($obat->harga*$obat->qty)}}</td>
                                        </tr>
                                        <?php $total+=$obat->harga*$obat->qty; ?>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" class="text-end">Total Obat :</th>
                                            <th>{{rupiah($total)}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
				</form>
			</div>
			<div class="modal-footer d-block float-end">
                <button type="button" class="btn btn-sm btn-secondary float-end" class="close" data-bs-dismiss="modal">Kembali</button>
			</div>
		</div>

	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    $('#modalEresep').modal('show');
    $('#modalEresep').on('hidden.bs.modal', function() {
        $('.modal-dialog').html('');
    });
</script>
