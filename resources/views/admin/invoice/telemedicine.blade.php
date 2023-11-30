<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>INVOICE TELEMEDICINE</title>
    <link rel="icon" href="{{ asset('assets/images/logo-rsu.png')}}" type="image/png" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300&display=swap" rel="stylesheet">
    <style type="text/css">
    {{file_get_contents("assets/css/bootstrap.css")}}
        body {
            margin-top: 10px;
            background: #eee;
            font-family: 'Poppins', sans-serif;
            font-size: 8pt;
            max-width: 500px;
            height: fit-content !important;
            margin-left: auto !important;
            margin-right: auto !important;
            justify-self: center !important;
        }
        th {
            border-bottom: 1px solid #57AA39 !important;
        }
        td {
            border: 0;
        }
    </style>
</head>

<body>
    @php
        function rupiah($angka){
            $hasilRupiah = "Rp. " . number_format((int)$angka);
            return str_replace(',', '.', $hasilRupiah);
        }
    @endphp
    <div class="card">
        <div class="card-body">
            <div class="position-relative w-100" style="position:relative;height:60px;">
                <div class="position-absolute top-0 start-0 text-white">
                    <img width="70" src="data:image/png;base64,<?php echo base64_encode(file_get_contents('assets/images/logo-rsu.png')); ?>" alt="Invoice logo">
                </div>
                <div class="position-absolute bottom-0 end-0 text-end">
                    <span style="color: #5EB63E;" class="d-block fw-bold">INVOICE</span>
                    <span>{{$payment->invoice_id}}</span>
                </div>
            </div>
            <div class="row pt-2 mt-2" style="position: relative;min-height:120px;top:40px">
                <div class="position-absolute top-0 start-0" style="width: 55%;">
                    <strong>DITERBITKAN ATAS NAMA</strong>
                    <br>
                    <span>Penyedia <strong>RSUD WAHIDIN SUDIRO HUSODO</strong></span>
                </div>
                <div class="position-absolute top-0 end-0" style="width: 40%">
                    <span><strong>Pasien</strong></span>
                    <br>
                    <span>{{$permintaan->nama}}</span>
                    <br>
                    <span>Tanggal Transaksi :<br>{{date('d F Y', strtotime($payment->created_at))}}</span>
                </div>
            </div>
            <div class="d-flex gap-2 pt-2" style="position: relative">
                <div class="position-absolute top-0 start-0" style="width: 25%">
                    <span><strong>No. Registrasi</strong></span>
                    <br>
                    <span><strong>Jadwal Periksa</strong></span>
                    <br>
                    <span><strong>Jam Periksa</strong></span>
                </div>
                <div class="position-absolute top-0 end-0" style="width: 75%">
                    <span>: {{$permintaan->no_registrasi}}</span>
                    <br>
                    <span>: {{$permintaan->tanggal_kunjungan}}</span>
                    <br>
                    <span>: {{$permintaan->jadwal_dokter}}</span>
                </div>
            </div>
            <div class="row pt-5" style="padding-top:1rem;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">LAYANAN</th>
                            <th class="text-center">JUMLAH</th>
                            <th class="text-center">HARGA SATUAN</th>
                            <th class="text-center">HARGA TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($layanan) > 0)
                        @foreach ($layanan as $ly)
                        <tr>
                            <td>{{$ly->nama_layanan}}</td>
                            <td class="text-center">1</td>
                            <td class="text-center">{{rupiah($ly->harga)}},00</td>
                            <td class="text-center">{{rupiah($ly->harga)}},00</td>
                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                    </tbody>
                    <tfoot >
                        <tr >
                            <th colspan="3" class="text-end" style="padding-top: 40px;">TOTAL HARGA:</th>
                            <th colspan="1" class="text-center" style="padding-top: 40px;">{{rupiah($permintaan->biaya_layanan)}},00</th>
                        </tr>
                    </tfoot>
                </table>
                <div class="invoice-metode">
                    <ul class="list-unstyled">
                        <li>METODE PEMBAYARAN</li>
                        <li>{{$permintaan->metode_pembayaran}}</li>
                    </ul>
                </div>
                <hr style="border: 1px solid #57AA39;">
                <div class="invoice-info">
                    <ul class="list-unstyled">
                        <li>Invoice ini sah dan diproses oleh komputer <br> Silakan hubungi <b>RSUD WAHIDIN SUDIRO HUSODO</b> apabila kamu membutuhkan bantuan. <br><br> Terakhir diupdate: {{$payment->updated_at}} WIB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @isset($isView)
        <button onclick="download()" style="background-color:#CCDDC6;" class="text-primary text-center w-100 btn fw-bold mt-2">UNDUH</button>
    @endisset
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    {{-- <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <script>
        function download() {
            window.open(
                "{{route('invoiceDownload', ['id'=>$permintaan->id_permintaan_telemedicine,'jenis'=>'telemedicine'])}}",
                "_blank"
            );
        }
    </script>
    <script type="text/javascript">
    </script>
</body>
</html>
