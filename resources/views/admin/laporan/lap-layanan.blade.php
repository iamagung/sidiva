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
                <li class="nav-item" role="presentation"> <!--Telemedis-->
                  <button class="nav-link" id="pills-telemedis-tab" data-bs-toggle="pill" data-bs-target="#pills-telemedis" type="button" role="tab" aria-controls="pills-telemedis" aria-selected="false">Telemedicine</button>
                </li>
                <li class="nav-item" role="presentation"> <!--MCU-->
                  <button class="nav-link" id="pills-mcu-tab" data-bs-toggle="pill" data-bs-target="#pills-mcu" type="button" role="tab" aria-controls="pills-mcu" aria-selected="false">Medical Check Up</button>
                </li>
                <li class="nav-item" role="presentation"> <!--EMergency-->
                    <button class="nav-link" id="pills-emergency-tab" data-bs-toggle="pill" data-bs-target="#pills-emergency" type="button" role="tab" aria-controls="pills-emergency" aria-selected="false">Call Emergency</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <!--Homecare-->
                <div class="tab-pane fade show active" id="pills-homecare" role="tabpanel" aria-labelledby="pills-homecare-tab">
                    
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Bulan</label>
                            <input type="text" value="{{date('m-Y')}}" name="pilih_bulan_homecare" id="pilih_bulan_homecare" class="form-control datepicker" style="display: inline-block;width: auto;text-align:center" autocomplete="off" readonly>
                        </div>
                        <div class="col-md-7"></div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary btn-sm float-end" onclick="exportLayanan('homecare')"><i class='bx bxs-report'></i> Export</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatabelHomecare" class="table table-striped table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Tanggal Order</td>
                                            <td>No. RM</td>
                                            <td>Nama Pasien</td>
                                            <td>Alamat Pasien</td>
                                            <td>Layanan Homecare</td>
                                            <td>Tanggal Mulai</td>
                                            <td>Status</td>
                                            <td>Tanggal Selesai</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Telemedis-->
                <div class="tab-pane fade" id="pills-telemedis" role="tabpanel" aria-labelledby="pills-telemedis-tab">
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Bulan</label>
                            <input type="text" value="{{date('m-Y')}}" name="pilih_bulan_telemedicine" id="pilih_bulan_telemedicine" class="form-control datepicker" style="display: inline-block;width: auto;text-align:center" autocomplete="off" readonly>
                        </div>
                        <div class="col-md-7"></div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary btn-sm float-end" onclick="exportLayanan('telemedicine')"><i class='bx bxs-report'></i> Export</button>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="datatabelTelemedicine" class="table table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Tanggal Order</td>
                                    <td>No. RM</td>
                                    <td>Nama Pasien</td>
                                    <td>Nama Poli</td>
                                    <td>Nama Dokter</td>
                                    <td>Nama Perawat</td>
                                    <td>Tanggal Mulai</td>
                                    <td>Status</td>
                                    <td>Tanggal Selesai</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--MCU-->
                <div class="tab-pane fade" id="pills-mcu" role="tabpanel" aria-labelledby="pills-mcu-tab">
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Bulan</label>
                            <input type="text" value="{{date('m-Y')}}" name="pilih_bulan_mcu" id="pilih_bulan_mcu" class="form-control datepicker" style="display: inline-block;width: auto;text-align:center" autocomplete="off" readonly>
                        </div>
                        <div class="col-md-7"></div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary btn-sm float-end" onclick="exportLayanan('mcu')"><i class='bx bxs-report'></i> Export</button>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="datatabelMcu" class="table table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Tanggal Order</td>
                                    <td>No. RM</td>
                                    <td>Nama Pemesan</td>
                                    <td>Jenis MCU</td>
                                    <td>Layanan MCU</td>
                                    <td>Tanggal Mulai</td>
                                    <td>Status</td>
                                    <td>Tanggal Selesai</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--Emergency-->
                <div class="tab-pane fade" id="pills-emergency" role="tabpanel" aria-labelledby="pills-emergency-tab">
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Bulan</label>
                            <input type="text" value="{{date('m-Y')}}" name="pilih_bulan" id="pilih_bulan" class="form-control datepicker" style="display: inline-block;width: auto;text-align:center" autocomplete="off" readonly>
                        </div>
                        <div class="col-md-7"></div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary btn-sm float-end" onclick="exportLayanan('')"><i class='bx bxs-report'></i> Export</button>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="datatabel" class="table table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>No. Telepon</td>
                                    <td>Tanggal</td>
                                    <td>Pukul</td>
                                    <td>Titik Lokasi Panggilan</td>
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
        var bulan = $('#pilih_bulan').val();
        var bulan_hc = $('#pilih_bulan_homecare').val();
        var bulan_tm = $('#pilih_bulan_telemedicine').val();
        var bulan_mcu = $('#pilih_bulan_mcu').val();
        tableHomecare(bulan_hc);
        tableTelemedicine(bulan_tm);
        tableMcu(bulan_mcu);
        filterByMonth();
    });
    // DataTable Homecare
    function tableHomecare(bulan) {
        var table = $('#datatabelHomecare').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('datatableHomecare') }}",
                type: "POST",
                data: {
                    bulan : bulan
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
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "modifyStatus", name: "modifyStatus"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"}
            ]
        });
    }
    // DataTable Telemedicine
    function tableTelemedicine(bulan) {
        var table = $('#datatabelTelemedicine').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('datatableTelemedicine') }}",
                type: "POST",
                data: {
                    bulan : bulan
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
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "modifyStatus", name: "modifyStatus"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"}
            ]
        });
    }
    // DataTable MCU
    function tableMcu(bulan) {
        var table = $('#datatabelMcu').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('datatableMcu') }}",
                type: "POST",
                data: {
                    bulan : bulan
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
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"},
                { data: "modifyStatus", name: "modifyStatus"},
                { data: "tanggal_kunjungan", name: "tanggal_kunjungan"}
            ]
        });
    }
    function filterByMonth() {
        $("#pilih_bulan").change(function (e) {
			e.preventDefault();
		});
        $("#pilih_bulan_telemedicine").change(function (e) {
			e.preventDefault();
            $('#datatabelTelemedicine').DataTable().destroy();
            tableTelemedicine($(this).val());
		});
        $("#pilih_bulan_mcu").change(function (e) {
			e.preventDefault();
            $('#datatabelMcu').DataTable().destroy();
            tableMcu($(this).val());
		});
        $("#pilih_bulan_homecare").change(function (e) {
			e.preventDefault();
            $('#datatabelHomecare').DataTable().destroy();
			tableHomecare($(this).val());
		});
    }

    function exportLayanan(params) {
        if(params == 'homecare') {
            var bulan_lap = $('#pilih_bulan_homecare').val();
        } else if(params == 'telemedicine') {
            var bulan_lap = $('#pilih_bulan_telemedicine').val();
        } else if(params == 'mcu') {
            var bulan_lap = $('#pilih_bulan_mcu').val();
        } else {
            var bulan_lap = $('#pilih_bulan').val();
        }
        var url = "{{route('exportLayanan',[':bulan',':layanan'])}}";
        url = url.replace(':bulan',bulan_lap);
        url = url.replace(':layanan',params);
        window.location = url;
    }
</script>
@endpush
