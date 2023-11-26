<?php
session_start();
include("config.php");

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
	// Redirect or show an error message
	exit("You must be logged in to view this page.");
}

$email = $_SESSION['email'];

function updateProductQuantity($conn, $new_quantity, $cart_id, &$message)
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
                if (mysqli_num_rows($select_cart) > 0) {
                    // Product already exists in the cart, so update the quantity
                    $fetch_cart = mysqli_fetch_assoc($select_cart);
                    $new_quantity = $fetch_cart['quantity'] + $product_quantity;
                    $update_quantity_query = mysqli_query($conn, "UPDATE `shopping_cart` SET quantity = '$new_quantity' WHERE id = '{$fetch_cart['id']}'");

                    if ($update_quantity_query) {
                        $message[] = 'Product quantity updated successfully';
                    } else {
                        $message[] = 'Failed to update product quantity';
                    }
                } else {
                    // Product is not in the cart, so add it
                    $insert_product = mysqli_query($conn, "INSERT INTO shopping_cart (name, price, image, quantity, email, product_id) VALUES ('$product_name', '$product_price', '$product_image', '$product_quantity', '$email', '$product_id')");
                    if ($insert_product) {
                        $message[] = 'Product added to cart successfully';
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
}

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

// Fetch the shipping address for the user
// Fetch the shipping address for the user
$stmt = $conn->prepare("SELECT * FROM shipping_address WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();


// Check if the shipping address exists for the user
if ($result->num_rows > 0) {
	// Retrieve the shipping address data
	$fetch_profile = $result->fetch_assoc();

	// Extract the individual fields
	$firstName = $fetch_profile['first_name'];
	$lastName = $fetch_profile['last_name'];
	$contactNumber = $fetch_profile['contact_number'];
	$address = $fetch_profile['address'];
	$state = $fetch_profile['state'];
	$city = $fetch_profile['city'];
	$postcode = $fetch_profile['postcode'];
} else {
	// Initialize the fields with empty values
	$firstName = "";
	$lastName = "";
	$contactNumber = "";
	$address = "";
	$state = "";
	$city = "";
	$postcode = "";
}



$email = $_SESSION['email'] ?? '';

// Assuming $conn is your database connection variable

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

$email = $_SESSION['email'] ?? '';

// Check if the necessary form data is set
if (isset($_POST['product_name']) && isset($_POST['product_price']) && isset($_POST['product_image']) && isset($_POST['product_quantity'])) {
	$product_name = $_POST['product_name'];
	$product_price = $_POST['product_price'];
	$product_image = $_POST['product_image'];
	$product_quantity = intval($_POST['product_quantity']);

	if (isset($_POST['add_to_cart'])) {
		// Check if the user is logged in
		if ($email !== "") {

			$select_product = mysqli_query($conn, "SELECT id,stock FROM product_db WHERE name='$product_name'");
			$fetch_product = mysqli_fetch_assoc($select_product);
			$product_id = $fetch_product['id'];
			$product_stock = $fetch_product['stock'];
			if ($product_quantity > $product_stock) {
				$message[] = "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}, Requested quantity: {$product_quantity}";
			} else {
				$select_cart = mysqli_query($conn, "SELECT * FROM shopping_cart WHERE name='$product_name' AND email='$email'");

				if (mysqli_num_rows($select_cart) > 0) {
					// Product already exists in the cart, so update the quantity
					$fetch_cart = mysqli_fetch_assoc($select_cart);
					$new_quantity = $fetch_cart['quantity'] + $product_quantity;
					$update_quantity_query = mysqli_query($conn, "UPDATE `shopping_cart` SET quantity = '$new_quantity' WHERE id = '{$fetch_cart['id']}'");

					if ($update_quantity_query) {
						$message[] = 'Product quantity updated successfully';
					} else {
						$message[] = 'Failed to update product quantity';
					}

				} else {
					// Product is not in the cart, so add it
					// Fetch the product ID from the product_db1 table



					$insert_product = mysqli_query($conn, "INSERT INTO shopping_cart (name, price, image, quantity, email, product_id) VALUES ('$product_name', '$product_price', '$product_image', '$product_quantity', '$email', '$product_id')");
					if ($insert_product) {
						$message[] = 'Product added to cart successfully';
					} elseif ($product_quantity > $product_stock) {
						$message[] = "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}, Requested quantity: {$product_quantity}";

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
}

if (isset($_POST['update_quantity']) && isset($_POST['update_quantity_id'])) {
	// Check if the user is logged in
	if ($email !== "") {
		$update_value = intval($_POST['update_quantity']);
		$update_id = $_POST['update_quantity_id'];
		updateProductQuantity($conn, $update_value, $update_id, $message);
	} else {
		// User is not logged in, redirect to the login page
		header('Location: login.php');
		exit();
	}
}


if (isset($_GET['remove'])) {
	// Check if the user is logged in
	if ($email !== "") {
		$remove_id = $_GET['remove'];
		mysqli_query($conn, "DELETE FROM shopping_cart WHERE id='$remove_id'");
		$message[] = 'Product removed from cart successfully';
	} else {
		// User is not logged in, redirect to the login page
		header('Location: login.php');
		exit();
	}
}
//changed until
if (isset($_POST['update_shippingaddress'])) {
	$firstName = $_POST['first_name'];
	$lastName = $_POST['last_name'];
	$contactNumber = $_POST['contact_number'];
	$address = $_POST['address'];
	$state = $_POST['state'];
	$city = $_POST['city'];
	$postcode = $_POST['postcode'];

	// Check if the shipping address already exists for the user
	$stmt = $conn->prepare("SELECT COUNT(*) as count FROM shipping_address WHERE email = ?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();

	if ($row['count'] > 0) {
		// Shipping address already exists, so update it
		$stmt = $conn->prepare("UPDATE shipping_address SET first_name=?, last_name=?, contact_number=?, address=?, state=?, city=?, postcode=? WHERE email=?");
		$stmt->bind_param("ssssssss", $firstName, $lastName, $contactNumber, $address, $state, $city, $postcode, $email);
		if ($stmt->execute()) {
			// Shipping address updated successfully
			echo "Shipping address updated successfully.";
		} else {
			echo "Failed to update the shipping address: " . mysqli_error($conn);
		}
	} else {
		// Shipping address does not exist, so insert it
		$stmt = $conn->prepare("INSERT INTO shipping_address (email, first_name, last_name, contact_number, address, state, city, postcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssss", $email, $firstName, $lastName, $contactNumber, $address, $state, $city, $postcode);
		if ($stmt->execute()) {
			// Shipping address inserted successfully
			echo "Shipping address inserted successfully.";
		} else {
			echo "Failed to insert the shipping address: " . mysqli_error($conn);
		}
	}
}



if (isset($_POST['proceed'])) {
	// Check if the shipping address is already stored in the orders1 table
	$stmt = $conn->prepare("SELECT COUNT(*) as count FROM shipping_address WHERE email = ?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();

	if ($row['count'] == 0) {
		// Shipping address not stored yet, so store it in the orders1 table
		$firstName = $_POST['first_name'];
		$lastName = $_POST['last_name'];
		$contactNumber = $_POST['contact_number'];
		$address = $_POST['address'];
		$state = $_POST['state'];
		$city = $_POST['city'];
		$postcode = $_POST['postcode'];

		// Insert or update the shipping address in the orders1 table
		$stmt = $conn->prepare("INSERT INTO orders1 (email, first_name, last_name, contact_number, address, state, city, postcode, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
		$stmt->bind_param("ssssssss", $email, $firstName, $lastName, $contactNumber, $address, $state, $city, $postcode);

		if ($stmt->execute()) {
			// Shipping address inserted successfully
			$message[] = 'Shipping address stored.';
		} else {
			$message[] = 'Failed to store the shipping address.';
		}
	}
	// Retrieve the last order ID from the orders1 table
	$stmt = $conn->prepare("SELECT MAX(order_id) AS max_order_id FROM orders1");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$lastOrderId = $row['max_order_id'];

	// Increment the order ID by 1
	if ($lastOrderId !== null) {
		$newOrderId = intval($lastOrderId) + 1;
	} else {
		$newOrderId = 1;
	}
	// Fetch the product information from the shopping_cart table
	$stmt = $conn->prepare("SELECT name, price, image, quantity, product_id FROM shopping_cart WHERE email = ?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();

	// Check if there are products in the cart
	if ($result->num_rows > 0) {
		// Prepare the statement to insert each product into the orders1 table
		$insert_stmt = $conn->prepare("INSERT INTO orders1 (order_id, email, first_name, last_name, contact_number, address, state, city, postcode, name, price, image, quantity, product_id, date, time,order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME(),?)");
		$insert_stmt->bind_param("issssssssssssis", $newOrderId, $email, $firstName, $lastName, $contactNumber, $address, $state, $city, $postcode, $product_name, $product_price, $product_image, $product_quantity, $product_id, $orderStatus);

		// Execute the insert statement for each product
		while ($row = $result->fetch_assoc()) {
			$product_name = $row['name'];
			$product_price = $row['price'];
			$product_image = $row['image'];
			$product_quantity = $row['quantity'];
			$product_id = $row['product_id'];
			$orderStatus = 'pending';


			$insert_stmt->execute();
		}

		$insert_stmt->close();

		$message[] = 'Order placed successfully.';

	} else {
		$message[] = 'No products in the cart.';
	}

	// Clear the shopping_cart table for the user
	$stmt = $conn->prepare("DELETE FROM shopping_cart WHERE email = ?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
} else {
	// Handle the case when the 'proceed' POST parameter is not set
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
			width: 40%;
			/* You can adjust this width as needed */
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
</head>

<body class="home home-1">
	<div id="all">
	<div class="flash_message text-center mx-auto" style="font-family: Arial;">
	<?php
	if (isset($message)) {
		foreach ($message as $msg) {
			echo '<h2>' . $msg . '</h2>';
		}
	}
	?>
	</div>
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
							<h2 class="title">Checkout</h2>
							<h3><a href="homepage.php">Home</a></h4>
								<h3><a href="#">/Checkout</a></h4>
						</div>
					</div>

				</div>
			</div>

			<div class="container">
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
						width: 40%;
						/* You can adjust this width as needed */
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
				<div class="page-checkout">
					<div class="row">
						<div class="checkout-left col-lg-9 col-md-9 col-sm-9 col-xs-12">
							<p>Returning customer? <a class="login" href="login.php">Click here to login</a>.</p>
							<div class="panel-group" id="accordion">

								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a class="accordion-toggle collapsed" data-bs-toggle="collapse"
												data-bs-parent="#accordion" href="#collapseOne">
												Shipping Address
											</a>
										</h4>
									</div>
									<div class="accordion-item">
										<div id="collapseOne" class="accordion-collapse collapse"
											aria-labelledby="headingOne" data-bs-parent="#accordion">
											<div class="row accordion-body">
												<form action="#" id="formaddress" method="POST" class="form">
													<div class="row">
														<div class="col-md-6 mb-3">
															<label for="firstName">First Name</label>
															<input type="text" id="firstName" name="first_name"
																value="<?php echo htmlspecialchars($firstName); ?>"
																placeholder="Firstname" required class="form-control">
														</div>
														<div class="col-md-6 mb-3">
															<label for="lastName">Last Name</label>
															<input type="text" id="lastName" name="last_name"
																value="<?php echo htmlspecialchars($lastName); ?>"
																placeholder="Lastname" required class="form-control">
														</div>
													</div>
													<div class="row">
														<div class="col-md-6 mb-3">
															<label for="contactNumber">Contact Number</label>
															<input type="text" id="contactNumber" name="contact_number"
																value="<?php echo htmlspecialchars($contactNumber); ?>"
																placeholder="Enter contact number" class="form-control">
														</div>
														<div class="col-md-6 mb-3">
															<label for="address">Address</label>
															<input type="text" name="address" id="address"
																value="<?php echo htmlspecialchars($address); ?>"
																placeholder="Enter address" class="form-control">
														</div>
													</div>
													<div class="row">
														<div class="col-md-6 mb-3">
															<label for="state">State</label>
															<select class="form-control" id="state" name="state"
																style="font-size: 12px;">
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
														<div class="col-md-3 mb-3">
															<label for="city">City</label>
															<input type="text" id="city" name="city"
																value="<?php echo htmlspecialchars($city); ?>"
																class="form-control">
														</div>
														<div class="col-md-3 mb-3">
															<label for="postcode">Postcode</label>
															<input type="text" id="postcode" name="postcode"
																value="<?php echo htmlspecialchars($postcode); ?>"
																class="form-control">
														</div>
													</div>
													<div class="form-group">
														<div class="col-md-12 mt-3">
															<input type="submit" class="btn"
																value="Update Shipping Address"
																name="update_shippingaddress">
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>




								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a class="accordion-toggle collapsed" data-bs-toggle="collapse"
												data-bs-parent="#accordion" href="#collapseThree">
												Payment
											</a>
										</h4>
									</div>
									<div class="accordion-item">
										<div id="collapseThree" class="accordion-collapse collapse"
											aria-labelledby="headingThree" data-bs-parent="#accordion">
											<div class="accordion-body">
												<table class="table table-bordered">
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
														$sum = 0; //changed
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
																			<img width="80" alt="Product Image"
																				class="img-responsive"
																				src="img/product/<?php echo $fetch_cart['image']; ?>">
																		</a>
																	</td>
																	<td>
																		<a href="product-detail-left-sidebar.php"
																			class="product-name">
																			<?php echo $fetch_cart['name']; ?>
																		</a>
																	</td>
																	<td class="text-center">RM
																		<?php echo number_format($fetch_cart['price']); ?>
																	</td>
																	<td class="text-center">
																		<div class="product-quantity">
																			<form action="" method="POST">
																				<input type="hidden" name="update_quantity_id"
																					value="<?php echo $fetch_cart['id']; ?>">
																				<input id="form1" min="1" name="update_quantity"
																					value="<?php echo $fetch_cart['quantity']; ?>"
																					type="number"
																					class="form-control form-control-lg text-center" />
																				<input type="submit" value="update"
																					class="btn mt-1" name="update_update_btn">
																			</form>
																		</div>
																	</td>
																	<td class="text-center">
																		<?php echo number_format($fetch_cart['price'] * $fetch_cart['quantity']); ?>
																	</td>
																	<?php
																	$grand_total += $fetch_cart['price'] * $fetch_cart['quantity'];
															}

															// Calculate shipping fee based on total weight
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
																<?php echo number_format($sum, 2); ?>
															</td>
														</tr>
													</tfoot>



												</table>

											</div>
										</div>

									</div>
								</div>
							</div>

							<div class="pull-right">
								<form method="POST">
									<input type="submit" value="Place Order" name="proceed" class="btn btn-primary">
								</form>
							</div>
						</div>

						<div class="checkout-right col-lg-3 col-md-3 col-sm-3 col-12 my-auto">
							<h4 class="h4">Cart Total</h4>
							<table class="table">
								<tbody>
									<tr class="cart-subtotal">
										<th>
											<strong>Cart Subtotal</strong>
										</th>
										<td colspan="1" class="text-center">RM
											<?php echo number_format($grand_total, 2); ?>
										</td>
									</tr>
									<tr class="shipping">
										<th>
											Shipping
										</th>
										<td colspan="1" class="text-center">RM
											<?php echo number_format($shipping_fee, 2); ?>
										</td>
									</tr>
									<tr class="total">
										<th>
											<strong>Order Total</strong>
										</th>
										<td colspan="1" class="total text-center">RM
											<?php echo number_format($sum, 2); ?>
										</td>
									</tr>
								</tbody>
							</table>
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