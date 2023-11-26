<?php
session_start();
include("config.php");
include "cart.php";
if (isset($_POST['save'])) {
	$name = isset($_SESSION['user_id']) ? $_SESSION['username'] : $_POST['name'];
	$email = isset($_SESSION['user_id']) ? $_SESSION['email'] : $_POST['email'];
	$subject = $_POST['subject'];
	$message = $_POST['content'];

	// Handle file upload
	$image_path = null;
	if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
		$image = $_FILES['image']['name'];
		$image_tmp_name = $_FILES['image']['tmp_name'];
		$image_folder = 'img/';

		// Generate a unique filename to avoid conflicts
		$image_filename = uniqid() . '_' . $image;

		// Move the uploaded image to the specified folder
		if (move_uploaded_file($image_tmp_name, $image_folder . $image_filename)) {
			$image_path = $image_folder . $image_filename;
		} else {
			// Handle file upload error
			echo "Failed to upload image.";
			exit;
		}
	}

	// Prepare the SQL query with parameterized statements to prevent SQL injection
	$sql_query = "INSERT INTO contact (name, email, subject, content, image)
		VALUES (?, ?, ?, ?, ?)";

	// Create a prepared statement
	$stmt = mysqli_prepare($conn, $sql_query);

	if ($stmt) {
		// Bind the parameters to the statement
		mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $subject, $message, $image_path);

		// Execute the statement
		if (mysqli_stmt_execute($stmt)) {
			echo "New details entry inserted successfully!";
		} else {
			echo "Error: " . mysqli_stmt_error($stmt);
		}

		// Close the statement
		mysqli_stmt_close($stmt);
	} else {
		$message[]= "Error: " . mysqli_error($conn);
	}

	mysqli_close($conn);
} else {
	$name = isset($_SESSION['username']) ? $_SESSION['username'] : '';
	$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
}

