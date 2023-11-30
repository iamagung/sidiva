<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>INVOICE HOME CARE</title>
    <link rel="icon" href="{{ asset('assets/images/logo-rsu.png')}}" type="image/png" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300&display=swap" rel="stylesheet">
    <style type="text/css">
        body {
            margin-top: 10px;
            background: #eee;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body>
    <div class="container bootdey">
        <div class="row invoice row-printable">
            <div class="col-md-10">

                <div class="panel panel-default plain" id="dash_0">

                    <div class="panel-body p30">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="invoice-logo"><img width="70" src="{{asset('assets/images/logo-rsu.png')}}" alt="Invoice logo"></div>
                            </div>
                            <div class="col-lg-6">
                                <div class="invoice-from">
                                    <ul class="list-unstyled text-right">
                                        <li style="color: #5EB63E;">INVOICE</li>
                                        <li>{{!empty($payment->invoice_id)?$payment->invoice_id:'-'}}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="invoice-details mt25">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <strong>DITERBITKAN ATAS NAMA</strong>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-4">
                                            <strong>Pasien</strong>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            Penyedia <strong>RSUD WAHIDIN SUDIRO HUSODO</strong>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-4">
                                            <span>{{$data->nama}}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8"></div>
                                        <div class="col-lg-4">
                                            <span>Tanggal Transaksi :<br>{{date('Y-m-d')}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice-to mt25">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <strong>No. Registrasi</strong>
                                        </div>
                                        <div class="col-lg-3">
                                            <span>: {{$data->no_registrasi}}</span>
                                        </div>
                                        <div class="col-lg-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <strong>Jadwal Periksa</strong>
                                        </div>
                                        <div class="col-lg-3">
                                            <span>: {{date('Y-m-d', strtotime($data->tanggal_kunjungan))}}</span>
                                        </div>
                                        <div class="col-lg-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <strong>Jam Periksa </strong>
                                        </div>
                                        <div class="col-lg-3">
                                            <span>: -</span>
                                        </div>
                                        <div class="col-lg-6"></div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <strong>Tempat Layanan </strong>
                                        </div>
                                        <div class="col-lg-3">
                                            <span>: Lokasi Pasien</span>
                                        </div>
                                        <div class="col-lg-6"></div>
                                    </div>
                                </div>
                                {{-- <div class="invoice-attention" style="margin-top: 10px;">
                                    <span><strong>Silahkan lakukan pembayaran di poli MCU untuk dapat No. Antrian</strong></span>
                                </div> --}}
                                <div class="invoice-items" style="margin-top: 20px;">
                                    <div class="table-responsive" style="overflow: hidden; outline: none;" tabindex="0">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="per45 text-center">LAYANAN</th>
                                                    <th class="per5 text-center">JUMLAH</th>
                                                    <th class="per25 text-center">HARGA SATUAN</th>
                                                    <th class="per25 text-center">HARGA TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($layanan)>0)
                                                @foreach ($layanan as $lay => $val)
                                                <tr>
                                                    <td>{{$val->nama_layanan}}</td>
                                                    <td class="text-center">1</td>
                                                    <td class="text-center">Rp. {{$val->harga}}</td>
                                                    <td class="text-center">Rp. {{$val->harga}}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td>Biaya Ke Lokasi</td>
                                                    <td class="text-center">-</td>
                                                    <td class="text-center">Rp. {{$data->biaya_ke_lokasi}}</td>
                                                    <td class="text-center">Rp. {{$data->biaya_ke_lokasi}}</td>
                                                </tr>
                                                @else
                                                <span>Not result</span>
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-right">TOTAL HARGA:</th>
                                                    <th class="text-center">Rp. {{$payment->nominal}}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="invoice-metode">
                                    <ul class="list-unstyled">
                                        <li>METODE PEMBAYARAN</li>
                                        <li>{{$data->jenis_pembayaran}}</li>
                                    </ul>
                                </div>
                                <hr style="border: 1px solid #57AA39;">
                                <div class="invoice-info">
                                    <ul class="list-unstyled">
                                        <li>Invoice ini sah dan diproses oleh komputer <br> Silakan hubungi RSUD WAHIDIN SUDIRO HUSODO apabila kamu membutuhkan bantuan. <br><br> Terakhir diupdate: {{date('Y-M-d H:i:s')}} WIB</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    </script>
</body>
</html>