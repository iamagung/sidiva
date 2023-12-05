@extends('layouts.index')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Or for RTL support -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
<!-- Datepicker -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
<style>
    .nav-link {
        color: #000 !important;
    }
    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        color: #fff !important;
        background-color: #be7dd3 !important;
    }
</style>
@endpush
@section('content')
<div class="page-content">
    <!-- judul dan link -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="pe-3">
            <span style="font-weight: bold;">{{$title}}<span>
        </div>
    </div>
    <!-- main content -->
    <div class="card main-layer">
        <div class="card-body">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation"> <!--Homecare-->
                  <button class="nav-link active" id="pills-homecare-tab" data-bs-toggle="pill" data-bs-target="#pills-homecare" type="button" role="tab" aria-controls="pills-homecare" aria-selected="true">Homecare</button>
                </li>
                <li class="nav-item" role="presentation"> <!--Telemedicine-->
                  <button class="nav-link" id="pills-telemedicine-tab" data-bs-toggle="pill" data-bs-target="#pills-telemedicine" type="button" role="tab" aria-controls="pills-telemedicine" aria-selected="false">Telemedicine</button>
                </li>
                <li class="nav-item" role="presentation"> <!--MCU-->
                  <button class="nav-link" id="pills-mcu-tab" data-bs-toggle="pill" data-bs-target="#pills-mcu" type="button" role="tab" aria-controls="pills-mcu" aria-selected="false">Medical Check Up</button>
                </li>
            </ul>
            <hr>
            <div class="tab-content" id="pills-tabContent">
                <!--Homecare-->
                <div class="tab-pane fade show active" id="pills-homecare" role="tabpanel" aria-labelledby="pills-homecare-tab">
                    <div class="row mb-3">
                        <div class="col-md-1">
                            <div class="float-end" style="margin-top: 5px;">Bulan</div>
                        </div>
                        <div class="col-md-2">
                            <input type="text" value="{{date('m-Y')}}" name="bulan_awal" id="bulan_awal" class="form-control datepicker" style="text-align:center" autocomplete="off" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="text" value="{{date('m-Y')}}" name="bulan_akhir" id="bulan_akhir" class="form-control datepicker" style="text-align:center" autocomplete="off" readonly>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Total Pendapatan</label>
                                <button type="button" class="btn btn-primary btn-sm" id="totalHomecare">Rp. 0,00</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary btn-sm float-end" onclick="exportExcel('homecare')"><i class='bx bxs-report'></i> Export</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatabelHomecareKeuangan" class="table table-striped table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Tanggal Order</td>
                                            <td>No. RM</td>
                                            <td>Nama Pasien</td>
                                            <td>Alamat Pasien</td>
                                            <td>Layanan Homecare</td>
                                            <td>Tagihan</td>
                                            <td>Total Dibayarkan</td>
                                            <td>Status</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Telemedicine-->
                <div class="tab-pane fade" id="pills-telemedicine" role="tabpanel" aria-labelledby="pills-telemedicine-tab">
                    <div class="row mb-3">
                        <div class="row mb-3">
                            <div class="col-md-1">
                                <div class="float-end" style="margin-top: 5px;">Bulan</div>
                            </div>
                            <div class="col-md-2">
                                <input type="text" value="{{date('m-Y')}}" name="bulan_awal_telemedicine" id="bulan_awal_telemedicine" class="form-control datepicker" style="text-align:center" autocomplete="off" readonly>
                            </div>
                            <div class="col-md-2">
                                <input type="text" value="{{date('m-Y')}}" name="bulan_akhir_telemedicine" id="bulan_akhir_telemedicine" class="form-control datepicker" style="text-align:center" autocomplete="off" readonly>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Total Pendapatan</label>
                                    <button type="button" class="btn btn-primary btn-sm" id="totaltelemedicine">Rp. 0,00</button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-secondary btn-sm float-end" onclick="exportExcel('telemedicine')"><i class='bx bxs-report'></i> Export</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatabelTelemedicineKeuangan" class="table table-striped table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Tanggal Order</td>
                                            <td>No. RM</td>
                                            <td>Nama Pasien</td>
                                            <td>Nama Poli</td>
                                            <td>Nama Dokter</td>
                                            <td>Nama Perawat</td>
                                            <td>Tagihan</td>
                                            <td>Total Dibayarkan</td>
                                            <td>status</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--MCU-->
                <div class="tab-pane fade" id="pills-mcu" role="tabpanel" aria-labelledby="pills-mcu-tab">
                    <div class="row mb-3">
                        <div class="col-md-1">
                            <div class="float-end" style="margin-top: 5px;">Bulan</div>
                        </div>
                        <div class="col-md-2">
                            <input type="text" value="{{date('m-Y')}}" name="bulan_awal_mcu" id="bulan_awal_mcu" class="form-control datepicker" style="text-align:center" autocomplete="off" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="text" value="{{date('m-Y')}}" name="bulan_akhir_mcu" id="bulan_akhir_mcu" class="form-control datepicker" style="text-align:center" autocomplete="off" readonly>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Total Pendapatan</label>
                                <button type="button" class="btn btn-primary btn-sm" id="totalMcu">Rp. 0,00</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary btn-sm float-end" onclick="exportExcel('mcu')"><i class='bx bxs-report'></i> Export</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatabelMcuKeuangan" class="table table-striped table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Tanggal Order</td>
                                            <td>No. RM</td>
                                            <td>Nama Pemesan</td>
                                            <td>Jenis MCU</td>
                                            <td>Layanan MCU</td>
                                            <td>Tagihan</td>
                                            <td>Total Dibayarkan</td>
                                            <td>Status</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".datepicker").datepicker( {
            format: "mm-yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true
        });
        // Homecare
        var awal = $('#bulan_awal').val();
        var akhir = $('#bulan_akhir').val();
        tableHomecare(awal, akhir);
        filterByMonth();
        // Homecare
        var awal_mcu = $('#bulan_awal_mcu').val();
        var akhir_mcu = $('#bulan_akhir_mcu').val();
        tableMcu(awal_mcu, akhir_mcu);
        filterByMonthMcu();
        // Telemedicine
        var awal_telemedicine = $('#bulan_awal_telemedicine').val();
        var akhir_telemedicine = $('#bulan_akhir_telemedicine').val();
        tableTelemedicine(awal_telemedicine, akhir_telemedicine);
        filterByMonthTelemedicine();
    });
    function tableHomecare(awal,akhir) {
        var table = $('#datatabelHomecareKeuangan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('datatableHomecareKeuangan') }}",
                type: "POST",
                data: {
                    awal  : awal,
                    akhir : akhir
                },
                error: function(xhr, errorType, exception) {
                    console.log(xhr.responseText); // Pesan kesalahan dari server
                }
            },

            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "tanggal_order", name: "tanggal_order"},
                { data: "modifyNorm", name: "modifyNorm", class: "text-center"},
                { data: "nama", name: "nama"},
                { data: "alamat", name: "alamat"},
                { data: "modifyLayanan", name: "modifyLayanan"},
                { data: "modfiyTagihan", name: "modfiyTagihan"},
                { data: "modifyTerbayarkan", name: "modifyTerbayarkan"},
                { data: "modifyStatus", name: "modifyStatus"}
            ]
        });
        table.on('xhr.dt', function ( e, settings, json, xhr ) {
            // console.log(json);
            if(json.data.length > 0) {
                $('#totalHomecare').text(json.data[0].total);
            }
        } )
    }
    // DataTable Telemedicine
    function tableTelemedicine(awaltelemedicine,akhirtelemedicine) {
        var table = $('#datatabelTelemedicineKeuangan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('datatableTelemedicineKeuangan') }}",
                type: "POST",
                data: {
                    awal : awaltelemedicine,
                    akhir : akhirtelemedicine
                },
                error: function(xhr, errorType, exception) {
                    console.log(xhr.responseText); // Pesan kesalahan dari server
                }
            },

            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "tanggal_order", name: "tanggal_order"},
                { data: "modifyNorm", name: "modifyNorm", class: "text-center"},
                { data: "nama", name: "nama"},
                { data: "modifyPoli", name: "modifyPoli"},
                { data: "modifyDokter", name: "modifyDokter"},
                { data: "modifyPerawat", name: "modifyPerawat"},
                { data: "modifyTagihan", name: "modifyTagihan"},
                { data: "modifyTerbayarkan", name: "modifyTerbayarkan"},
                { data: "modifyStatus", name: "modifyStatus"},
            ]
        })
        // table.on('xhr', function() {
        //     var test = table.ajax.json().tes;
        //     $('#totaltelemedicine').text(test);
        // });
        table.on('xhr.dt', function ( e, settings, json, xhr ) {
            // console.log(json);
            if(json.data.length > 0) {
                $('#totaltelemedicine').text(json.data[0].tes);
            }
        } )
    }
    // DataTable MCU
    function tableMcu(awalmcu, akhirmcu) {
        var table = $('#datatabelMcuKeuangan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('datatableMcuKeuangan') }}",
                type: "POST",
                data: {
                    awal  : awalmcu,
                    akhir : akhirmcu
                },
                error: function(xhr, errorType, exception) {
                    console.log(xhr.responseText); // Pesan kesalahan dari server
                }
            },

            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex"},
                { data: "tanggal_order", name: "tanggal_order"},
                { data: "modifyNorm", name: "modifyNorm", class: "text-center"},
                { data: "nama", name: "nama"},
                { data: "modifyJenis", name: "modifyJenis"},
                { data: "modifyLayanan", name: "modifyLayanan"},
                { data: "modfiyTagihan", name: "modfiyTagihan"},
                { data: "modifyTerbayarkan", name: "modifyTerbayarkan"},
                { data: "modifyStatus", name: "modifyStatus"},
            ]
        });
        table.on('xhr.dt', function ( e, settings, json, xhr ) {
            // console.log(json);
            if(json.data.length > 0) {
                $('#totalMcu').text(json.data[0].total);
            }
        } )
    }
    //filter between two month HOMECARE
    function filterByMonth() {
        $("#bulan_awal").change(function (e) {
			e.preventDefault();
            $('#datatabelHomecareKeuangan').DataTable().destroy();
			tableHomecare($(this).val(),$('#bulan_akhir').val());
		});
        $("#bulan_akhir").change(function (e) {
			e.preventDefault();
            $('#datatabelHomecareKeuangan').DataTable().destroy();
			tableHomecare($('#bulan_awal').val(),$(this).val());
		});
    }
    //filter between two month MCU
    function filterByMonthMcu() {
        $("#bulan_awal_mcu").change(function (e) {
			e.preventDefault();
            $('#datatabelMcuKeuangan').DataTable().destroy();
			tableMcu($(this).val(),$('#bulan_akhir_mcu').val());
		});
        $("#bulan_akhir_mcu").change(function (e) {
			e.preventDefault();
            $('#datatabelMcuKeuangan').DataTable().destroy();
			tableMcu($('#bulan_awal_mcu').val(),$(this).val());
		});
    }
    //filter between two month Telemedicine
    function filterByMonthTelemedicine() {
        $("#bulan_awal_telemedicine").change(function (e) {
			e.preventDefault();
            $('#datatabelTelemedicineKeuangan').DataTable().destroy();
			tableTelemedicine($(this).val(),$('#bulan_akhir_telemedicine').val());
		});
        $("#bulan_akhir_telemedicine").change(function (e) {
			e.preventDefault();
            $('#datatabelTelemedicineKeuangan').DataTable().destroy();
			tableTelemedicine($('#bulan_awal_telemedicine').val(),$(this).val());
		});
    }

    function exportExcel(params) {
        console.log(params);
        if(params=='homecare'){
            var bulan_awal = $('#bulan_awal').val();
            var bulan_akhir = $('#bulan_akhir').val();
        } else if (params=='telemedicine') {
            var bulan_awal = $('#bulan_awal_telemedicine').val();
            var bulan_akhir = $('#bulan_akhir_telemedicine').val();
        } else {
            var bulan_awal = $('#bulan_awal_mcu').val();
            var bulan_akhir = $('#bulan_akhir_mcu').val();
        }
        var url = "{{route('exportKeuangan',[':bulan_awal',':bulan_akhir',':layanan'])}}";
        url = url.replace(':bulan_awal',bulan_awal);
        url = url.replace(':bulan_akhir',bulan_akhir);
        url = url.replace(':layanan',params);
        window.location = url;
    }
</script>
@endpush
