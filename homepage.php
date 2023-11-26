<?php session_start();
include "config.php";


$email = $_SESSION['email'] ?? '';

$message = []; // Initialize an empty array to store error messages

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

// Check if the necessary form data is set
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
            $fetch_product = mysqli_stmt_get_result($select_product);
            $fetch_product = mysqli_fetch_assoc($fetch_product);

            if ($fetch_product) {
                $product_id = $fetch_product['id'];
                $product_stock = $fetch_product['stock'];

                // Fetch the quantity from the shopping_cart table
                $cart_product_quantity_query = mysqli_prepare($conn, "SELECT quantity FROM shopping_cart WHERE name=?");
                mysqli_stmt_bind_param($cart_product_quantity_query, "s", $product_name);
                mysqli_stmt_execute($cart_product_quantity_query);
                $cart_product_quantity_result = mysqli_stmt_get_result($cart_product_quantity_query);
                $row = mysqli_fetch_assoc($cart_product_quantity_result);
                $cart_product_quantity = $row ? $row['quantity'] : 0;

                // Check if the requested quantity exceeds the available stock
                if ($product_quantity <= 0) {
                    $message[] = "Invalid quantity. Quantity must be greater than 0.";
                } else if ($product_quantity + $cart_product_quantity > $product_stock) {
                    $message[] = "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}. Our admin will restock as soon as possible.";
                } else {
                    // Check if the product is already in the cart
                    $select_cart = mysqli_prepare($conn, "SELECT * FROM shopping_cart WHERE name=? AND email=?");
                    mysqli_stmt_bind_param($select_cart, "ss", $product_name, $email);
                    mysqli_stmt_execute($select_cart);
                    $select_cart_result = mysqli_stmt_get_result($select_cart);

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
                $message[] = 'Product not found in database';
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

    <!-- Template CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reponsive.css">
</head>

<body class="home home-3">
    <div id="all">
        <!-- Header -->
        <header id="header">
            <div class="container">
                <div class="header-top" id="homepage-header">
                    <div class="mobile-toggler d-lg-none">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#navModal">
                            <i class="fa-solid fa-bars"></i>
                        </a>
                    </div>
                    <div class="row align-items-center" id="homepage-navMenu">
                        <!-- Header Left -->
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
                                    <div class="modal" id="navModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <img src="img/logo.png" alt="">
                                                    <button class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
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
                                                        <i class="fa-solid fa-circle-info"></i><a
                                                            href="aboutUs.php">About Us</a>
                                                    </div>

                                                    <div class="modal-line">
                                                        <i class="fa-solid fa-phone"></i><a href="contact.php">Contact
                                                            Us</a>
                                                    </div>

                                                    <div class="modal-line">
                                                        <div class="item">
                                                            <div class="dropdown account text-start">
                                                                <div class="dropdown-toggle" data-toggle="dropdown">
                                                                    <i class="fa-solid fa-user"></i><a
                                                                        href="#">Profile</a>
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

                        <!-- Header Center -->
                        <div class="col-lg-2 col-md-2 col-sm-12 header-center justify-content-center">

                            <!-- Logo -->
                            <div class="logo">
                                <a href="homepage.html">
                                    <img class="img-responsive .circular-logo" src="img/logo.png" alt="Logo">
                                </a>
                            </div>
                        </div>


                        <!-- Header Right -->
                        <div class="col-lg-5 col-md-5 col-sm-12 header-right d-flex justify-content-end align-items-end">
                            <!-- Search -->
                            <div class="form-search me-auto">
                                <form action="product-list-left-sidebar.php?search" method="GET"
                                    style="margin-left:8px;margin-right:1rem;">
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
                            <div class="order-check me-auto mt-1">
                                <a href="orders.php" style="color:#78b144;font-size:3rem;">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <?php
// Assuming $message is an array
if (is_array($message)) {
    foreach ($message as $msg) {
        if (is_array($msg) && isset($msg['type']) && isset($msg['message'])) {
            $messageType = $msg['type'];
            $messageText = $msg['message'];

            // Apply the appropriate CSS class based on the message type
            $messageClass = ($messageType === 'success') ? 'success-message' : 'error-message';

            // Display the message with the corresponding background color
            echo '<div class="' . $messageClass . '">' . $messageText . '</div>';
        } else {
            // Handle invalid message structure
            echo 'Invalid message structure.';
        }
    }
} else {
    // Handle the case where $message is not an array
    echo 'No messages to display.';
}
?>



    <style>
    .success-message {
        background-color: green;
        color: white;
            position: fixed;
            top: 20%;
			left: 30%;
            z-index: 1000;
            animation: slide-up 3s forwards;
            animation-delay: 2.55s;
            width: 40%;
            padding: 1em;
			display: flex;
            justify-content: center;
    }

    .error-message {
		color: #fff;
            position: fixed;
            top: 20%;
			left: 30%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: slide-up 3s forwards;
            animation-delay: 2.55s;
            width: 40%;
            /* You can adjust this width as needed */
            padding: 1em;
        background-color: red;
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

        <!-- Main Content -->
        <div id="content" class="site-content">
            <!-- Slideshow -->
            <div class="section slideshow">
                <div class="tiva-slideshow-wrapper">
                    <div id="tiva-slideshow" class="nivoSlider">
                        <a href="#">
                            <img class="img-responsive" src="img/slideshow/home3-slideshow-1.jpg" alt="Slideshow Image">
                        </a>
                        <a href="#">
                            <img class="img-responsive" src="img/slideshow/home3-slideshow-2.jpg" alt="Slideshow Image">
                        </a>
                        <a href="#">
                            <img class="img-responsive" src="img/slideshow/home3-slideshow-3.jpg" alt="Slideshow Image">
                        </a>
                    </div>
                </div>
            </div>

            <!-- Intro -->
            <div class="section intro">
                <div class="block-content">
                    <div class="container">
                        <div class="intro-content">
                            <div class="row">
                                <div class="title">Why Choose Us</div>
                                <div class="col-lg-6 col-md-6 col-xs-6 item up-left">
                                    <h4>100% Natural</h4>
                                    <p>Discover a bountiful selection of 100% natural products at our grocery store, where we are dedicated to providing you with the freshest and highest quality items for a healthier lifestyle.</p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xs-6 item up-right">
                                    <h4>Always Fresh</h4>
                                    <p>Experience the delight of always fresh produce and groceries at our store, where quality and flavor meet to elevate your shopping experience.
</p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xs-6 item down-left">
                                    <h4>Premium Quality</h4>
                                    <p>Experience pure delight and unparalleled satisfaction as you savor the extraordinary flavors and uncompromising quality of our meticulously curated selection of premium products.</p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-xs-6 item down-right">
                                    <h4>Super Healthy</h4>
                                    <p>Boost your well-being with our super healthy products, specially crafted to nourish your body and invigorate your senses.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Product -->
            <div class="two-column">
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 mx-auto">
                        <div class="section products-block category-double no-border">
                            <div class="block-title">
                                <h2 class="title">Best <span>Seller</span></h2>
                                <div class="sub-title">Lorem ipsum dolor sit amet consectetur adipiscing</div>
                            </div>

                            <div class="block-content">
                                <div class="products owl-theme owl-carousel">
                                    <?php
                                    $featured_product = mysqli_query($conn, "SELECT * FROM product_db WHERE featured='Yes'");

                                    while ($row = mysqli_fetch_assoc($featured_product)) {
                                        $product_name = $row['name'];
                                        $product_image = $row['image'];
                                        $product_price = $row['price'];
                                        $product_id = $row['id'];
                                        ?>

                                        <div class="product-item">
                                            <div class="product-image">
                                                <img class="img-responsive" src="img/product/<?php echo $product_image; ?>"
                                                    alt="Product Image">
                                            </div>

                                            <div class="product-title">
                                                <a href="product-detail-left-sidebar.php">
                                                    <?php echo $product_name; ?>
                                                </a>
                                            </div>

                                            <div class="product-price">
                                                <span class="sale-price">RM
                                                <?php echo number_format($product_price, 2); ?>
                                                </span>
                                            </div>

                                            <div class="product-buttons">
                                                <form action="" method="POST" onsubmit="addToCart(this); return false;">
                                                    <input type="hidden" name="product_name"
                                                        value="<?php echo $product_name; ?>">
                                                    <input type="hidden" name="product_price"
                                                        value="<?php echo $product_price; ?>">
                                                    <input type="hidden" name="product_image"
                                                        value="<?php echo $product_image; ?>">
                                                    <input type="hidden" name="product_id"
                                                        value="<?php echo $product_id; ?>">
                                                    <input type="hidden" name="product_quantity" value="1">
                                                    <input type="submit" name="add_to_cart" class="btn" value="Add to cart">
                                                </form>
                                            </div>
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

            <!-- Product -->
            <div class="two-column">
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 mx-auto">
                        <div class="section products-block category-double no-border">
                            <div class="block-title">
                                <h2 class="title">Latest <span>Products</span></h2>
                                <div class="sub-title">Lorem ipsum dolor sit amet consectetur adipiscing</div>
                            </div>

                            <div class="block-content">
                                <div class="products owl-theme owl-carousel">
                                    <?php
                                    $select_latest_products = mysqli_query($conn, "SELECT * FROM `product_db` ORDER BY id DESC LIMIT 10");

                                    while ($row = mysqli_fetch_assoc($select_latest_products)) {
                                        $product_name = $row['name'];
                                        $product_image = $row['image'];
                                        $product_price = $row['price'];
                                        $product_id = $row['id'];
                                        ?>

                                        <div class="product-item">
                                            <div class="product-image">
                                                <img class="img-responsive" src="img/product/<?php echo $product_image; ?>"
                                                    alt="Product Image">
                                            </div>

                                            <div class="product-title">
                                                <a href="product-detail-left-sidebar.php">
                                                    <?php echo $product_name; ?>
                                                </a>
                                            </div>

                                            <div class="product-price">
                                                <span class="sale-price">RM
                                                <?php echo number_format($product_price, 2); ?>
                                                </span>
                                            </div>

                                            <div class="product-buttons">
                                                <form action="" method="POST" onsubmit="addToCart(this); return false;">
                                                    <input type="hidden" name="product_name"
                                                        value="<?php echo $product_name; ?>">
                                                    <input type="hidden" name="product_price"
                                                        value="<?php echo $product_price; ?>">
                                                    <input type="hidden" name="product_image"
                                                        value="<?php echo $product_image; ?>">
                                                    <input type="hidden" name="product_id"
                                                        value="<?php echo $product_id; ?>">
                                                    <input type="hidden" name="product_quantity" value="1">
                                                    <input type="submit" name="add_to_cart" class="btn" value="Add to cart">
                                                </form>
                                            </div>
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



            <!-- Banners -->
            <div class="section banners">
                <div class="row margin-0">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-0">
                        <div class="banner-item">
                            <div class="text">
                                <h3>Vegetable and Fruit</h3>
                                <p>Explore the vibrant world of fresh vegetables and fruits, brimming with essential nutrients and bursting with natural flavors.</p>
                                <a class="button" href="product-list-left-sidebar.php?category=Vegetable%20Fruit"><i
                                        class="fa fa-shopping-cart" aria-hidden="true"></i>SHOP NOW</a>
                            </div>
                            <div class="image-mask"></div>
                            <img class="img-responsive" src="img/banner/home3-banner-1.jpg" alt="Banner">
                        </div>
                    </div>

                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-0">
                        <div class="row margin-0">
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-0">
                                <div class="banner-item">
                                    <div class="text">
                                        <h3>Seafood</h3>
                                        <p>Dive into the ocean's bounty with our succulent seafood selection, delivering the taste of the sea straight to your plate.</p>
                                        <a class="button" href="product-list-left-sidebar.php?category=Seafood"><i
                                                class="fa fa-shopping-cart" aria-hidden="true"></i>SHOP NOW</a>
                                    </div>
                                    <div class="image-mask"></div>
                                    <img class="img-responsive" src="img/banner/home3-banner-2.jpg" alt="Banner">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-0">
                                <div class="banner-item">
                                    <div class="text">
                                        <h3>Food Essentials</h3>
                                        <p>Discover our wide range of food essentials that are a must-have in every kitchen. From pantry staples like grains, oils, and spices to baking ingredients and condiments, we've got you covered with everything you need to create delicious and nutritious meals.</p>
                                        <a class="button"
                                            href="product-list-left-sidebar.php?category=Food%20Essentials"><i
                                                class="fa fa-shopping-cart" aria-hidden="true"></i>SHOP NOW</a>
                                    </div>
                                    <div class="image-mask"></div>
                                    <img class="img-responsive" src="img/banner/home3-banner-3.jpg" alt="Banner">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-0">
                                <div class="banner-item">
                                    <div class="text">
                                        <h3>Meat</h3>
                                        <p>Savor the juiciness and rich flavors of our premium meats, carefully sourced and expertly prepared to satisfy even the most discerning carnivores.</p>
                                        <a class="button" href="product-list-left-sidebar.php?category=Meat"><i
                                                class="fa fa-shopping-cart" aria-hidden="true"></i>SHOP NOW</a>
                                    </div>
                                    <div class="image-mask"></div>
                                    <img class="img-responsive" src="img/banner/home3-banner-4.jpg" alt="Banner">
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-0">
                                <div class="banner-item">
                                    <div class="text">
                                        <h3>Beverage</h3>
                                        <p>Quench your thirst with our refreshing selection of beverages, ranging from invigorating juices and smoothies to energizing coffees and soothing teas.
</p>
                                        <a class="button" href="product-list-left-sidebar.php?category=Beverage"><i
                                                class="fa fa-shopping-cart" aria-hidden="true"></i>SHOP NOW</a>
                                    </div>
                                    <div class="image-mask"></div>
                                    <img class="img-responsive" src="img/banner/home3-banner-5.jpg" alt="Banner">
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
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                            <div class="footer-intro">
                                <a class="logo-footer">
                                    <img src="img/logo-3.png" alt="Logo">
                                </a>
                                <p>Discover a wide selection of fresh, organic, and locally sourced products at FreshMart, where quality meets convenience for your everyday shopping needs.</p>
                                <div class="social">
                                    <ul>
                                        <li><a href="#"><i class="zmdi zmdi-facebook"></i></a></li>
                                        <li><a href="#"><i class="zmdi zmdi-twitter"></i></a></li>
                                        <li><a href="#"><i class="zmdi zmdi-dribbble"></i></a></li>
                                        <li><a href="#"><i class="zmdi zmdi-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                            <div class="footer-top">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 footer-left">
                                        <div class="block text">
                                            <h2 class="block-title">Contact Us</h2>

                                            <div class="block-content">
                                                <div class="contact">
                                                    <div class="item d-flex">
                                                        <div>
                                                            <i class="zmdi zmdi-home"></i>
                                                        </div>
                                                        <div>
                                                            <span>348, Jln Tun Razak, Kampung Datuk Keramat, 50400 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur </span>
                                                        </div>
                                                    </div>
                                                    <div class="item d-flex">
                                                        <div>
                                                            <i class="zmdi zmdi-phone-in-talk"></i>
                                                        </div>
                                                        <div>
                                                            <span>012-3456789<br>03-45678990</span>
                                                        </div>
                                                    </div>
                                                    <div class="item d-flex">
                                                        <div>
                                                            <i class="zmdi zmdi-email"></i>
                                                        </div>
                                                        <div>
                                                            <span><a
                                                                    href="mailto:support@domain.com">support@domain.com</a><br><a
                                                                    href="mailto:contact@domain.com">contact@domain.com</a></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 footer-right">
                                        <div class="block text">
                                            <h2 class="block-title">Opening Hours</h2>

                                            <div class="block-content">
                                                <p>Our store at FreshMart is open seven days a week to serve you. Our opening hours are Monday to Friday from 8:00 AM to 8:00 PM, Saturday  from 7:30 AM to 9:30 PM and on Sunday from 7:00 AM to 10:00 PM. We look forward to welcoming you during our operating hours.</p>
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