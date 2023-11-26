<?php session_start();
include "config.php";
include "cart.php";
$email = $_SESSION['email'];

?>
<!DOCTYPE html>
<html lang="zxx">

<head>
	<!-- Basic Page Needs -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>About Us</title>

	<meta name="keywords" content="Organic, Fresh Food, Farm Store">
	<meta name="description" content="FreshMart - Organic, Fresh Food, Farm Store HTML Template">
	<meta name="author" content="tivatheme">

	<!-- Favicon -->
	<link rel="shortcut icon" href="img/favicon.png" type="image/png">

	<!-- Mobile Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:300,400,700" rel="stylesheet">

	<!-- Vendor CSS -->
	<link rel="stylesheet" href="asset/bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="asset/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="asset/font-material/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" href="asset/nivo-slider/css/nivo-slider.css">
	<link rel="stylesheet" href="asset/nivo-slider/css/animate.css">
	<link rel="stylesheet" href="asset/nivo-slider/css/style.css">
	<link rel="stylesheet" href="asset/owl.carousel/assets/owl.carousel.min.css">
	<link rel="stylesheet" href="asset/slider-range/css/jslider.css">
	<!--extra asset-->
	<link rel="stylesheet" href="asset/bootstrap-5.0.2-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="asset/fontawesome-free-6.4.0-web/css/all.min.css">
	<!-- Template CSS -->
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/reponsive.css">
</head>

