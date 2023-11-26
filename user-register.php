<?php
	include("config.php");
	$msg = "";

	if (isset($_POST['submit'])) {
		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$password = $_POST['password'];
		$cpassword = $_POST['cpassword'];
		$code = mysqli_real_escape_string($conn, md5(rand()));
		$user_type = "user"; // Set the default value of user_type as "user"

		if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM login WHERE email='{$email}'")) > 0) {
			$msg = "<div class='alert alert-danger'>{$email} - This email address has already been used.</div>";
		} else {
			if ($password === $cpassword) {
				$sql = "INSERT INTO login (username, email, password, code, user_type) VALUES ('{$name}', '{$email}', '{$password}', '{$code}', '{$user_type}')";
				$result = mysqli_query($conn, $sql);

				if ($result) {
					$message[] = 'registered successfully!';
					header("Location: login.php");
				} else {
					$msg = "<div class='alert alert-danger'>Something went wrong.</div>";
				}
			} else {
				$msg = "<div class='alert alert-danger'>Password and Confirm Password do not match</div>";
			}
		}
	}
?>



<!DOCTYPE html>
<html lang="zxx">
	

<head>
		<!-- Basic Page Needs -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>FreshMart - Organic, Fresh Food, Farm Store HTML Template</title>
		
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
		<link rel="stylesheet" href="asset/fontawesome-free-6.4.0-web/css/all.min.css">
        <link rel="stylesheet" href="asset/bootstrap-5.0.2-dist/css/bootstrap-grid.min.css">
        <link rel="stylesheet" href="asset/bootstrap-5.0.2-dist/css/bootstrap.min.css">
		
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
								<!-- Cart -->
									<div class="block-cart dropdown ms-auto">
										<div class="cart-title">
											<i class="fa fa-shopping-basket"></i>
											<span class="cart-count">2</span>
										</div>
		
										<div class="dropdown-content">
											<div class="cart-content">
												<table>
													<tbody>
														<tr>
															<td class="product-image">
																<a href="product-detail-left-sidebar.php">
																	<img src="img/product/7.jpg" alt="Product">
																</a>
															</td>
															<td>
																<div class="product-name">
																	<a href="product-detail-left-sidebar.php">Organic Strawberry Fruits</a>
																</div>
																<div>
																	2 x <span class="product-price">$28.98</span>
																</div>
															</td>
															<td class="action">
																<a class="remove" href="#">
																	<i class="fa fa-trash-o" aria-hidden="true"></i>
																</a>
															</td>
														</tr>
		
														<tr>
															<td class="product-image">
																<a href="product-detail-left-sidebar.php">
																	<img src="img/product/6.jpg" alt="Product">
																</a>
															</td>
															<td>
																<div class="product-name">
																	<a href="product-detail-left-sidebar.php">Organic Strawberry</a>
																</div>
																<div>
																	1 x <span class="product-price">$35.00</span>
																</div>
															</td>
															<td class="action">
																<a class="remove" href="#">
																	<i class="fa fa-trash-o" aria-hidden="true"></i>
																</a>
															</td>
														</tr>
		
														<tr class="total">
															<td>Total:</td>
															<td colspan="2">$92.96</td>
														</tr>
		
														<tr>
															<td colspan="3">
																<div class="cart-button">
																	<a class="btn btn-primary" href="product-cart.php" title="View Cart">View Cart</a>
																	<a class="btn btn-primary" href="product-checkout.php" title="Checkout">Checkout</a>
																</div>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
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
							<div class="modal" id="navModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<img src="img/logo.png" alt="">
										<button  class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
									</div>
	
									<div class="modal-body">
	
										<div class="modal-line">
										   <i class="fa-solid fa-house"></i><a href="homepage.php">Home</a>
										</div>
						 
										<div class="modal-line">
										  <i class="fa-solid fa-bag-shopping"></i><a href="product-list-left-sidebar.php">Product</a>
										</div>
						 
										<div class="modal-line">
										  <i class="fa-solid fa-circle-info"></i><a href="aboutUs.php">About Us</a>
										</div>
						 
										<div class="modal-line">
										   <i class="fa-solid fa-phone"></i><a href="contact.php">Contact Us</a>
										</div>
						 
										<div class="modal-line">
										  <div class="item">
											<div class="dropdown account text-start">
											  <div class="dropdown-toggle" data-toggle="dropdown">
												<i class="fa-solid fa-user"></i><a href="#">Profile</a>
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
								<h2 class="title">User Register</h2>
								<h3><a href="homepage.php">Home</a></h3>
								<h3><a href="#">/User register</a></h3>
							</div>
						</div>
	
					</div>
				</div>
			
			
				<div class="container">
					<div class="register-page">
						<div class="register-form form">
							<div class="block-title">
								<h2 class="title"><span>Create Account</span></h2>
							</div>
							
							<form action="" method="POST" enctype="multipart/form-data">
							
							<?php echo $msg; ?>

								<div class="form-group">
									<label>UserName</label>
									<input type="text" value="<?php if (isset($_POST['submit'])) { echo $name; }?>" name="name" required>
								</div>
								
								<div class="form-group">
									<label>Email</label>
									<input type="email" value="<?php if (isset($_POST['submit'])) { echo $email; }?>" name="email" required>
								</div>
								
								<div class="form-group">
									<label>Password</label>
									<input type="password" value="" name="password" required>
								</div>

								<div class="form-group">
									<label>Confirm your Password</label>
									<input type="password" value="" name="cpassword">
								</div>
								
								
								<div class="form-group text-center">
									<input type="submit" class="btn btn-primary" value="Register" name="submit"></br></br>
									<p>Already have an account? <a href="login.php" style="color: #3ba66b;">LOGIN NOW</a></p>

								</div>

							</form>
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
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="block text">
										<div class="block-content">
											<a href="index.php" class="logo-footer">
												<img src="img/logo-2.png" alt="Logo">
											</a>
									
											<div class="contact">
												<div class="item d-flex">
													<div class="item-left">
														<i class="zmdi zmdi-home"></i>
													</div>
													<div class="item-right">
														<span>123 Suspendis matti, VST District, NY Accums, North American</span>
													</div>
												</div>
												<div class="item d-flex">
													<div class="item-left">
														<i class="zmdi zmdi-phone-in-talk"></i>
													</div>
													<div class="item-right">
														<span>0123-456-78910<br>0987-654-32100</span>
													</div>
												</div>
												<div class="item d-flex">
													<div class="item-left">
														<i class="zmdi zmdi-email"></i>
													</div>
													<div class="item-right">
														<span><a href="mailto:support@domain.com">support@domain.com</a><br><a href="mailto:contact@domain.com">contact@domain.com</a></span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="block text">
										<h2 class="block-title"></h2>
										
										<div class="block-content">
											<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p>
											<p>
												<strong>Monday To Friday</strong> : 8.00 AM - 8.00 PM<br>
												<strong>Satuday</strong> : 7.30 AM - 9.30 PM<br>
												<strong>Sunday</strong> : 7.00 AM - 10.00 PM
											</p>
										</div>
									</div>
								</div>

								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="block text">
										<h2 class="block-title">Opening Hours</h2>
										
										<div class="block-content">
											<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p>
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
												<h3 class="title">Free Shipping item</h3>
												<div class="content">Proin gravida nibh vel velit auctor aliquet. Aenean lorem quis bibendum auctor</div>
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
												<div class="content">Proin gravida nibh vel velit auctor aliquet. Aenean lorem quis bibendum auctor</div>
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
												<div class="content">Proin gravida nibh vel velit auctor aliquet. Aenean lorem quis bibendum auctor</div>
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
		
		<!-- Template CSS -->
		<script src="js/main.js"></script>
	</body>

</html>