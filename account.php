<?php
include("config.php");

session_start();

$email = '';
$password = '';
$message = array();

if (isset($_SESSION['email'])) {
   $email = $_SESSION['email'];

   // Retrieve the user's profile data
   $fetch_profile_query = $conn->prepare("SELECT * FROM `login` WHERE email = ?");
   $fetch_profile_query->bind_param("s", $email);
   $fetch_profile_query->execute();
   $fetch_profile_result = $fetch_profile_query->get_result();
   $fetch_profile = $fetch_profile_result->fetch_assoc();

   if ($fetch_profile) {
      $password = $fetch_profile['password'];
   }
}

if (isset($_POST['update_profile'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $newEmail = mysqli_real_escape_string($conn, $_POST['email']);

   if ($email !== $newEmail) {
      $message[] = 'Cannot change email address!';
   } else {
      $update_profile = $conn->prepare("UPDATE `login` SET username = ? WHERE email = ?");
      $update_profile->bind_param("ss", $name, $email);
      $update_profile->execute();

      // Retrieve the updated user data
      $fetch_updated_profile_query = $conn->prepare("SELECT * FROM `login` WHERE email = ?");
      $fetch_updated_profile_query->bind_param("s", $email);
      $fetch_updated_profile_query->execute();
      $fetch_updated_profile_result = $fetch_updated_profile_query->get_result();
      $fetch_profile = $fetch_updated_profile_result->fetch_assoc();

      if ($fetch_profile) {
         $name = $fetch_profile['username'];
      }

      $old_pass = $_POST['old_pass'];
      $update_pass = $_POST['update_pass'];
      $new_pass = $_POST['new_pass'];
      $confirm_pass = $_POST['confirm_pass'];

      if (!empty($update_pass) && !empty($new_pass) && !empty($confirm_pass)) {
         if ($old_pass !== $password) {
            $message[] = 'Old password does not match!';
         } elseif ($new_pass !== $confirm_pass) {
            $message[] = 'Confirm password does not match!';
         } else {
            $update_pass_query = $conn->prepare("UPDATE `login` SET password = ? WHERE email = ?");
            $update_pass_query->bind_param("ss", $new_pass, $email);
            $update_pass_query->execute();
            $message[] = 'Password updated successfully!';
         }
      }
   }
}

$name = isset($fetch_profile['username']) ? $fetch_profile['username'] : '';
$email = isset($fetch_profile['email']) ? $fetch_profile['email'] : '';
// Add this code after retrieving the user's profile data
$fetch_shipping_query = $conn->prepare("SELECT * FROM shipping_address WHERE email = ?");
$fetch_shipping_query->bind_param("s", $email);
$fetch_shipping_query->execute();
$fetch_shipping_result = $fetch_shipping_query->get_result();
$fetch_shipping = $fetch_shipping_result->fetch_assoc();

$firstName = isset($fetch_shipping['first_name']) ? $fetch_shipping['first_name'] : '';
$lastName = isset($fetch_shipping['last_name']) ? $fetch_shipping['last_name'] : '';
$contactNumber = isset($fetch_shipping['contact_number']) ? $fetch_shipping['contact_number'] : '';
$address = isset($fetch_shipping['address']) ? $fetch_shipping['address'] : '';
$state = isset($fetch_shipping['state']) ? $fetch_shipping['state'] : '';
$city = isset($fetch_shipping['city']) ? $fetch_shipping['city'] : '';
$postcode = isset($fetch_shipping['postcode']) ? $fetch_shipping['postcode'] : '';

// Close the fetch shipping query statement
$fetch_shipping_query->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_shippingaddress'])) {
    $firstName = $_POST["firstName"] ?? "";
    $lastName = $_POST["lastName"] ?? "";
    $contactNumber = $_POST["contact_number"] ?? "";
    $address = $_POST["address"] ?? "";
    $state = $_POST["state"] ?? "";
    $city = $_POST["city"] ?? "";
    $postcode = $_POST["postcode"] ?? "";

    // Validate and sanitize user input
    $firstName = mysqli_real_escape_string($conn, $firstName);
    $lastName = mysqli_real_escape_string($conn, $lastName);
    $contactNumber = mysqli_real_escape_string($conn, $contactNumber);
    $address = mysqli_real_escape_string($conn, $address);
    $state = mysqli_real_escape_string($conn, $state);
    $city = mysqli_real_escape_string($conn, $city);
    $postcode = mysqli_real_escape_string($conn, $postcode);

    // Check if the user already has a shipping address in the database
    $check_query = $conn->prepare("SELECT * FROM shipping_address WHERE email = ?");
    $check_query->bind_param("s", $email);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows > 0) {
        // User already has a shipping address, update it
        $update_query = $conn->prepare("UPDATE shipping_address SET first_name = ?, last_name = ?, contact_number = ?, address = ?, state = ?, city = ?, postcode = ? WHERE email = ?");
        $update_query->bind_param("ssssssss", $firstName, $lastName, $contactNumber, $address, $state, $city, $postcode, $email);
        $update_query->execute();

        if ($update_query->affected_rows > 0) {
            $message[] = "Shipping address updated successfully.";
        } else {
            $message[] = "Failed to update the shipping address.";
        }

        // Close the update query statement
        $update_query->close();
    } else {
        // User does not have a shipping address, insert a new record
        $insert_query = $conn->prepare("INSERT INTO shipping_address (email, first_name, last_name, contact_number, address, state, city, postcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_query->bind_param("ssssssss", $email, $firstName, $lastName, $contactNumber, $address, $state, $city, $postcode);
        $insert_query->execute();

        if ($insert_query->affected_rows > 0) {
            $message[] = "Shipping address saved successfully.";
        } else {
            $message[] = "Failed to save the shipping address.";
        }

        // Close the insert query statement
        $insert_query->close();
    }

    // Close the check query statement
    $check_query->close();
}

// Close the database connection
$conn->close();
?>

<!-- Display messages -->
<?php if (!empty($message)): ?>
   <ul>
      <?php foreach ($message as $msg): ?>
         <li><?php echo $msg; ?></li>
      <?php endforeach; ?>
   </ul>
<?php endif; ?>



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
			
								</div>
							</div>
					</div>
				</div>
				<!-- Main Menu -->
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
								<h2 class="title">Account</h2>
								<h3><a href="homepage.php">Home</a></h3>
								<h3><a href="user-register.php">/Account</a></h3>
							</div>
						</div>
					</div>
				</div>
			
				<div class="container">
					<div class="row d-flex align-items-center justify-content-center">
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
							<div class="register-page">
								<div class="register-form form">
								<div class="block-title">
								<h2 class="title"><span>Update Profile</span></h2>
							</div>

							<form action="" method="POST" enctype="multipart/form-data">
							<?php if (!empty($message)): ?>
								<div class="error-message" style="background-color: #ffcccc; color: #ff0000; padding: 10px;">
									<?php foreach ($message as $msg): ?>
										<p><?php echo $msg; ?></p>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
								<div class="flex">
									<div class="form-group">
									<div class="inputBox">
										<span>Username:</span>
										<input type="text" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Username" required class="form-control">
										<span>Email:</span>
										<input type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Email" required class="box">
									</div>
									</div>
									<div class="form-group">
									<div class="inputBox">
										<input type="hidden" name="old_pass" value="<?= $password ?>">
										<span>Old Password:</span>
										<input type="password" name="update_pass" placeholder="Enter previous password" class="box">
										<span>New Password:</span>
										<input type="password" name="new_pass" placeholder="Enter new password" class="box">
										<span>Confirm Password:</span>
										<input type="password" name="confirm_pass" placeholder="Confirm new password" class="box">
									</div>
									</div>

								</div>
								<div class="row d-flex align-items-center justify-content-center">
									<input type="submit" class="btn" value="Update Profile" name="update_profile">
								</div>

								
								</form>
							<br/><br/>


								</div>
							</div>
						</div>


					</div>

				</div>
				<div class="container">
					<div class="row d-flex align-items-center justify-content-center">
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
							<div class="register-page">
								<div class="register-form form">
								<div class="block-title">
								<h2 class="title"><span>Update Shipping Address</span></h2>
							</div>

							<form action="" method="POST" enctype="multipart/form-data">
							<?php if (!empty($message)): ?>
								<div class="error-message" style="background-color: #ffcccc; color: #ff0000; padding: 10px;">
									<?php foreach ($message as $msg): ?>
										<p><?php echo $msg; ?></p>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<div class="flex">
							<div class="inputBox">
								<span>First Name</span>
								<input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" placeholder="Firstname" required class="box">
								<span>Last Name</span>
								<input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" placeholder="Lastname" required class="box">
							</div>
							<div class="inputBox">
								<span>Contact Number</span>
								<input type="text" name="contact_number" id="contact_number" value="<?php echo htmlspecialchars($contactNumber); ?>" placeholder="Enter contact number" class="box">
								<span>Address</span>
								<input type="text" name="address" id="address" value="<?php echo htmlspecialchars($address); ?>" placeholder="Enter address" class="box">
							</div>
							<div class="col-md-12 mb-3">
								<label for="state">State</label>
								<select class="form-control" id="state" name="state" style="font-size:12px;">
								<option value="Johor" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Johor' ? 'selected' : ''; ?>>Johor</option>
								<option value="Kedah" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Kedah' ? 'selected' : ''; ?>>Kedah</option>
								<option value="Kelantan" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Kelantan' ? 'selected' : ''; ?>>Kelantan</option>
								<option value="Kuala Lumpur" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Kuala Lumpur' ? 'selected' : ''; ?>>Kuala Lumpur</option>
								<option value="Selangor" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Selangor' ? 'selected' : ''; ?>>Selangor</option>
								<option value="Pahang" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Pahang' ? 'selected' : ''; ?>>Pahang</option>
								<option value="Pulau Pinang" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Pulau Pinang' ? 'selected' : ''; ?>>Pulau Pinang</option>
								<option value="Negeri Sembilan" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Negeri Sembilan' ? 'selected' : ''; ?>>Negeri Sembilan</option>
								<option value="Terengganu" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Terengganu' ? 'selected' : ''; ?>>Terengganu</option>
								<option value="Sabah" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Sabah' ? 'selected' : ''; ?>>Sabah</option>
								<option value="Sarawak" <?php echo isset($fetch_profile['state']) && $fetch_profile['state'] == 'Sarawak' ? 'selected' : ''; ?>>Sarawak</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="city">City</label>
								<input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" class="form-control">
							</div>
							<div class="col-md-6">
								<label for="postcode">Postcode</label>
								<input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($postcode); ?>" class="form-control">
							</div>
						</div>
						<div class="row d-flex align-items-center justify-content-center">
							<input type="submit" class="btn" value="Update Shipping Address" name="update_shippingaddress">
						</div>
					</form>

							<br/><br/>


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
		
		<!-- Template CSS -->
		<script src="js/main.js"></script>
	</body>

</html>