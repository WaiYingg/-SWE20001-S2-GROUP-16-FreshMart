<?php
session_start();
include "config.php";

$email = $_SESSION['email'] ?? '';
$message = []; // Initialize an empty array to store error messages

// Function to update product quantity
function updateProductQuantity($conn, $new_quantity, $cart_id, $message)
{
    $update_query = mysqli_query($conn, "UPDATE `shopping_cart` SET quantity = '$new_quantity' WHERE id = '$cart_id'");
    if ($update_query) {
        $message[] = 'Product quantity updated successfully';
    } else {
        $message[] = 'Failed to update product quantity';
    }
}

// Check if the necessary form data is set
if (isset($_POST['product_name']) && isset($_POST['product_price']) && isset($_POST['product_image']) && isset($_POST['product_quantity'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = intval($_POST['product_quantity']);

    if (isset($_POST['add_to_cart'])) {
        // Check if the user is logged in
        if ($email !== "") {
            // User is logged in
            // Fetch the product ID and stock quantity from the product_db table
            $select_product = mysqli_query($conn, "SELECT id, stock FROM product_db WHERE name='$product_name'");
            $fetch_product = mysqli_fetch_assoc($select_product);
            $product_id = $fetch_product['id'];
            $product_stock = $fetch_product['stock'];

            // Check if the requested quantity exceeds the available stock
            if ($product_quantity <= 0) {
                $message[] = "Invalid quantity. Quantity must be greater than 0.";
            } else if ($product_quantity > $product_stock) {
                $message[] = "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}. Our admin will restock as soon as possible.";
            } else {
                // Check if the product is already in the cart
                $select_cart = mysqli_query($conn, "SELECT * FROM shopping_cart WHERE name='$product_name' AND email='$email'");
				if (mysqli_num_rows($select_cart_result) > 0) {
					// Product already exists in the cart, so update the quantity
					$fetch_cart = mysqli_fetch_assoc($select_cart_result);
					$new_quantity = $fetch_cart['quantity'] + $product_quantity;
				
					
					if ($product_quantity + $cart_product_quantity > $product_stock) {
						$message[] = "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}. Our admin will restock as soon as possible.";
					} else {
						$update_quantity_query = mysqli_prepare($conn, "UPDATE shopping_cart SET quantity=? WHERE id=?");
						mysqli_stmt_bind_param($update_quantity_query, "ii", $new_quantity, $fetch_cart['id']);
						mysqli_stmt_execute($update_quantity_query);
				
						if ($update_quantity_query) {
							$message[] = 'Product quantity updated successfully';
						} else {
							$message[] = 'Failed to update product quantity';
						}
					}
				} else {
                    // Product is not in the cart, so add it
                    $insert_product = mysqli_query($conn, "INSERT INTO shopping_cart (name, price, image, quantity, email, product_id) VALUES ('$product_name', '$product_price', '$product_image', '$product_quantity', '$email', '$product_id')");
                    if ($insert_product) {
                        $message[] = 'Product added to cart successfully';
						echo "<script>alert('Product added to cart successfully');</script>";

                    } else {
                        $message[] = 'Failed to add product to cart';
                    }
                }
            }
        } else {
            // User is not logged in, redirect to the login page
            header('Location: login.php');
            exit();
        }
    }
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
}

// ...
if (isset($_POST['update_quantity']) && isset($_POST['update_quantity_id'])) {
    // Check if the user is logged in
    if ($email !== "") {
        $update_value = intval($_POST['update_quantity']);
        $update_id = $_POST['update_quantity_id'];

        // Fetch the product ID and stock quantity from the product_db table
        $select_cart_item = mysqli_query($conn, "SELECT name, product_id FROM shopping_cart WHERE id='$update_id' AND email='$email'");
        $fetch_cart_item = mysqli_fetch_assoc($select_cart_item);
        $product_name = $fetch_cart_item['name'];
        $product_id = $fetch_cart_item['product_id'];

        $select_product = mysqli_query($conn, "SELECT stock FROM product_db WHERE id='$product_id'");
        $fetch_product = mysqli_fetch_assoc($select_product);
        $product_stock = $fetch_product['stock'];

        // Check if the requested quantity exceeds the available stock
        if ($update_value <= 0) {
            $message[] = "Invalid quantity. Quantity must be greater than 0.";
        } else if ($update_value > $product_stock) {
            $message[] = "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}, Requested quantity: {$update_value}";
        } else {
            updateProductQuantity($conn, $update_value, $update_id, $message);
        }
    } else {
        // User is not logged in, redirect to the login page
        header('Location: login.php');
        exit();
    }
}

// new section for fetching product_weight//
$stmt = mysqli_prepare($conn, "SELECT sc.*, pd.product_weight
    FROM shopping_cart sc
    JOIN product_db pd ON sc.product_id = pd.id
    WHERE sc.id = ? AND pd.id = ?");

if ($stmt) {
	// Step 2: Bind the user ID and product ID parameters to the prepared statement
	mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id); // Assuming user_id and product_id are integers, use "i" for integer

	// Step 3: Execute the prepared statement
	mysqli_stmt_execute($stmt);

	// Get the result set
	$select_data = mysqli_stmt_get_result($stmt);

	// Now you can fetch the data from the result set
	while ($row = mysqli_fetch_assoc($select_data)) {
		// Process the row data here
		$productWeight = $row['product_weight'];
		// ...
	}

	// Don't forget to close the statement when you are done with it
	mysqli_stmt_close($stmt);
} else {
	// Error handling if the statement preparation fails
	die('Prepared statement error: ' . mysqli_error($conn));
}

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
// new section for fetching product_weight//


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
	<title>Online Grocery Store</title>

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
	<link rel="stylesheet" href="asset/bootstrap-5.0.2-dist/css/bootstrap-utilities.min.css">

	<!-- Template CSS -->
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/reponsive.css">
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
						<div class="form-search">
                                <form action="product-list-left-sidebar.php?search" method="GET"
                                    style="margin-left:8px;">
                                    <input type="text" class="form-input" name="search" placeholder="Search">
                                    <button type="submit" class="fa fa-search"></button>
                                </form>
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
</style>
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
		<!-- Main Content -->
		<div id="content" class="site-content">
			<!-- Breadcrumb -->
			<div id="breadcrumb">
				<div class="container">
					<div class="row d-flex align-items-center justify-content-center text-center mx-auto">
						<div class="col-5">
							<h2 class="title">Shopping Cart</h2>
							<h3><a href="homepage.php">Home</a></h3>
							<h3><a href="#">/Shopping Cart</a></h3>
						</div>
					</div>

				</div>
			</div>

			<!-- CHANGE HERE -->
			<div class="container">
				<div class="page-cart">
					<div class="table-responsive">
						<table class="cart-summary table table-bordered">
							<thead>
								<tr>
									<th class="width-20">&nbsp;</th>
									<th class="width-100 text-center">Image</th>
									<th class="width-40">Name</th>
									<th class="width-100 text-center">Unit price</th>
									<th class="width-100 text-center">Qty</th>
									<th class="width-100 text-center">Total</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$select_cart = mysqli_query($conn, "SELECT * FROM `shopping_cart` WHERE email='$email'") or die('query failed');
								$grand_total = 0;
								$shipping_fee = 0;
								$total_weight = 0;
								if (mysqli_num_rows($select_cart) > 0) {
									while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
										$product_id = $fetch_cart['product_id'];
										$product_weight = floatval(getProductWeight($conn, $product_id)); // Convert to float
										$total_weight += $product_weight * $fetch_cart['quantity'];

										?>
										<tr>
											<td class="product-remove">
												<a title="Remove this item" class="btn"
													href="product-cart.php?remove=<?php echo $fetch_cart['id']; ?>"
													onclick="return confirm('Remove item from cart?')">
													<i class="fa fa-times"></i>
												</a>
											</td>
											<td>
												<a href="product-detail-left-sidebar.php">
													<img width="80" alt="Product Image" class="img-responsive"
														src="img/product/<?php echo $fetch_cart['image']; ?>">
												</a>
											</td>
											<td>
												<a href="product-detail-left-sidebar.php" class="product-name">
													<?php echo $fetch_cart['name']; ?>
												</a>
											</td>
											<td class="text-center">RM
											<?php echo number_format($fetch_cart['price'], 2); ?>
											</td>
											<td class="text-center">
												<div class="product-quantity">
													<form action="" method="POST">
														<input type="hidden" name="update_quantity_id"
															value="<?php echo $fetch_cart['id']; ?>">
														<input id="form1" min="1" name="update_quantity"
															value="<?php echo $fetch_cart['quantity']; ?>" type="number"
															class="form-control form-control-lg text-center" />
														<input type="submit" value="update" class="btn mt-1"
															name="update_update_btn">
													</form>
												</div>
											</td>
											<td class="text-center">
											<?php echo number_format($fetch_cart['price'] * $fetch_cart['quantity'], 2); ?>
											</td>
											<?php
											$grand_total += number_format($fetch_cart['price'] * $fetch_cart['quantity'], 2);
									}

									// Calculate shipping fee based on total weight
									if ($total_weight <= 500) {
										$shipping_fee = 4;
									} elseif ($total_weight > 500 && $total_weight <= 1000) {
										$shipping_fee = 7;
									} else {
										$shipping_fee = 12;
									}

									$sum = $grand_total + $shipping_fee; // Calculate grand total
								}
								?>
							</tbody>

							<tfoot>
								<tr class="cart-total">
									<td rowspan="3" colspan="3"></td>
									<td colspan="2" class="text-right">Total products</td>
									<td colspan="1" class="text-center">RM
										<?php echo number_format($grand_total, 2); ?>
									</td>
								</tr>
								<tr class="cart-total">
									<td colspan="2" class="text-right">Total shipping</td>
									<td colspan="1" class="text-center">
										RM
										<?php echo number_format($shipping_fee, 2); ?>
									</td>
								</tr>
								<tr class="cart-total">
									<td colspan="2" class="total text-right">Total</td>
									<td colspan="1" class="total text-center">
										RM
										<?php echo number_format($grand_total, 2); ?>
									</td>
								</tr>
							</tfoot>



						</table>
					</div>

					<div class="checkout-btn text-right">
						<a href="product-checkout.php" class="btn btn-primary" title="Proceed to checkout">
							<span>Proceed to checkout</span>
							<i class="fa fa-angle-right ml-xs"></i>
						</a>
					</div>
				</div>
			</div>
			<!-- CHANGE HERE -->


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