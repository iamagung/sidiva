@extends('layouts.index')

@push('style')
<style>
    .card-body {
        background: #ECEDEF !important;
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <!-- judul dan link -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="pe-3">
            <span style="font-weight: bold">Selamat Datang!<span>
        </div>
    </div>

    <!-- main content -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col-md-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="mb-0" style="font-size: 8pt; font-weight: bold">PERMINTAAN HOMECARE</span>
                        <div class="ms-auto">
                            <div class="radius-10" style="background: #D9D9D9">
                                <i class="bx bx-desktop fs-3" style="color: #ffffff;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span id="ttlPermintaanHC" style="font-weight: bold; font-size: 14pt; color: #000;">0</span>
                    </div>
                    <div class="float-left">
                        <span id="presPermintaanHC" style="background: #A484B0; border-radius: 3px; color: #fff;" class="mb-0 ms-auto">+0%</span>
                        <span style="font-size: 7pt">Dari Bulan Sebelumnya</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
    </div>
    <!--end row-->
</div>
@endsection

@push('script')
<script type="text/javascript">
    $.get("{{ route('getDashboardTelemedicine') }}").done(function(result){
        $('#ttlPermintaanHC').text(result.data.ttlPermintaanHC);
    });
</script>
@endpush