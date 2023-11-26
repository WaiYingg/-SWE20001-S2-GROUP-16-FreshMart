<?php
session_start();
include "config.php";
include "cart.php";
$email = $_SESSION['email'] ?? '';
function getProductWeight($conn, $product_id)
{
	$query = "SELECT product_weight FROM product_db WHERE id = ?";
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, "i", $product_id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $product_weight);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $product_weight;
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

		<style>
			body{
				font-family: "Playfair Display";
				font-size: 14px;
			}
		</style>
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
                        class="product-price"><?php echo number_format($fetch_cart['price']); ?></span>
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
										<div class="modal-line">
										<i class="fa-solid fa-receipt"></i><a href="orders.php">Orders</a>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</header>
			</div>
		</div>
				
		<div class="site-content" id="content">
		<div id="breadcrumb">
                    <div class="container">
                        <div class="row d-flex align-items-center justify-content-center text-center mx-auto">
                            <div class="col-5">
                                <h2 class="title">Order</h2>
                                <h3><a href="homepage.php">Home</a></h4>
                                <h3><a href="#">/Order</a></h4>
                            </div>
                        </div>
    
                    </div>
                </div>
    <div class="order-buttons" style="text-align: center;">
        <form method="POST">
            <input type="submit" value="Pending Order" name="pending-order" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 50px;background-color:grey;">
            <input type="submit" value="Completed Order" name="completed-order" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 50px;">
        </form>
    </div>
    <div class="row g-5 p-5">
        <?php
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];

            // Check if the pending order button is pressed
            if (isset($_POST['pending-order'])) {
                $select_orders = mysqli_query($conn, "SELECT DISTINCT order_id FROM `orders1` WHERE email='$email' AND order_status='pending' ORDER BY order_id") or die('query failed');
            } elseif (isset($_POST['completed-order'])) { // Check if the completed order button is pressed
                $select_orders = mysqli_query($conn, "SELECT DISTINCT order_id FROM `orders1` WHERE email='$email' AND order_status='completed' ORDER BY order_id") or die('query failed');
            } else {
                // By default, display pending orders
                $select_orders = mysqli_query($conn, "SELECT DISTINCT order_id FROM `orders1` WHERE email='$email' AND order_status='pending' ORDER BY order_id") or die('query failed');
            }

            $order_number = 1; // Initialize the order number

            while ($fetch_order = mysqli_fetch_assoc($select_orders)) {
                $order_id = $fetch_order['order_id'];

                // Fetch and display the shipping address for the current order
                $select_order_items = mysqli_query($conn, "SELECT * FROM `orders1` WHERE email='$email' AND order_id='$order_id'") or die('query failed');
                $fetch_order_item = mysqli_fetch_assoc($select_order_items);

                // Display shipping address only once per order

				    // Insert the following code to retrieve the generated order_id
					$inserted_order_id = mysqli_insert_id($conn);

					// Use $inserted_order_id for display purposes or further processing
					//echo "Inserted Order ID: " . $inserted_order_id;
                ?>
                <div class="col-md-4">
                    <div class="card p-3">
                        <div class="card-body">
						<div class="order-id text-center"><h1>Order <?php echo $order_number; ?><h1></div>
                            <div class="shipping-address">
                                Shipping Address: <br>
                                <?php echo $fetch_order_item['last_name'] . ' ' . $fetch_order_item['first_name']; ?><br>
                                (+60) <?php echo $fetch_order_item['contact_number']; ?><br>
                                <?php echo $fetch_order_item['address'] . ', ' . $fetch_order_item['city'] . ', ' . $fetch_order_item['state'] . ', ' . $fetch_order_item['postcode']; ?>
                            </div>
							<hr>
                            <?php
                            // Reset the query pointer to the first row
                            mysqli_data_seek($select_order_items, 0);

                            $current_order_total = 0;
                            $shipping_fee = 0;
                            $total_weight = 0;

                            while ($fetch_order_item = mysqli_fetch_assoc($select_order_items)) {
                                $product_id = $fetch_order_item['product_id'];
                                $product_weight = floatval(getProductWeight($conn, $product_id)); // Convert to float
                                $total_weight += $product_weight * $fetch_order_item['quantity'];

                                // Display order details
                                ?>
                                <div class="order-details">
                                    <div class="product-image d-flex align-items-center justify-content-between">
                                        <span><img width="80" alt="Product Image" class="img-responsive" src="img/product/<?php echo $fetch_order_item['image']; ?>"></span>
										<span><?php echo $fetch_order_item['name']; ?></span>
									</div>
									<hr>

                                    <div class="product-info">
                                        
                                        <div class="product-title d-flex align-items-center justify-content-between">
											<span>Price: RM</span>
											<span>RM <?php echo number_format($fetch_order_item['price'], 2); ?></span>
										</div>
                                        <div class="product-title  d-flex align-items-center justify-content-between">
											<span>Quantity:</span>
											<span><?php echo $fetch_order_item['quantity']; ?></span>
										</div>
                                        <div class="product-title  d-flex align-items-center justify-content-between">
											<span>Total: </span>
											<span>RM <?php echo number_format($fetch_order_item['price'] * $fetch_order_item['quantity'], 2); ?></span>
											
										</div>
                                    </div>
                                </div>
                                <hr>
                                <?php
                                $current_order_total += $fetch_order_item['price'] * $fetch_order_item['quantity'];
                            }

                            // Calculate shipping fee based on total weight
							if ($total_weight <= 500) {
								$shipping_fee = 4;
							} elseif ($total_weight > 500 && $total_weight <= 1000) {
								$shipping_fee = 7;
							} else {
								$shipping_fee = 12;
							}
                            $order_total = $current_order_total + $shipping_fee;
                            ?>
                            <div class="order-total">
                                <div class="subtotal d-flex align-items-center justify-content-between">
									<span>Subtotal: </span>
									<span>RM <?php echo number_format($current_order_total, 2); ?></span>
								</div>
                                <div class="shipping d-flex align-items-center justify-content-between">
									<span>Shipping: </span>
									<span>RM <?php echo number_format($shipping_fee, 2); ?></span>
								</div>
                                <div class="total d-flex align-items-center justify-content-between">
									<span>Total: RM</span>
									<span>RM <?php echo number_format($order_total, 2); ?></span>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $order_number++;
            }
        } else {
            // Redirect the user or display an error message
            echo '<h1 class="text-center">User not logged in.</h1>';
        }
        ?>
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
			<!-- Go Up button -->
			<div class="go-up">
				<a href="#">
					<i class="fa fa-long-arrow-up"></i>
				</a>
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