<?php
	session_start();
	include("config.php")
?>
<!DOCTYPE html>
<html lang="zxx">


<head>
    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Log In Page</title>

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
    <link rel="stylesheet" href="asset/slider-range/css/jslider.css">		<!--extra asset-->
    <link rel="stylesheet" href="asset/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="asset/fontawesome-free-6.4.0-web/css/all.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reponsive.css">

	<style>
		    .password-toggle {
        position: relative;
    }

    .password-toggle .toggle-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    }

    .password-toggle .toggle-icon i {
        margin-bottom: 18px;
    }
	</style>
</head>

<body class="home home-1">
    <div id="all">
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
									<a href="homepage.php">
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
								</div>
							</div>
					</div>
				</div>
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
                            <h2 class="title">Login</h2>
                            <h3><a href="homepage.php">Home</a></h3>
                            <h3><a href="#">/Login</a></h3>
                        </div>
                    </div>

                </div>
            </div>


            <div class="container">
			<div class="login-page">
    <div class="login-form form">
        <div class="block-title">
            <h2 class="title"><span>Login</span></h2>
        </div>

        <form action="logindata.php" onsubmit="return isValid()" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="" name="email" id="email">
            </div>

			<div class="form-group">
    <label>Password</label>
    <div class="password-toggle">
        <div class="col d-flex justify-content-between">
            <input type="password" value="" name="pass" id="pass">
            <label for="pass" class="toggle-icon" onclick="togglePasswordVisibility()">
                <i class="fa fa-eye"></i>
            </label>
        </div>
    </div>
</div>

            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="LOG IN" name="submit">
            </div>
			<div class="form-group text-center">
                <div class="link">
                    <p>Don't have an account? <a href="user-register.php"style="color: #3ba66b;">REGISTER NOW</a></p>
                    
                </div>
            </div>
        </form>

        <script>
            function isValid() {
                var email = document.getElementById('email').value;
                var pass = document.getElementById('pass').value;

                if (email.length === 0 && pass.length === 0) {
                    alert("Email and password field is empty!!!");
                    return false;
                } else {
                    if (email.length === 0) {
                        alert("User Name is empty!!!");
                        return false;
                    }
                    if (pass.length === 0) {
                        alert("Password is empty!!!");
                        return false;
                    }
                }

                return true;
            }
			function togglePasswordVisibility() {
				var passwordInput = document.getElementById('pass');
				var toggleIcon = document.querySelector('.toggle-icon i');

				if (passwordInput.type === 'password') {
				passwordInput.type = 'text';
				toggleIcon.classList.remove('fa-eye');
				toggleIcon.classList.add('fa-eye-slash');
				} else {
				passwordInput.type = 'password';
				toggleIcon.classList.remove('fa-eye-slash');
				toggleIcon.classList.add('fa-eye');
				}
			}
        </script>
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
                                                        <div class="content">At FreshMart, we prioritize the security of your payments. We offer secure payment options to ensure that your personal and financial information is protected. You can shop with confidence knowing that your transactions at FreshMart are safe and secure.</div>
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
                                                        <div class="content">If you are not completely satisfied with your purchase, simply contact our customer service within 3 days, and we will gladly refund your money.</div>
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