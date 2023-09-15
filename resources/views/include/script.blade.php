<!-- Bootstrap JS -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>
<!-- Select 2 -->
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
<!-- Ckeditor -->
<script src="{!! url('assets/ckeditor/ckeditor.js') !!}"></script>
<script src="{!! url('assets/ckeditor/adapters/jquery.js') !!}"></script>
<!--plugins-->
<script src="{{ asset('assets/js/jquery.min.js')}}"></script>
<script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js')}}"></script>
<script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js')}}"></script>
<script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
<script src="{{ asset('assets/plugins/chartjs/js/Chart.min.js')}}"></script>
<script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js')}}"></script>
<script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js')}}"></script>
<script src="{{ asset('assets/plugins/sparkline-charts/jquery.sparkline.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-knob/excanvas.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-knob/jquery.knob.js')}}"></script>
<script src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/index.js')}}"></script>
<!--app JS-->
<script src="{{ asset('assets/js/app.js')}}"></script>
<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	})
    
    $(function() {
        $(".knob").knob();
    });
</script>
@stack('script')