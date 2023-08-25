<!--start header wrapper-->
<div class="header-wrap-per">
	<!--start header -->
	<header>
		<div class="topbar d-flex align-items-center">
				<nav class="navbar navbar-expand">
					 <div class="topbar-logo-header">
						<div class="">
							<img src="{{ asset('assets/images/logo-rsu.png')}}" class="logo-icon" alt="logo icon">
					  </div>
					  <div class="">
							<h5 style="font-size: 8pt" class="logo-text">RSUD</h5>
							<h5 style="font-size: 8pt" class="logo-text">dr. WAHIDIN SUDIRO HUSODO</h5>
					  </div>
					 </div>
					 <div class="mobile-toggle-menu"><i class='bx bx-menu'></i></div>
					 {{-- <div class="search-bar flex-grow-1">
						  <div class="position-relative search-bar-box">
								<input type="text" class="form-control search-control" placeholder="Type to search..."> <span class="position-absolute top-50 search-show translate-middle-y"><i class='bx bx-search'></i></span>
								<span class="position-absolute top-50 search-close translate-middle-y"><i class='bx bx-x'></i></span>
						  </div>
					 </div> --}}
					 <div class="top-menu ms-auto">
						  <ul class="navbar-nav align-items-center">
								<li class="nav-item mobile-search-icon">
									<a class="nav-link" href="#">
										<i class='bx bx-search'></i>
									</a>
								</li>
								
								<li class="nav-item dropdown dropdown-large">
									 <a class="nav-link" onclick="alert('Maintenance')" href="#">
									 {{-- <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span class="alert-count">7</span> --}}
										  <i class='bx bx-bell'></i>
									 </a>
									 <div class="dropdown-menu dropdown-menu-end">
										  <a href="javascript:;">
												<div class="msg-header">
													 <p class="msg-header-title">Notifications</p>
													 <p class="msg-header-clear ms-auto">Marks all as read</p>
												</div>
										  </a>
										  <div class="header-notifications-list">
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-primary text-primary"><i class="bx bx-group"></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">New Customers<span class="msg-time float-end">14 Sec
														  ago</span></h6>
																<p class="msg-info">5 new user registered</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-danger text-danger"><i class="bx bx-cart-alt"></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">New Orders <span class="msg-time float-end">2 min
														  ago</span></h6>
																<p class="msg-info">You have recived new orders</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-success text-success"><i class="bx bx-file"></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">24 PDF File<span class="msg-time float-end">19 min
														  ago</span></h6>
																<p class="msg-info">The pdf files generated</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-warning text-warning"><i class="bx bx-send"></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Time Response <span class="msg-time float-end">28 min
														  ago</span></h6>
																<p class="msg-info">5.1 min avarage time response</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-info text-info"><i class="bx bx-home-circle"></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">New Product Approved <span
														  class="msg-time float-end">2 hrs ago</span></h6>
																<p class="msg-info">Your new product has approved</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-danger text-danger"><i class="bx bx-message-detail"></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">New Comments <span class="msg-time float-end">4 hrs
														  ago</span></h6>
																<p class="msg-info">New customer comments recived</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-success text-success"><i class='bx bx-check-square'></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Your item is shipped <span class="msg-time float-end">5 hrs
														  ago</span></h6>
																<p class="msg-info">Successfully shipped your item</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-primary text-primary"><i class='bx bx-user-pin'></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">New 24 authors<span class="msg-time float-end">1 day
														  ago</span></h6>
																<p class="msg-info">24 new authors joined last week</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="notify bg-light-warning text-warning"><i class='bx bx-door-open'></i>
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Defense Alerts <span class="msg-time float-end">2 weeks
														  ago</span></h6>
																<p class="msg-info">45% less alerts last 4 weeks</p>
														  </div>
													 </div>
												</a>
										  </div>
										  <a href="javascript:;">
												<div class="text-center msg-footer">View All Notifications</div>
										  </a>
									 </div>
								</li>
								<li class="nav-item dropdown dropdown-large">
									 <a class="nav-link" onclick="alert('Maintenance')" href="#">
									 {{-- <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span class="alert-count">8</span> --}}
										  <i class='bx bx-comment'></i>
									 </a>
									 <div class="dropdown-menu dropdown-menu-end">
										  <a href="javascript:;">
												<div class="msg-header">
													 <p class="msg-header-title">Messages</p>
													 <p class="msg-header-clear ms-auto">Marks all as read</p>
												</div>
										  </a>
										  <div class="header-message-list">
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-1.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Daisy Anderson <span class="msg-time float-end">5 sec
														  ago</span></h6>
																<p class="msg-info">The standard chunk of lorem</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-2.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Althea Cabardo <span class="msg-time float-end">14
														  sec ago</span></h6>
																<p class="msg-info">Many desktop publishing packages</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-3.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Oscar Garner <span class="msg-time float-end">8 min
														  ago</span></h6>
																<p class="msg-info">Various versions have evolved over</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-4.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Katherine Pechon <span class="msg-time float-end">15
														  min ago</span></h6>
																<p class="msg-info">Making this the first true generator</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-5.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Amelia Doe <span class="msg-time float-end">22 min
														  ago</span></h6>
																<p class="msg-info">Duis aute irure dolor in reprehenderit</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-6.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Cristina Jhons <span class="msg-time float-end">2 hrs
														  ago</span></h6>
																<p class="msg-info">The passage is attributed to an unknown</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-7.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">James Caviness <span class="msg-time float-end">4 hrs
														  ago</span></h6>
																<p class="msg-info">The point of using Lorem</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-8.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Peter Costanzo <span class="msg-time float-end">6 hrs
														  ago</span></h6>
																<p class="msg-info">It was popularised in the 1960s</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-9.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">David Buckley <span class="msg-time float-end">2 hrs
														  ago</span></h6>
																<p class="msg-info">Various versions have evolved over</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-10.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Thomas Wheeler <span class="msg-time float-end">2 days
														  ago</span></h6>
																<p class="msg-info">If you are going to use a passage</p>
														  </div>
													 </div>
												</a>
												<a class="dropdown-item" href="javascript:;">
													 <div class="d-flex align-items-center">
														  <div class="user-online">
																<img src="{{ asset('assets/images/avatars/avatar-11.png')}}" class="msg-avatar" alt="user avatar">
														  </div>
														  <div class="flex-grow-1">
																<h6 class="msg-name">Johnny Seitz <span class="msg-time float-end">5 days
														  ago</span></h6>
																<p class="msg-info">All the Lorem Ipsum generators</p>
														  </div>
													 </div>
												</a>
										  </div>
										  <a href="javascript:;">
												<div class="text-center msg-footer">View All Messages</div>
										  </a>
									 </div>
								</li>
						  </ul>
					 </div>
					 <div class="user-box dropdown">
						<a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<img src="{{ asset('assets/images/avatars/avatar-9.png')}}" class="user-img" alt="user avatar">
							<div class="user-info ps-3">
								 <p class="user-name mb-0">{{Auth::User()->name}}</p>
								 <p class="designattion mb-0">{{Auth::User()->level}}</p>
							</div>
					  </a>
					  <ul class="dropdown-menu dropdown-menu-end">
							<li>
								 <a class="dropdown-item" href="javascript:;"><i class="bx bx-user"></i><span>Profile</span></a>
							</li>
							<li>
								 <a class="dropdown-item" href="javascript:;" onclick="alert('Maintenance!')"><i class="bx bx-cog"></i><span>Settings</span></a>
							</li>
							<li>
								 <div class="dropdown-divider mb-0"></div>
							</li>
							<li>
								 <a class="dropdown-item" href="{{ Route('logout')}}"><i class='bx bx-log-out-circle'></i><span>Logout</span></a>
							</li>
					  </ul>
					 </div>
				</nav>
		  </div>
	 </header>
	 <!--end header -->

	 <!--start navigation-->
	 <div class="nav-container">
		  <!-- logo tampilan resposif mobile -->
		  <div class="mobile-topbar-header">
				<div>
                    <img src="{{ asset('assets/images/logo-rsu.png')}}" class="logo-icon" alt="logo icon">
				</div>
				<div>
                    <h4 class="logo-text">MCU</h4>
				</div>
				<div class="toggle-icon ms-auto">
                    <i class='bx bx-arrow-to-left'></i>
				</div>
		  </div>
		  <!-- main menu -->
		  <nav class="topbar-nav">
			<ul class="metismenu" id="menu">
                <li>
                    <a href="{{ Route('dashboard')}}">
                        <div class="parent-icon text-white"><i class='bx bx-home'></i>
                        </div>
                        <div class="menu-title">Dashboard</div>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow text-white">
                        <div class="parent-icon text-white"><i class="bx bx-first-aid"></i>
                        </div>
                        <div class="menu-title">Homecare</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('mainPermintaanHC')}}"><i class="bx bx-right-arrow-alt"></i>Permintaan Baru</a>
                        </li>
                        <li>
                            <a href="{{ route('mainRiwayatHC')}}"><i class="bx bx-right-arrow-alt"></i>Riwayat Home Care</a>
                        </li>
                        <li>
                            {{-- <a href="{{ route('indexLayananHC')}}"><i class="bx bx-right-arrow-alt"></i>Jenis Layanan</a> --}}
							<a href="{{ route('mainLayananHC')}}"><i class="bx bx-right-arrow-alt"></i>Jenis Layanan</a>
                        </li>
                        <li>
                            <a href="{{ route('mainPaketHC')}}"><i class="bx bx-right-arrow-alt"></i>Paket Home Care</a>
                        </li>
                        <li>
                            <a href="{{ route('mainTenagaMedis')}}"><i class="bx bx-right-arrow-alt"></i>Tenaga Medis</a>
                        </li>
						<li>
                            <a href="{{ route('formPengaturanHC')}}"><i class="bx bx-right-arrow-alt"></i>Pengaturan</a>
                        </li>
						<li>
                            <a href="{{ route('mainSyaratHC')}}"><i class="bx bx-right-arrow-alt"></i>Syarat & Aturan</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0)" class="has-arrow text-white">
                        <div class="parent-icon text-white"><i class="bx bx-first-aid"></i>
                        </div>
                        <div class="menu-title">Medical Check Up</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ Route('mainPermintaanMcu')}}"><i class="bx bx-right-arrow-alt"></i>Permintaan MCU</a>
                        </li>
                        <li>
                            <a href="{{ Route('mainRiwayatMcu')}}"><i class="bx bx-right-arrow-alt"></i>Riwayat MCU</a>
                        </li>
                        <li>
                            <a href="{{ Route('mainLayananMcu')}}"><i class="bx bx-right-arrow-alt"></i>Layanan MCU</a>
                        </li>
						<li>
                            <a href="{{ route('formPengaturanMCU')}}"><i class="bx bx-right-arrow-alt"></i>Pengaturan</a>
                        </li>
						<li>
                            <a href="{{ route('mainSyaratMcu')}}"><i class="bx bx-right-arrow-alt"></i>Syarat & Aturan</a>
                        </li>
                    </ul>
                </li>
				<li>
                    <a href="{{ Route('mainPengguna')}}">
                        <div class="parent-icon text-white"><i class='bx bxs-user'></i></i>
                        </div>
                        <div class="menu-title">User TM</div>
                    </a>
                </li>
			</ul>
	  </nav>
		  <!-- /main menu -->
	 </div>
	 <!-- !navigation -->

</div>
<!--end header wrapper-->