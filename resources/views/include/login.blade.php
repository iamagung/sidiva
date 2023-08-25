<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="{{ asset('assets/images/logo-rsu.png')}}" type="image/png" />
	<!--plugins-->
	<link href="{{ asset('assets/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet" />
	<link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" />
	<link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
	<!-- loader-->
	<link href="{{ asset('assets/css/pace.min.css')}}" rel="stylesheet" />
	<script src="{{ asset('assets/js/pace.min.js')}}"></script>
	<!-- Bootstrap CSS -->
	<link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/bootstrap-extended.css')}}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="{{ asset('assets/css/app.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/icons.css')}}" rel="stylesheet">
	<title>MCU - RSUWAHIDIN</title>
	<style>
		.bg-login {
			/* background: linear-gradient(119.65deg, #3EC396 31.86%, #59B596 95.51%); */
			background: linear-gradient(151deg, #C96AF6 0%, #EB20BE 100%);
		}
	</style>
</head>

<body class="bg-login">
	<!--wrapper-->
	<div class="wrapper">
		<div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
			<div class="container-fluid">
				<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
					<div class="col mx-auto">
						<div class="mb-4 text-center">
						</div>
						<div class="card">
							<div class="card-body">
								<div class="border p-4 rounded">
									<div class="topbar-logo-header mb-3">
                                        <div class="">
											<img src="{{ asset('assets/images/logo-sidiva.png')}}" class="logo-icon" alt="logo sidiva">
                                            <img src="{{ asset('assets/images/logo-rsu.png')}}" class="logo-icon" alt="logo rsu">
                                        </div>
                                        <div class="">
                                            <h5 style="font-size: 10pt; color: #000; font-weight: bold;" class="logo-text">RSUD</h5>
                                            <h5 style="font-size: 10pt; color: #000; font-weight: bold;" class="logo-text">dr. WAHIDIN SUDIRO HUSODO</h5>
                                        </div>
                                    </div>
									{{-- <div class="d-grid text-center">
										<img src="{{ asset('assets/images/login-images/logo.png')}}" class="center" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">
									</div> --}}
									<div class="mb-4">
                                        <span style="font-size: 9pt; font-weight: bold;">Silahkan masuk dengan username dan password anda!</span>
									</div>
									<div class="form-body">
										<form class="row g-3" action="{{url('proses_login')}}" method="POST" id="logForm">
											{{ csrf_field() }}
											<div class="col-12">
												@error('login_gagal')
												<div class="alert alert-warning alert-dismissible fade show" role="alert">
													<span class="alert-inner--text"><strong>Warning!</strong> {{ $message }}</span>
													<button type="button" class="close" data-dismiss="alert" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												@enderror

												<label class="form-label">Username</label>
												<input type="text" class="form-control" placeholder="Masukkan Username" name="username" autocomplete="off">
												@if($errors->has('username'))
                                                <span class="error">{{ $errors->first('username') }}</span>
                                                @endif
											</div>
											<div class="col-12">
												<label for="inputChoosePassword" class="form-label">Password</label>
												<div class="input-group" id="show_hide_password">
													<input type="password" class="form-control border-end-0" id="inputChoosePassword" name="password" placeholder="Masukkan Password" autocomplete="off"> <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
													@error('password')
													<div class="invalid-feedback">
														{{$message}}
													</div>
													@enderror
												</div>
											</div>
											<div class="col-12">
												<div class="d-grid">
													<button type="submit" class="btn btn-primary">Masuk</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--end row-->
			</div>
		</div>
	</div>
	<!--end wrapper-->
	<!-- Bootstrap JS -->
	<script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>
	<!--plugins-->
	<script src="{{ asset('assets/js/jquery.min.js')}}"></script>
	<script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js')}}"></script>
	<script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js')}}"></script>
	<script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
	<!--Password show & hide js -->
	<script>
		$(document).ready(function () {
			$("#show_hide_password a").on('click', function (event) {
				event.preventDefault();
				if ($('#show_hide_password input').attr("type") == "text") {
					$('#show_hide_password input').attr('type', 'password');
					$('#show_hide_password i').addClass("bx-hide");
					$('#show_hide_password i').removeClass("bx-show");
				} else if ($('#show_hide_password input').attr("type") == "password") {
					$('#show_hide_password input').attr('type', 'text');
					$('#show_hide_password i').removeClass("bx-hide");
					$('#show_hide_password i').addClass("bx-show");
				}
			});
		});
	</script>
	<!--app JS-->
	<script src="{{ asset('assets/js/app.js')}}"></script>
</body>

</html>