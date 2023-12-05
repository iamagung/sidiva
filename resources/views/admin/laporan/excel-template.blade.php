<table>
    <thead>
        <tr>
            @isset($paymentTelemedicine)
                <th>No</th>
                <th>Tanggal Order</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Nama Poli</th>
                <th>Nama Dokter</th>
                <th>Nama Perawat</th>
                <th>Tagihan</th>
                <th>Total Dibayarkan</th>
                <th>status</th>
            @endisset
            @isset($paymentHomecare)
                <td>No</td>
                <td>Tanggal Order</td>
                <td>No. RM</td>
                <td>Nama Pasien</td>
                <td>Alamat Pasien</td>
                <td>Layanan Homecare</td>
                <td>Tagihan</td>
                <td>Total Dibayarkan</td>
                <td>Status</td>
            @endisset
            @isset($paymentMcu)
                <td>No</td>
                <td>Tanggal Order</td>
                <td>No. RM</td>
                <td>Nama Pemesan</td>
                <td>Jenis MCU</td>
                <td>Layanan MCU</td>
                <td>Tagihan</td>
                <td>Total Dibayarkan</td>
                <td>Status</td>
            @endisset
            @isset($layananHomecare)
                <th>No</th>
                <th>Tanggal Order</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Alamat Pasien</th>
                <th>Layanan Homecare</th>
                <th>Tanggal Mulai</th>
                <th>Status</th>
                <th>Tanggal Selesai</th>
            @endisset
            @isset($layananTelemedicine)
                <th>No</th>
                <th>Tanggal Order</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Nama Poli</th>
                <th>Nama Dokter</th>
                <th>Nama Perawat</th>
                <th>Tanggal Mulai</th>
                <th>Status</th>
                <th>Tanggal Selesai</th>
            @endisset
            @isset($layananMcu)
                <th>No</th>
                <th>Tanggal Order</th>
                <th>No. RM</th>
                <th>Nama Pemesan</th>
                <th>Jenis Mcu</th>
                <th>Layanan Mcu</th>
                <th>Tanggal Mulai</th>
                <th>Status</th>
                <th>Tanggal Selesai</th>
            @endisset
        </tr>
    </thead>
    <tbody>
        <?php $no=1;?>
        @isset($paymentTelemedicine)
            @if (count($paymentTelemedicine)>0)
                @foreach ($paymentTelemedicine as $pay)
                <tr>
                    <td>{{$no++}}</td>
                    <td>{{$pay->tanggal_kunjungan}}</td>
                    <td>{{$pay->no_rm}}</td>
                    <td>{{$pay->nama}}</td>
                    <td>{{$pay->permintaan_telemedicine->tmPoli->NamaPoli}}</td>
                    @if ($pay->permintaan_telemedicine->dokter==null)
                        <td>-</td>
                    @else
                        <td>{{$pay->permintaan_telemedicine->dokter->user_ranap->name}}</td>
                    @endif
                    @if ($pay->permintaan_telemedicine->perawat==null)
                        <td>-</td>
                    @else
                        <td>{{$pay->permintaan_telemedicine->perawat->user_ranap->name}}</td>
                    @endif
                    @if ($pay->diantar == 'ya' && $pay->jenis_layanan == 'eresep_telemedicine')
                        <td>{{"Rp. " . number_format(($pay->nominal + $pay->ongkos_kirim), 2, ",", ".")}}</td>
                    @else
                        <td>{{"Rp. " . number_format($pay->nominal, 2, ",", ".")}}</td>
                    @endif
                    @if (in_array($pay->status,['SETTLED','PAID']))
                        @if ($pay->diantar == 'ya' && $pay->jenis_layanan == 'eresep_telemedicine')
                            <td>{{"Rp. " . number_format(($pay->nominal + $pay->ongkos_kirim), 2, ",", ".")}}</td>
                        @else
                            <td>{{"Rp. " . number_format($pay->nominal, 2, ",", ".")}}</td>
                        @endif
                    @else
                        <td>{{"Rp. " . number_format(0, 2, ",", ".")}}</td>
                    @endif
                    @if (in_array($pay->status,['PAID','SETTLED']))
                        <td>TERBAYARKAN</td>
                    @elseif (in_array($pay->status,['UNCONFIRMED','PENDING']))
                        <td>BELUM BAYAR</td>
                    @else
                        <td>EXPIRED</td>
                    @endif
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="10">Tidak ditemukan data</td>
                </tr>
            @endif
        @endisset
        @isset($paymentHomecare)
            @if (count($paymentHomecare)>0)
                @foreach ($paymentHomecare as $pay)
                    <tr>
                        <td>{{$no++}}</td>
                        <td>{{$pay->tanggal_order}}</td>
                        @if ($pay->no_rm)
                            <td>{{$pay->no_rm}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{$pay->nama}}</td>
                        <td>{{$pay->alamat}}</td>
                        @if ($pay->permintaan_hc)
                            @php
                                $layanan = '';
                            @endphp
                            @foreach ($pay->permintaan_hc->layanan_permintaan_hc as $phc)
                                @php
                                    if($layanan != '') {
                                        $layanan .= ',';
                                    }
                                    $layanan .=  $phc->layanan_hc->nama_layanan;
                                @endphp
                            @endforeach
                            <td>{{$layanan}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{"Rp. " . number_format($pay->nominal, 2, ",", ".");}}</td>
                        @if (in_array($pay->status,['SETTLED','PAID']))
                            <td>{{"Rp. " . number_format($pay->nominal, 2, ",", ".")}}</td>
                        @else
                            <td>{{"Rp. " . number_format("0", 2, ",", ".")}}</td>
                        @endif
                        @if (in_array($pay->status,['PAID','SETTLED']))
                            <td>TERBAYARKAN</td>
                        @elseif (in_array($pay->status,['UNCONFIRMED','PENDING']))
                            <td>BELUM BAYAR</td>
                        @else
                            <td>EXPIRED</td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9">Tidak ditemukan data</td>
                </tr>
            @endif
        @endisset
        @isset($paymentMcu)
            @if (count($paymentMcu)>0)
                @foreach ($paymentMcu as $pay)
                    <tr>
                        <td>{{$no++}}</td>
                        <td>{{$pay->tanggal_order}}</td>
                        @if ($pay->no_rm)
                            <td>{{$pay->no_rm}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{$pay->nama}}</td>
                        <td>{{$pay->jenis_mcu}}</td>
                        @if ($pay->permintaan_mcu)
                            @php
                                $layanan = '';
                            @endphp
                            @foreach ($pay->permintaan_mcu->layanan_permintaan_mcu as $pmcu)
                                @php
                                    if($layanan != '') {
                                        $layanan .= ',';
                                    }
                                    $layanan .=  $pmcu->layanan_mcu->nama_layanan;
                                @endphp
                            @endforeach
                            <td>{{$layanan}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{"Rp. " . number_format($pay->nominal, 2, ",", ".");}}</td>
                        @if (in_array($pay->status,['SETTLED','PAID']))
                            <td>{{"Rp. " . number_format($pay->nominal, 2, ",", ".")}}</td>
                        @else
                            <td>{{"Rp. " . number_format("0", 2, ",", ".")}}</td>
                        @endif
                        @if (in_array($pay->status,['PAID','SETTLED']))
                            <td>TERBAYARKAN</td>
                        @elseif (in_array($pay->status,['UNCONFIRMED','PENDING']))
                            <td>BELUM BAYAR</td>
                        @else
                            <td>EXPIRED</td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9">Tidak ditemukan data</td>
                </tr>
            @endif
        @endisset
        @isset($layananHomecare)
            @if (count($layananHomecare)>0)
                @foreach ($layananHomecare as $pay)
                    <tr>
                        <td>{{$no++}}</td>
                        <td>{{$pay->tanggal_order}}</td>
                        @if ($pay->no_rm)
                            <td>{{$pay->no_rm}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{$pay->nama}}</td>
                        <td>{{$pay->alamat}}</td>
                        @if ($pay->permintaan_hc)
                            @php
                                $layanan = '';
                            @endphp
                            @foreach ($pay->permintaan_hc->layanan_permintaan_hc as $phc)
                                @php
                                    if($layanan != '') {
                                        $layanan .= ',';
                                    }
                                    $layanan .=  $phc->layanan_hc->nama_layanan;
                                @endphp
                            @endforeach
                            <td>{{$layanan}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{$pay->tanggal_kunjungan}}</td>
                        <td>{{strtoupper($pay->status_pasien)}}</td>
                        <td>{{$pay->tanggal_kunjungan}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9">Tidak ditemukan data</td>
                </tr>
            @endif
        @endisset
        @isset($layananTelemedicine)
            @if (count($layananTelemedicine)>0)
                @foreach ($layananTelemedicine as $pay)
                    <tr>
                        <td>{{$no++}}</td>
                        <td>{{$pay->tanggal_order}}</td>
                        @if ($pay->no_rm)
                            <td>{{$pay->no_rm}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{$pay->nama}}</td>
                        @if ($pay->tmPoli)
                            <td>{{$pay->tmPoli->NamaPoli}}</td>
                        @else
                            <td>-</td>
                        @endif
                        @if ($pay->nakes)
                            <td>{{$pay->nakes->name}}</td>
                        @else
                            <td>-</td>
                        @endif
                        @if ($pay->nakes_perawat)
                            <td>{{$pay->nakes_perawat->name}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{$pay->tanggal_kunjungan}}</td>
                        <td>{{strtoupper($pay->status_pasien)}}</td>
                        <td>{{$pay->tanggal_kunjungan}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9">Tidak ditemukan data</td>
                </tr>
            @endif
        @endisset
        @isset($layananMcu)
            @if (count($layananMcu)>0)
                @foreach ($layananMcu as $pay)
                    <tr>
                        <td>{{$no++}}</td>
                        <td>{{$pay->tanggal_order}}</td>
                        @if ($pay->no_rm)
                            <td>{{$pay->no_rm}}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{$pay->nama}}</td>
                        <td>{{$pay->jenis_mcu}}</td>
                        @php
                            $layanan = '';
                        @endphp
                        @foreach ($pay->layanan_permintaan_mcu as $pmcu)
                            @php
                                if($layanan != '') {
                                    $layanan .= ',';
                                }
                                $layanan .=  $pmcu->layanan_mcu->nama_layanan;
                            @endphp
                        @endforeach
                        <td>{{$layanan}}</td>
                        <td>{{$pay->tanggal_kunjungan}}</td>
                        <td>{{strtoupper($pay->status_pasien)}}</td>
                        <td>{{$pay->tanggal_kunjungan}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9">Tidak ditemukan data</td>
                </tr>
            @endif
        @endisset
    </tbody>
</table>
