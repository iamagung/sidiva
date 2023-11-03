<!doctype html>
<html lang="en" class="color-header headercolor4">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{csrf_token()}}"> <!--csrfToken-->
	<title>SIDIVA - RSUWAHIDIN</title>
    @include('include.style') <!--importCSS-->
    <style>
        html.color-header .topbar-logo-header .logo-icon {
			filter: none !important;
		}
		html.headercolor4 .topbar {
			background: #8F49A9 !important;
		}
		.topbar-nav .metismenu a:hover, .topbar-nav .metismenu a:focus, .topbar-nav .metismenu a:active {
			color: #ffffff;
			text-decoration: none;
			background: #8F49A9;
		}
		/* .active {
			background: #8F49A9 !important;
		} */
		.nav-container {
			background: #A484B0 !important;
		}
		.menu-title {
			color: #fff !important;
		}
    </style>
</head>

<body>
	<div class="wrapper">
		@include('include.navbar')
		<div class="page-wrapper">
			@yield('content')
		</div>
		@include('include.footer')
	</div>

	@include('include.script') <!--importJavaScript-->
</body>
</html>