<body class="home home-1">
	<div id="all">
		<!-- Header -->
		<!-- Header Top -->
		<div class="header-top">
			<div class="container">
				<div class="row mt-4 mb-2">
					<!-- mobile menu -->
					<div class="mobile-toggler d-lg-none">
						<a href="#" data-bs-toggle="modal" data-bs-target="#navModal">
							<i class="fa-solid fa-bars"></i>
						</a>
					</div>
					<!-- Logo -->
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mx-auto">
						<div class="logo">
							<a href="homepage.html">
								<img class="img-responsive" src="img/logo.png" alt="Logo">
							</a>
						</div>

					</div>
				</div>
				<div class="row d-flex align-items-center mt-0 mb-3">
					<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 mx-auto">
						<!-- Search -->
						<div class="form-search me-auto">
							<form action="#" method="get">
								<input type="text" class="form-input" placeholder="Search">
								<button type="submit" class="fa fa-search"></button>
							</form>
						</div>
						<!-- Cart -->

					</div>
				</div>
			</div>
		</div>
		<!-- Main Menu -->
		<div class="row align-items-center" id="sub-page-navMenu">
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 mx-auto">
				<header id="hehe">
					<div class="container-fluid-light ">
						<div class="nav-items d-none d-lg-flex">
							<div class="item">
								<a href="homepage.php">Home</a>
							</div>
							<div class="item">
								<a href="product-list-left-sidebar.php">Product</a>
							</div>
							<div class="item">
								<a href="aboutUs.php">About Us</a>
							</div>
							<div class="item">
								<a href="contact.php">Contact</a>
							</div>
							<div class="item">
								<a href="orders.php">Orders</a>
							</div>
							<div class="item">
								<div class="dropdown account">
									<div class="dropdown-toggle" data-toggle="dropdown">
										<a href="">Profile</a>
									</div>
									<div class="dropdown-menu">
										<?php

										if (isset($_SESSION['email'])) {
											// User is logged in, show logout button
											echo '
                                                        <div class="item">
                                                            <a href="account.php" title="Account">Account</a>
                                                        </div>
                                                        <div class="item">
                                                            <a href="logout.php" title="Logout">Logout</a>
                                                        </div>
                                                        ';
										} else {
											// User is not logged in, show login and register links
											echo '
                                                        <div class="item">
                                                            <a href="login.php" title="Login">Login</a>
                                                        </div>
                                                        <div class="item">
                                                            <a href="user-register.php" title="Register">Register</a>
                                                        </div>
                                                        ';
										}
										?>
									</div>
								</div>
							</div>

						</div>

						<!-- Modal -->
						<div class="modal" id="navModal" tabindex="-1" aria-labelledby="exampleModalLabel"
							aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<img src="img/logo.png" alt="">
										<button class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
												class="fa-solid fa-xmark"></i></button>
									</div>

									<div class="modal-body">

										<div class="modal-line">
											<i class="fa-solid fa-house"></i><a href="homepage.php">Home</a>
										</div>

										<div class="modal-line">
											<i class="fa-solid fa-bag-shopping"></i><a
												href="product-list-left-sidebar.php">Product</a>
										</div>

										<div class="modal-line">
											<i class="fa-solid fa-circle-info"></i><a href="aboutUs.php">About Us</a>
										</div>

										<div class="modal-line">
											<i class="fa-solid fa-phone"></i><a href="contact.php">Contact Us</a>
										</div>
										<div class="modal-line">
											<i class="fa-solid fa-receipt"></i><a href="orders.php">Orders</a>
										</div>
										<div class="modal-line">
											<div class="item">
												<div class="dropdown account text-start">
													<div class="dropdown-toggle" data-bs-toggle="#dropdown">
														<i class="fa-solid fa-user"></i><a href="#">Profile</a>
													</div>
													<div class="dropdown-menu" id="dropdown">
														<?php

														if (isset($_SESSION['email'])) {
															// User is logged in, show logout button
															echo '
                                                        <div class="item">
                                                            <a href="account.php" title="Account">Account</a>
                                                        </div>
                                                        <div class="item">
                                                            <a href="logout.php" title="Logout">Logout</a>
                                                        </div>
                                                        ';
														} else {
															// User is not logged in, show login and register links
															echo '
                                                        <div class="item">
                                                            <a href="login.php" title="Login">Login</a>
                                                        </div>
                                                        <div class="item">
                                                            <a href="user-register.php" title="Register">Register</a>
                                                        </div>
                                                        ';
														}
														?>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>

					</div>
				</header>
			</div>
		</div>


		<!-- Main Content -->
		<div id="content" class="site-content">
			<!-- Breadcrumb -->
			<div id="breadcrumb">
				<div class="container">
					<div class="row d-flex align-items-center justify-content-center text-center mx-auto">
						<div class="col-5">
							<h2 class="title">About Us</h2>
							<h3><a href="homepage.html">Home</a></h3>
							<h3><a href="#">/About Us</a></h3>
						</div>
					</div>

				</div>
			</div>

			<div class="container">
				<div class="about-us intro">
					<div class="container m-5">
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="intro-header">
									<h3>Welcome To FreshMart</h3>
									<p>Discover a wide selection of fresh, organic, and locally sourced products at
										FreshMart, where quality meets convenience for your everyday shopping needs.</p>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="intro-right">
									<div class="intro-item">
										<p><img src="img/blog/wy.png" style="height:22rem;"></p>
										<h4>Name : Chong Wai Ying</h4>
									</div>


									<div class="col-12">
										<div class="row">
											<div class="col-2">
												<p><img src="img/blog/intro-icon.jpeg" alt="Intro Image"
														style="height:42px;"></p>
											</div>
											<div class="col-10">
												<p>
													Throughout the project system development process, my primary role
													is to
													handle reporting and
													backend database tasks as well. Overall, my active
													involvement in
													handling reporting and backend database tasks has provided me with
													valuable
													experience that
													will greatly benefit my future studies in the degree program.
												</p>
											</div>
										</div>

									</div>
									<div class="col-12">
										<div class="row">
											<div class="col-2">
												<p><img src="img/intro-icon-4.png" alt="Intro Image"></p>
											</div>
											<div class="col-10">
												<p>Specifically, I was responsible
													for developing
													functionalities related to user accounts, including login,
													registration,
													logout, search, checkout,
													and order management. For the admin site, I successfully developed
													the admin
													login, dashboard,
													order management,assign driver and contact management
													functionalities. For driver site, I have developed the functionalities of driver
													register, driver login and access the delivery details. Furthermore,
													I worked closely with Sooi Peng
													to handle various
													aspects of the system's backend database.
												</p>
											</div>
										</div>

									</div>

								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="intro-right">
									<div class="intro-item">
										<p><img src="img/blog/sp.png" style="height:22rem;"></p>
										<h4>Name : Kee Sooi Peng</h4>
									</div>


									<div class="col-12">
										<div class="row">
											<div class="col-2">
												<p><img src="img/blog/intro-icon.jpeg" alt="Intro Image"
														style="height:42px;"></p>
											</div>
											<div class="col-10">
												<p>
													Throughout the project, I
													have gained a comprehensive understanding of various aspects of
													computer science,
													including frontend development, database management, and backend
													systems.
												</p>
											</div>
										</div>

									</div>
									<div class="col-12">
										<div class="row">
											<div class="col-2">
												<p><img src="img/intro-icon-4.png" alt="Intro Image"></p>
											</div>
											<div class="col-10">
												<p>I played a role in advancing the front-end development for both the
													user and admin sites, contributing significantly to their overall
													design and functionality, and also reporting. Specifically, I took
													charge of developing
													key features for pages such as the homepage, about us, shopping
													cart, and the product page. Additionally, I worked on various
													shopping-related features, enriching the user experience across the
													platforms.

													Furthermore, I worked with Wai Ying together on
													specific functionalities and sections, ensuring a cohesive and
													well-integrated approach to development.
												</p>
											</div>
										</div>

									</div>

								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>


		<!-- Footer -->
		<footer id="footer">
			<div class="footer">
				<!-- Footer Top -->
				<div class="footer-top">
					<div class="container">
						<div class="row d-flex justify-content-center align-items-center">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="block text">
									<div class="block-content">
										<div class="contact">
											<h2 class="block-title">Contact Us</h2>

											<div class="item d-flex justify-content-center">
												<div class="item-left">
													<i class="zmdi zmdi-home"></i>
												</div>
												<div class="item-right">
													<span>348, Jln Tun Razak, Kampung Datuk Keramat, 50400 Kuala Lumpur,
														Wilayah Persekutuan Kuala Lumpur </span>
												</div>
											</div>
											<div class="item d-flex justify-content-center">
												<div class="item-left">
													<i class="zmdi zmdi-phone-in-talk"></i>
												</div>
												<div class="item-right">
													<span>012-3456789<br>03-45678990</span>
												</div>
											</div>
											<div class="item d-flex justify-content-center">
												<div class="item-left">
													<i class="zmdi zmdi-email"></i>
												</div>
												<div class="item-right">
													<span><a
															href="mailto:support@domain.com">support@domain.com</a><br><a
															href="mailto:contact@domain.com">contact@domain.com</a></span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="block text">
									<h2 class="block-title">
										<a href="index.html" class="logo-footer">
											<img src="img/logo-2.png" alt="Logo">
										</a>
									</h2>

									<div class="block-content">
										<p>Discover a wide selection of fresh, organic, and locally sourced products at
											FreshMart, where quality meets convenience for your everyday shopping needs.
										</p>
									</div>
								</div>
							</div>

							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="block text">
									<h2 class="block-title">Opening Hours</h2>

									<div class="block-content">
										<p>
											<strong>Monday To Friday</strong> : 8.00 AM - 8.00 PM<br>
											<strong>Satuday</strong> : 7.30 AM - 9.30 PM<br>
											<strong>Sunday</strong> : 7.00 AM - 10.00 PM
										</p>
									</div>
								</div>
							</div>


						</div>
					</div>
				</div>

				<!-- Footer Bottom -->
				<div class="footer-bottom">
					<div class="payment-intro">
						<div class="container">
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
									<div class="item d-flex">
										<div class="item-left">
											<img src="img/home1-payment-1.png" alt="Payment Intro">
										</div>
										<div class="item-right">
											<h3 class="title">Shipping Fees</h3>
											<div class="content">Shipping fees based on the total weight of items</div>
										</div>
									</div>
								</div>

								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
									<div class="item d-flex">
										<div class="item-left">
											<img src="img/home1-payment-2.png" alt="Payment Intro">
										</div>
										<div class="item-right">
											<h3 class="title">Secured Payment</h3>
											<div class="content">At FreshMart, we prioritize the security of your
												payments. We offer secure payment options to ensure that your personal
												and financial information is protected. You can shop with confidence
												knowing that your transactions at FreshMart are safe and secure.</div>
										</div>
									</div>
								</div>

								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
									<div class="item d-flex">
										<div class="item-left">
											<img src="img/home1-payment-3.png" alt="Payment Intro">
										</div>
										<div class="item-right">
											<h3 class="title">Money Back Guarantee</h3>
											<div class="content">If you are not completely satisfied with your purchase,
												simply contact our customer service within 3 days, and we will gladly
												refund your money.</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</footer>

		<!-- Go Up button -->
		<div class="go-up">
			<a href="#">
				<i class="fa fa-long-arrow-up"></i>
			</a>
		</div>

		<!-- Page Loader -->
		<div id="page-preloader">
			<div class="page-loading">
				<div class="dot"></div>
				<div class="dot"></div>
				<div class="dot"></div>
				<div class="dot"></div>
				<div class="dot"></div>
			</div>
		</div>
	</div>

	<!-- Vendor JS -->
	<script src="asset/jquery/jquery.js"></script>
	<script src="asset/bootstrap/js/bootstrap.js"></script>
	<script src="asset/jquery.countdown/jquery.countdown.js"></script>
	<script src="asset/nivo-slider/js/jquery.nivo.slider.js"></script>
	<script src="asset/owl.carousel/owl.carousel.min.js"></script>
	<script src="asset/slider-range/js/tmpl.js"></script>
	<script src="asset/slider-range/js/jquery.dependClass-0.1.js"></script>
	<script src="asset/slider-range/js/draggable-0.1.js"></script>
	<script src="asset/slider-range/js/jquery.slider.js"></script>
	<script src="asset/elevatezoom/jquery.elevatezoom.js"></script>
	<script src="asset/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
	<script src="asset/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
	<!-- Template CSS -->
	<script src="js/main.js"></script>
</body>

</html>