if (isset($_GET['remove'])) {
    // Check if the user is logged in
    if ($email !== "") {
        $remove_id = $_GET['remove'];
        mysqli_query($conn, "DELETE FROM shopping_cart WHERE id='$remove_id'");
        $message[] = 'Product removed from cart successfully';
        
        // Redirect back to the shopping cart page
        header('Location: product-list-left-sidebar.php');
        exit();
    } else {
        // User is not logged in, redirect to the login page
        header('Location: login.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="zxx">


<head>
	<!-- Basic Page Needs -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Contact</title>

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
							<a href="homepage.php">
								<img class="img-responsive" src="img/logo.png" alt="Logo">
							</a>
						</div>

					</div>
				</div>
				<div class="row d-flex align-items-center mt-0 mb-3">
					<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 mx-auto">
						<!-- Search -->
						<div class="form-search">
                                <form action="product-list-left-sidebar.php?search" method="GET"
                                    style="margin-left:8px;">
                                    <input type="text" class="form-input" name="search" placeholder="Search">
                                    <button type="submit" class="fa fa-search"></button>
                                </form>
                            </div>
						<!-- Cart -->
						<!-- Cart -->
						<div class="block-cart dropdown ms-auto">
							<div class="cart-title">
								<?php
								$select_rows = mysqli_query($conn, "SELECT * FROM `shopping_cart`  WHERE email='$email'") or die('query failed');
								$row_count = mysqli_num_rows($select_rows);
								?>

								<i class="fa fa-shopping-basket"></i>
								<span class="cart-count">
									<?php echo $row_count; ?>
								</span>
							</div>
							<div class="dropdown-content">
								<div class="cart-content">
									<table>
										<tbody>
											<?php
											$select_cart = mysqli_query($conn, "SELECT * FROM `shopping_cart` WHERE email='$email'") or die('query failed');
											$grand_total = 0;
											if (isset($_POST['update_update_btn'])) {
												// Check if the user is logged in
												if ($email !== "") {
													$update_value = $_POST['update_quantity'];
													$update_id = $_POST['update_quantity_id'];
													updateProductQuantity($conn, $update_value, $update_id, $message);
												} else {
													// User is not logged in, redirect to the login page
													header('Location: login.php');
													exit();
												}
											}
											if (mysqli_num_rows($select_cart) > 0) {
												while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
													?>
<tr>
    <td class="product-image p-2">
        <a href="product-detail-left-sidebar.html">
            <img src="img/product/<?php echo $fetch_cart['image']; ?>" alt="Product" style="width: 120px;">
        </a>
    </td>
    <td>
        <div class="col-md-6 d-flex flex-column">
            <div class="product-name" style="margin-left: 5px; text-transform: capitalize;">
                <a href="product-detail-left-sidebar.html">
                    <?php echo $fetch_cart['name']; ?>
                </a>
            </div>
            <div>
                <form action="" method="POST">
                    <input type="hidden" name="update_quantity_id" value="<?php echo $fetch_cart['id']; ?>">
                    <input id="form1" min="1" name="update_quantity" value="<?php echo $fetch_cart['quantity']; ?>"
                        type="number" class="form-control form-control-lg text-center mx-2" style="width: 60px;" />
                    <input type="submit" value="Update" class="btn mt-2" name="update_update_btn">
                </form>
            </div>
        </div>
		<div class="col-md-6 d-flex flex-column align-items-end justify-content-end">
		<a class="remove mb-5" href="product-list-left-sidebar.php?remove=<?php echo $fetch_cart['id']; ?>"
                    onclick="return confirm('Remove item from cart?')">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a>
			<span class="mt-5">
                    <?php echo $fetch_cart['quantity']; ?> x <span
                        class="product-price">RM <?php echo number_format($fetch_cart['price']); ?></span>
                </span>
            </div>
    </td>
</tr>


													<?php
													$subtotal = $fetch_cart['price'] * $fetch_cart['quantity'];
													$grand_total += $subtotal;
												}
											} else {
												echo "<tr><td colspan='3'><h2>Cart is empty</h2></td></tr>";
											}
											?>

											<tr class="total">
												<td>Total:</td>
												<td colspan="2">$
													<?php echo number_format($grand_total); ?>
												</td>
											</tr>

											<tr>
												<td colspan="3">
													<div class="cart-button">
														<a class="btn btn-primary" href="product-cart.php"
															title="View Cart">View Cart</a>
														<a class="btn btn-primary" href="product-checkout.php"
															title="Checkout">Checkout</a>
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




<style>
    .flash_message {
		color: #fff;
        position: fixed;
        left: 0;
        right: 0;
        top: 20%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        animation: slide-up 3s forwards;
        animation-delay: 2.55s;
		width: 40%; /* You can adjust this width as needed */
        padding: 2rem 2rem;
    }

    .flash_message h2 {
		padding: 1rem 1rem;
		background-color: #696969;
    }

    @keyframes slide-up {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(-150px);
            opacity: 0;
        }
    }
</style>		<!-- Main Menu -->
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
							<h2 class="title">Contact Us</h2>
							<h3><a href="homepage.php">Home</a></h4>
								<h3><a href="#">/Contact Us</a></h4>
						</div>
					</div>

				</div>
			</div>

			<div class="container">
				<div class="contact-page">
					<div class="contact-info">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="item d-flex">
									<div class="item-left">
										<div class="icon"><i class="zmdi zmdi-email"></i></div>
									</div>
									<div class="item-right d-flex">
										<div class="title">Email:</div>
										<div class="content">
											<a href="mailto:support@domain.com">support@domain.com</a><br>
											<a href="mailto:contact@domain.com">contact@domain.com</a>
										</div>
									</div>
								</div>
							</div>

							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="item d-flex justify-content-center">
									<div class="item-left">
										<div class="icon"><i class="zmdi zmdi-home"></i></div>
									</div>
									<div class="item-right d-flex">
										<div class="title">Address:</div>
										<div class="content">
											23 Jalan SS15, Visaosang Building VST<br> District, NY Accums, North
											American
										</div>
									</div>
								</div>
							</div>

							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="item d-flex justify-content-end">
									<div class="item-left">
										<div class="icon"><i class="zmdi zmdi-phone"></i></div>
									</div>
									<div class="item-right d-flex">
										<div class="title">Holine:</div>
										<div class="content">
											0123-456-78910<br>
											0987-654-32100
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="contact-map" style="display:flex;justify-content:center;align-items:center;">
						<div id="map">
							<div class="mapouter">
								<div class="gmap_canvas">
									<iframe width="1000" height="400" id="gmap_canvas"
										src="https://maps.google.com/maps?q=malaysia,subang jaya ss15, inti&t=&z=10&ie=UTF8&iwloc=&output=embed"
										frameborder="0" scrolling="no"></iframe><a href="https://2yu.co"></a><br>
								</div>
							</div>
						</div>
					</div>




					<div class="contact-intro">
						<p>“Proin gravida nibh vel velit auctor aliquet. Aenean sollicudin, lorem quis bibendum auctor,
							nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh
							vultate cursus a sit amet mauris. Proin gravida nibh vel velit auctor”</p>
						<img src="img/contact-icon.png" alt="Contact Comment">
					</div>

					<div class="contact-form form" style="font-size: 16px;">
						<form action="" method="post" enctype="multipart/form-data">
							<div class="form-group row">
								<div class="inputBox">
									<input type="text" name="name" value="<?= htmlspecialchars($name) ?>"
										placeholder="Username" required class="box">
									<input type="email" name="email" value="<?= htmlspecialchars($email) ?>"
										placeholder="Email" required class="box">
								</div>
							</div>

							<div class="form-group">
    <select name="subject" required style="display: block; width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ccc;">
        <option value="" disabled selected>Select a subject</option>
        <option value="Product Quality">Complaint: Product Quality</option>
        <option value="Product Availability">Complaint: Product Availability</option>
        <option value="Shipping Delay">Complaint: Shipping Delay</option>
        <option value="Wrong Item">Complaint: Wrong Item Received</option>
        <option value="Damaged Item">Complaint: Damaged Item Received</option>
        <option value="Payment Issues">Complaint: Payment Issues</option>
        <option value="Order Tracking">Feedback: Order Tracking</option>
        <option value="Return/Refund Process">Feedback: Return/Refund Process</option>
        <!-- Add more options as needed -->
    </select>
</div>



							<div class="form-group">
								<textarea rows="10" name="content" placeholder="MESSAGE"></textarea>
							</div>
							<div class="form-group">
								<label for="file-upload" style="display: inline-block; padding: 10px 20px; background-color: #f8f9fa; border: 1px solid #ccc; cursor: pointer;color:#222;" class="btn btn-primary">
									Upload Image
								</label>
								<input type="file" name="image" id="file-upload" class="btn" style="display: none;">
							</div>


							<div class="form-group text-center">
								<input type="submit" name="save" class="btn btn-primary" value="Send Message">
							</div>
						</form>


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
	<!-- Map google -->
	<script src="http://maps.google.com/maps/api/js?key=AIzaSyCHw5d0S8zNjrr5Eo4Rg0j2pE8JK9UDZWY"></script>

	<!-- Template JS -->
	<script src="js/main.js"></script>

</body>


</html>