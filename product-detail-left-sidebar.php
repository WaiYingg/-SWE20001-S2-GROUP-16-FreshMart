<?php
session_start();
include "config.php";

$id = $_GET['id'] ?? null; // Assuming the ID is passed as a URL parameter

$email = $_SESSION['email'] ?? '';

// Function to update product quantity

$message = [];

function updateProductQuantity($conn, $new_quantity, $cart_id, &$message)
{
    // Fetch the product ID and stock quantity from the shopping_cart table
    $select_cart_item = mysqli_prepare($conn, "SELECT pd.stock FROM shopping_cart sc
        JOIN product_db pd ON sc.product_id = pd.id
        WHERE sc.id=?");
    mysqli_stmt_bind_param($select_cart_item, "i", $cart_id);
    mysqli_stmt_execute($select_cart_item);
    $select_cart_result = mysqli_stmt_get_result($select_cart_item);
    $fetch_cart_item = mysqli_fetch_assoc($select_cart_result);

    if ($fetch_cart_item) {
        $product_stock = $fetch_cart_item['stock'];

        // Check if the requested quantity exceeds the available stock
        if ($new_quantity > $product_stock) {
            // Add the insufficient stock message only if it doesn't already exist
            if (!in_array("Insufficient stock for the product. Available stock: {$product_stock}.", $message)) {
                $message[] = "Insufficient stock for the product. Available stock: {$product_stock}.";
            }
        } else {
            // Update the quantity only if there is sufficient stock
            $update_query = mysqli_query($conn, "UPDATE `shopping_cart` SET quantity = '$new_quantity' WHERE id = '$cart_id'");
            // Clear previous messages
            if ($update_query) {
                // Add the success message only if it doesn't already exist
                if (!in_array('Product quantity updated successfully', $message)) {
                    $message[] = 'Product quantity updated successfully';
                }
            } else {
                // Add the failure message only if it doesn't already exist
                if (!in_array('Failed to update product quantity', $message)) {
                    $message[] = 'Failed to update product quantity';
                }
            }
        }
    } else {
        // Add the "Product not found in the cart" message only if it doesn't already exist
        if (!in_array('Product not found in the cart', $message)) {
            $message[] = 'Product not found in the cart';
        }
    }
}

if (isset($_POST['product_name']) && isset($_POST['product_price']) && isset($_POST['product_image']) && isset($_POST['product_quantity'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = intval($_POST['product_quantity']);

    if (isset($_POST['add_to_cart'])) {
        // Check if the user is logged in
        if ($email !== "") {
            // Fetch the product ID and stock quantity from the product_db table
            $select_product = mysqli_prepare($conn, "SELECT id, stock FROM product_db WHERE name=?");
            mysqli_stmt_bind_param($select_product, "s", $product_name);
            mysqli_stmt_execute($select_product);
            $fetch_product_result = mysqli_stmt_get_result($select_product);
            $fetch_product = mysqli_fetch_assoc($fetch_product_result);

            if ($fetch_product) {
                $product_id = $fetch_product['id'];
                $product_stock = $fetch_product['stock'];

                // Fetch the quantity from the shopping_cart table
                $cart_product_quantity_query = mysqli_prepare($conn, "SELECT SUM(quantity) as total_quantity FROM shopping_cart WHERE product_id=? AND email=?");
                mysqli_stmt_bind_param($cart_product_quantity_query, "is", $product_id, $email);
                mysqli_stmt_execute($cart_product_quantity_query);
                $cart_product_quantity_result = mysqli_stmt_get_result($cart_product_quantity_query);
                $cart_product_quantity_row = mysqli_fetch_assoc($cart_product_quantity_result);
                $cart_product_quantity = $cart_product_quantity_row ? $cart_product_quantity_row['total_quantity'] + $product_quantity : $product_quantity;

                // Check if the requested quantity exceeds the available stock
                if ($product_quantity <= 0) {
                    $message[] = "Invalid quantity. Quantity must be greater than 0.";
                } else if ($cart_product_quantity > $product_stock) {
                    $message[] = "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}.";
                } else {
                    // Check if the product is already in the cart
                    $select_cart = mysqli_prepare($conn, "SELECT * FROM shopping_cart WHERE product_id=? AND email=?");
                    mysqli_stmt_bind_param($select_cart, "is", $product_id, $email);
                    mysqli_stmt_execute($select_cart);
                    $select_cart_result = mysqli_stmt_get_result($select_cart);

                    if (mysqli_num_rows($select_cart_result) > 0) {
                        // Product already exists in the cart, so update the quantity
                        $fetch_cart = mysqli_fetch_assoc($select_cart_result);
                        $new_quantity = $fetch_cart['quantity'] + $product_quantity;

                        if ($new_quantity > $product_stock) {
                            $message[] = "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}.";
                        } else {
                            updateProductQuantity($conn, $new_quantity, $fetch_cart['id'], $message);
                        }
                    } else {
                        // Product is not in the cart, so add it
                        $insert_product = mysqli_prepare($conn, "INSERT INTO shopping_cart (name, price, image, quantity, email, product_id) VALUES (?, ?, ?, ?, ?, ?)");
                        mysqli_stmt_bind_param($insert_product, "sssssi", $product_name, $product_price, $product_image, $product_quantity, $email, $product_id);
                        mysqli_stmt_execute($insert_product);

                        if ($insert_product) {
                            $message[] = 'Product added to cart successfully';
                        } else {
                            $message[] = 'Failed to add product to cart';
                        }
                    }
                }
            } else {
                $message[] = 'Product not found in the database';
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
                        class="product-price">RM <?php echo number_format($fetch_cart['price'],2); ?></span>
                </span>
            </div>
    </td>
</tr>


													<?php
													$subtotal = number_format($fetch_cart['price'],2) * $fetch_cart['quantity'];
													$grand_total += $subtotal;
												}
											} else {
												echo "<tr><td colspan='3'><h2>Cart is empty</h2></td></tr>";
											}
											?>

											<tr class="total">
												<td>Total:</td>
												<td colspan="2">$
                                                <?php echo number_format($grand_total, 2); ?>
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
		color: #696969;
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
        padding: 2rem 2rem;
    }

    .flash_message h2 {
		padding: 1rem 1rem;
		background-color: #F0F8FF;
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
		<div class="flash_message text-center mx-auto" style="font-family: Arial;">
    <?php
    if (isset($message) && is_array($message)) {
        foreach ($message as $msg) {
            echo '<h2>' . $msg . '</h2>';
        }
    }
    ?>
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
                        <?php
                        $specific_product = mysqli_query($conn, "SELECT * FROM `product_db` WHERE id=$id");
                        if ($fetch_specific_product = mysqli_fetch_assoc($specific_product)) {
                            $low_stock_threshold = 5; // Define the threshold value for low stock
                            $product_name = $fetch_specific_product['name'];
                            $product_stock = $fetch_specific_product['stock'];
                            ?>
                            <div class="col-5">
                                <h2 class="title">Details</h2>
                                <h3>
                                    <a href="homepage.php">Home / </a>
                                    <a href="product-list-left-sidebar.php">
                                        <?php echo $fetch_specific_product['category']; ?>
                                        <?php echo $fetch_specific_product['name']; ?>
                                    </a>
                                </h3>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <!-- Sidebar -->
                    <?php include "dynamic/left-sidebar.php"; ?>
                    <!-- Page Content -->
                    <div id="center-column" class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                        <div class="product-detail">
                            <div class="products-block layout-5">
                                <div class="product-item">
                                    <div class="row">
                                        <div class="product-left col-md-5 col-sm-5 col-xs-12">
                                            <div class="product-image horizontal">
                                                <div class="main-image">
                                                    <img class="img-responsive"
                                                        src="img/product/<?php echo $fetch_specific_product['image']; ?>"
                                                        alt="Product Image">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="product-right col-md-7 col-sm-7 col-xs-12">

                                            <div class="product-info">
                                                <div class="product-title">
                                                    <?php echo $fetch_specific_product['name']; ?>
                                                </div>
                                                <div class="product-price">
                                                    <span class="sale-price">
                                                        RM
                                                        <?php echo number_format($fetch_specific_product['price'], 2); ?>
                                                    </span>
                                                </div>

                                                <div class="product-stock">
                                                    <span class="availability">Availability :</span>
                                                    <i class="fa fa-check-square-o" aria-hidden="true"></i>In stock
                                                </div>
                                                <div class="product-add-to-cart border-bottom">

                                                    <form action="" method="POST">
                                                        <div class="product-quantity">
                                                            <span class="control-label">QTY :</span>
                                                            <div class="qty">
                                                                <div class="input-group">
                                                                    <input type="number" name="product_quantity"
                                                                        value="1" min="1" max="999">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="product-buttons">
                                                            <input type="hidden" name="product_id"
                                                                value="<?php echo $fetch_specific_product['id']; ?>">
                                                            <input type="hidden" name="product_name"
                                                                value="<?php echo $fetch_specific_product['name']; ?>">
                                                            <input type="hidden" name="product_price"
                                                                value="<?php echo $fetch_specific_product['price']; ?>">
                                                            <input type="hidden" name="product_image"
                                                                value="<?php echo $fetch_specific_product['image']; ?>">
                                                            <input type="submit" name="add_to_cart" value="Add To Cart"
                                                                class="btn">
                                                        </div>
                                                    </form>
                                                    <div class="product-share border-bottom">
                                                        <div class="item">
                                                            <a href="#">
                                                                <i class="zmdi zmdi-share" aria-hidden="true"></i>
                                                                <span class="text">Share</span>
                                                            </a>
                                                        </div>
                                                        <div class="item">
                                                            <a href="#">
                                                                <i class="zmdi zmdi-email" aria-hidden="true"></i>
                                                                <span class="text">Send to a friend</span>
                                                            </a>
                                                        </div>
                                                        <div class="item">
                                                            <a href="#">
                                                                <i class="zmdi zmdi-print" aria-hidden="true"></i>
                                                                <span class="text">Print</span>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div class="product-review border-bottom">
                                                        <div class="item">
                                                            <div class="product-quantity">
                                                                <span class="control-label">Review :</span>
                                                                <div class="product-rating">
                                                                    <div class="star on"></div>
                                                                    <div class="star on"></div>
                                                                    <div class="star on"></div>
                                                                    <div class="star on"></div>
                                                                    <div class="star"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="product-extra">
                                                        <div class="item">
                                                            <span class="control-label">Review :</span>
                                                            <span class="control-label">E-02154</span>
                                                        </div>
                                                        <div class="item">
                                                            <span class="control-label">Categories :</span>
                                                            <a href="#" title="">
                                                                <?php echo $fetch_specific_product['category']; ?>
                                                            </a>
                                                        </div>
                                                        <div class="item">
                                                            <span class="control-label">Tags :</span>
                                                            <a href="#" title="Vegetables">Hot Trend,</a>
                                                            <a href="#" title="Fruits">Summer</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="product-tab">
                                            <!-- Tab Navigation -->
                                            <div class="tab-nav">
                                                <ul>
                                                    <li class="active">
                                                        <a data-toggle="tab" href="#description">
                                                            <span>Description</span>
                                                        </a>
                                                        <div class="product-short-description">
                                                            <?php echo $fetch_specific_product['description']; ?>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Related Products -->
                            <div class="products-block related-products">
                                <div class="block-title">
                                    <h2 class="title">Related <span>Products</span></h2>
                                </div>
                                <div class="block-content">
                                    <div class="products owl-theme owl-carousel">
                                        <?php
                                        $current_category = $fetch_specific_product['category'];
                                        $related_products = mysqli_query($conn, "SELECT DISTINCT * FROM `product_db` WHERE category='$current_category' AND id != $id");
                                        while ($fetch_related_product = mysqli_fetch_assoc($related_products)) {

                                            ?>
                                            <div class="product-item">
                                                <div class="product-image">
                                                    <a
                                                        href="product-detail-left-sidebar.php?id=<?php echo $fetch_related_product['id']; ?>">
                                                        <img src="img/product/<?php echo $fetch_related_product['image']; ?>"
                                                            alt="Product Image">
                                                    </a>
                                                </div>

                                                <div class="col-12 product-title text-center">
                                                    <a
                                                        href="product-detail-left-sidebar.php?id=<?php echo $fetch_related_product['id']; ?>">
                                                        <?php echo $fetch_related_product['name']; ?>
                                                    </a>
                                                </div>

                                                <div class="product-rating">
                                                    <div class="star on"></div>
                                                    <div class="star on"></div>
                                                    <div class="star on"></div>
                                                    <div class="star on"></div>
                                                    <div class="star"></div>
                                                </div>

                                                <div class="col-12 product-price">
                                                    <span class="sale-price">RM
                                                    <?php echo number_format($fetch_related_product['price'], 2); ?>
                                                    </span>
                                                </div>

                                                <form action="" method="POST">
                                                    <input type="hidden" name="product_name"
                                                        value="<?php echo $fetch_related_product['name']; ?>">
                                                    <input type="hidden" name="product_price"
                                                        value="<?php echo $fetch_related_product['price']; ?>">
                                                    <input type="hidden" name="product_image"
                                                        value="<?php echo $fetch_related_product['image']; ?>">
                                                    <!-- Add a hidden input field to store the product quantity -->
                                                    <input type="hidden" name="product_quantity" value="1">
                                                    <div class="product-buttons" style="left:27%;">
                                                        <input type="submit" name="add_to_cart" value="Add To cart"
                                                            class="btn">
                                                    </div>
                                                </form>
                                            </div>
                                            <?php
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

    </div>
    </div>
    </div>


    <!-- Footer -->
    <?php include "dynamic/footer.php"; ?>

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
    <!-- Template CSS -->
    <script src="js/main.js"></script>
</body>

</html>