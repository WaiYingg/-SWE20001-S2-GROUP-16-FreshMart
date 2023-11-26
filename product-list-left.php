<?php
session_start();
include "config.php";
$message = array(); // Initialize an empty array to store error messages

$email = ""; // Default value for email
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}

// Check if the necessary form data is set
if (isset($_POST['product_name']) && isset($_POST['product_price']) && isset($_POST['product_image'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = 1;

    if (isset($_POST['add_to_cart'])) {
        // Check if the user is logged in
        if ($email != "") {
            // User is logged in
            // Check if the product is already in the cart
            $select_cart = mysqli_query($conn, "SELECT * FROM `shopping_cart` WHERE name='$product_name' AND email='$email'") or die('query failed');

            if (mysqli_num_rows($select_cart) > 0) {
                // Product already exists in the cart, so update the quantity
                $fetch_cart = mysqli_fetch_assoc($select_cart);
                $new_quantity = $fetch_cart['quantity'] + 1;
                $update_quantity_query = mysqli_query($conn, "UPDATE `shopping_cart` SET quantity = '$new_quantity' WHERE id = '{$fetch_cart['id']}'");

                if ($update_quantity_query) {
                    $message[] = 'Product quantity updated successfully';
                } else {
                    $message[] = 'Failed to update product quantity';
                }
            } else {
                // Product is not in the cart, so add it
                $insert_product = mysqli_query($conn, "INSERT INTO `shopping_cart`(name,price,image,quantity,email) VALUES('$product_name','$product_price','$product_image','$product_quantity','$email')");
                if ($insert_product) {
                    $message[] = 'Product added to cart successfully';
                } else {
                    $message[] = 'Failed to add product to cart';
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
        if ($email != "") {
            $update_value = $_POST['update_quantity'];
            $update_id = $_POST['update_quantity_id'];
            $update_quantity_query = mysqli_query($conn, "UPDATE `shopping_cart` SET quantity = '$update_value' WHERE id = '$update_id'");
            if ($update_quantity_query) {
                $message[] = 'Product quantity updated successfully';
                header('Location: product-list-left-sidebar.php');
                exit();
            } else {
                $message[] = 'Failed to update product quantity';
            }
        } else {
            // User is not logged in, redirect to the login page
            header('Location: login.php');
            exit();
        }
    }
}

if (isset($_GET['remove'])) {
    // Check if the user is logged in
    if ($email != "") {
        $remove_id = $_GET['remove'];
        mysqli_query($conn, "DELETE FROM `shopping_cart` WHERE id='$remove_id'");
        $message[] = 'Product removed from cart successfully';
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
		<!-- Topbar -->

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
						<div class="block-cart dropdown ms-auto">
						<div class="cart-title">
						<?php 
						$select_rows = mysqli_query($conn, "SELECT * FROM `shopping_cart`  WHERE email='$email'") or die('query failed');
						$row_count = mysqli_num_rows($select_rows);
						?>

						<i class="fa fa-shopping-basket"></i>
						<span class="cart-count"><?php echo $row_count;?></span>
					</div>

<div class="dropdown-content">
	<div class="cart-content">
		<table>
			<tbody>
				<?php
				$select_cart = mysqli_query($conn, "SELECT * FROM `shopping_cart` WHERE email='$email'") or die('query failed');
				$grand_total = 0;
				if (mysqli_num_rows($select_cart) > 0) {
					while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
						?>
						<tr>
							<td class="product-image">
								<a href="product-detail-left-sidebar.html">
									<img src="img/product/<?php echo $fetch_cart['image']; ?>" alt="Product">
								</a>
							</td>
							<td>
								<div class="product-name">
									<a href="product-detail-left-sidebar.html">
										<?php echo $fetch_cart['name']; ?>
									</a>
								</div>
								<div class="product-quantity">
									<form action="" method="POST">
										<input type="hidden" name="update_quantity_id" value="<?php echo $fetch_cart['id']; ?>">
										<input id="form1" min="1" name="update_quantity" value="<?php echo $fetch_cart['quantity']; ?>" type="number" class="form-control form-control-lg text-center" />
										<input type="submit" value="Update" class="btn mt-1" name="update_update_btn">
									</form>
								</div>
								<div>
									<?php echo $fetch_cart['quantity']; ?> x <span class="product-price">$<?php echo number_format($fetch_cart['price']); ?></span>
								</div>
							</td>
							<td class="action">
								<a class="remove" href="product-list-left-sidebar.php?remove=<?php echo $fetch_cart['id']; ?>" onclick="return confirm('Remove item from cart?')">
									<i class="fa fa-trash-o" aria-hidden="true"></i>
								</a>
							</td>
						</tr>
						<?php
						$subtotal = $fetch_cart['price'] * $fetch_cart['quantity'];
						$grand_total += $subtotal;
					}
				} else {
					echo "<tr><td colspan='3'>Cart is empty</td></tr>";
				}
				?>

				<tr class="total">
					<td>Total:</td>
					<td colspan="2">$<?php echo number_format($grand_total); ?></td>
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
							<h2 class="title">Our Product</h2>
							<h3><a href="homepage.html">Home</a></h4>
								<h3><a href="product-list-left-sidebar.html">/Product</a></h4>
						</div>
					</div>
				</div>
			</div>


			<div class="container">
				<div class="row">
					<!-- Sidebar -->
					<div id="left-column" class="sidebar col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<!-- Block - Product Categories -->
						<div class="block product-categories">
							<h3 class="block-title">Categories</h3>
							<?php
							// Retrieve distinct categories from the 'categories' table
							$select_categories = mysqli_query($conn, "SELECT DISTINCT name FROM categories");

							while ($row = mysqli_fetch_assoc($select_categories)) {
								// Process each row of data
								?>
								<div class="block-content">
									<div class="item">
										<a class="category-title"
											href="product-list-left-sidebar.php?category=<?php echo $row['name']; ?>">
											<?php echo $row['name']; ?>
										</a>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>


					<!-- Page Content -->
					<div id="center-column" class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
						<div class="product-category-page">
							<!-- Nav Bar -->
							<div class="products-bar">
								<div class="row">
									<div class="col-md-6 col-xs-6">
										<div class="gridlist-toggle" role="tablist">
											<ul class="nav nav-tabs">
												<li class="active"><a href="#products-grid" data-toggle="tab"
														aria-expanded="true"><i class="fa fa-th-large"></i></a></li>
												<li><a href="#products-list" data-toggle="tab" aria-expanded="false"><i
															class="fa fa-bars"></i></a></li>
											</ul>
										</div>

										<div class="total-products">There are 12 products</div>
									</div>

									<div class="col-md-6 col-xs-6">
										<div class="filter-bar">
											<form action="#" class="pull-right">
												<div class="select">
													<select class="form-control">
														<option value="">Sort By</option>
														<option value="1">Price: Lowest first</option>
														<option value="2">Price: Highest first</option>
														<option value="3">Product Name: A to Z</option>
														<option value="4">Product Name: Z to A</option>
														<option value="5">In stock</option>
													</select>
												</div>
											</form>
											<form action="#" class="pull-right">
												<div class="select">
													<select class="form-control">
														<option value="">Relevance</option>
														<option value="1">Price: Lowest first</option>
														<option value="2">Price: Highest first</option>
														<option value="3">Product Name: A to Z</option>
														<option value="4">Product Name: Z to A</option>
														<option value="5">In stock</option>
													</select>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>

							<!-- Page Content -->
							<div id="center-column" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="product-category-page">
									<div class="tab-content">
										<?php
										 if (isset($message)) {
											foreach ($message as $msg) {
												echo '<div class="msg"><h1>' . $msg . '</h1></div>';
											}}
										?>
										<!-- Products Grid -->
										
										<div class="tab-pane active" id="products-grid">
											<div class="products-block">
												<div class="row">
													<?php
													if (isset($_GET['category'])) {
														$selected_category = $_GET['category'];

														// Retrieve products for the selected category
														$select_products = mysqli_query($conn, "SELECT * FROM `product_db` WHERE category = '$selected_category'");

														if (mysqli_num_rows($select_products) > 0) {
															while ($fetch_product = mysqli_fetch_assoc($select_products)) {
																?>
														<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="hehe">
																	<div class="product-item">
																	<form action="" method="POST">
																		<div class="product-image">
																		<a href="product-detail-left-sidebar.php?id=<?php echo $fetch_product['id'];?>">
																		<img class="img-responsive"
																					src="img/product/<?php echo $fetch_product['image']; ?>"
																					alt="Product Image">
																			</a>
																		</div>

																		<div class="product-title">
																		<a href="product-detail-left-sidebar.php?id=<?php echo $fetch_product['id'];?>">
																				<?php echo $fetch_product['name']; ?>
																			</a>
																		</div>

																		<div class="product-rating">
																			<div class="star on"></div>
																			<div class="star on"></div>
																			<div class="star on "></div>
																			<div class="star on"></div>
																			<div class="star"></div>
																		</div>

																		<div class="product-price">
																			<span class="sale-price">
																				$ <?php echo $fetch_product['price']; ?>
																			</span>
																		</div>

																		<div class="product-buttons">
																			<input type="submit" name="add_to_cart" value="Add To cart" class="btn">
																		</div>
																		<input type="hidden"  name="product_name" value="<?php echo $fetch_product['name']; ?>">
																	<input type="hidden"  name="product_price" value="<?php echo $fetch_product['price']; ?>">
																	<input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
																	</form>
																	</div>
																</div>
																
															<?php
															}
														} else {
															echo '<div class="col-lg-12">';
															echo 'No products found for the selected category.';
															echo '</div>';
														}
													} else {
														// Retrieve all products
														$select_all_products = mysqli_query($conn, "SELECT * FROM `product_db`");
														$select_all_products = mysqli_query($conn, "SELECT * FROM `product_db`");

														if (mysqli_num_rows($select_all_products) > 0) {
															while ($fetch_product = mysqli_fetch_assoc($select_all_products)) {
																?>
															<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="hehe">
																	<div class="product-item">
																	<form action="" method="POST">
																		<div class="product-image">
																		<a href="product-detail-left-sidebar.php?id=<?php echo $fetch_product['id'];?>">																				<img class="img-responsive"
																					src="img/product/<?php echo $fetch_product['image']; ?>"
																					alt="Product Image">
																			</a>
																		</div>
																		<div class="product-title">
																		<a href="product-detail-left-sidebar.php?id=<?php echo $fetch_product['id'];?>">
																				<?php echo $fetch_product['name']; ?>
																			</a>
																		</div>
																		<div class="product-rating">
																			<div class="star on"></div>
																			<div class="star on"></div>
																			<div class="star on "></div>
																			<div class="star on"></div>
																			<div class="star"></div>
																		</div>
																		<div class="product-price">
																			<span class="sale-price">
																				$ <?php echo $fetch_product['price']; ?>
																			</span>
																		</div>
																		<input type="hidden"  name="product_name" value="<?php echo $fetch_product['name']; ?>">
																	<input type="hidden"  name="product_price" value="<?php echo $fetch_product['price']; ?>">
																	<input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
																		<div class="product-buttons">
																			<input type="submit" value="Add To cart" name="add_to_cart" class="btn">
																		</div>
																	</form>
																	</div>
																	</div>
															<?php
															}
														} else {
															echo '<div class="col-lg-12">';
															echo 'No products found.';
															echo '</div>';
														}
													}
													?>
											</div>

										</div>
										</div>

										<!-- Products List -->
										<div class="tab-pane" id="products-list">
											<div class="products-block layout-5">
												<?php
												// Check if a category is selected
												if (isset($_GET['category'])) {
													$selected_category = $_GET['category'];

													// Retrieve products for the selected category
													$select_products = mysqli_query($conn, "SELECT * FROM `product_db` WHERE category = '$selected_category'");

													if (mysqli_num_rows($select_products) > 0) {
														while ($fetch_product = mysqli_fetch_assoc($select_products)) {
															?>
															<div class="product-item">
																<div class="row">
																<form action="" method="POST">
																	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																		<div class="product-image">
																		<a href="product-detail-left-sidebar.php?id=<?php echo $fetch_product['id'];?>">																				<img class="img-responsive"
																					src="img/product/<?php echo $fetch_product['image']; ?>"
																					alt="Product Image">
																			</a>
																		</div>
																	</div>
																	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
																		<div class="product-info">
																		<div class="product-title">
																			<a href="product-detail-left-sidebar.php?id=<?php echo $fetch_product['id'];?>">
																				<?php echo $fetch_product['name']; ?>
																			</a>
																		</div>

																			<div class="product-rating">
																				<div class="star on"></div>
																				<div class="star on"></div>
																				<div class="star on"></div>
																				<div class="star on"></div>
																				<div class="star"></div>
																				<span class="review-count">(3 Reviews)</span>
																			</div>

																			<div class="product-price">
																				<span class="sale-price">
																					$ <?php echo $fetch_product['price']; ?>
																				</span>
																			</div>

																			<!-- <div class="product-stock">
																				<i class="fa fa-check-square-o"
																					aria-hidden="true"></i>In stock
																			</div> -->

																			<div class="product-description">
																			<?php echo $fetch_product['description']; ?>
																			</div>
																			<div class="d-flex flex-row mt-4">
																		<button class="btn btn-link px-4 py-0"
																		  onclick="this.parentNode.querySelector('input[type=number]').stepDown()">
																		  <i class="fas fa-minus"></i>
																		</button>
													
																		<input id="form1" min="0" name="quantity" value="1" type="number"
																		  class="form-control form-control-sm m-2 p-2" style="width: 50px;border-radius:10px;" />
													
																		<button class="btn btn-link px-4 py-0"
																		  onclick="this.parentNode.querySelector('input[type=number]').stepUp()">
																		  <i class="fas fa-plus"></i>
																		</button>
																	  </div>
																	<input type="hidden"  name="product_name" value="<?php echo $fetch_product['name']; ?>">
																	<input type="hidden"  name="product_price" value="<?php echo $fetch_product['price']; ?>">
																	<input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
																	<div class="product-buttons">
																			<input type="submit" name="add_to_cart" value="Add To cart" class="btn">
																		</div>
																		</div>
																</div>
														</form>
																</div>
																</div>
															
														<?php
														}
													} else {
														echo '<div class="col-lg-12">';
														echo 'No products found for the selected category.';
														echo '</div>';
													}
												} else {
													// Retrieve all products
													$select_all_products = mysqli_query($conn, "SELECT * FROM `product_db`");

													if (mysqli_num_rows($select_all_products) > 0) {
														while ($fetch_product = mysqli_fetch_assoc($select_all_products)) {
															?>
												<div class="product-item">
																<div class="row">
																	<form action="" method="POST">
																	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																		<div class="product-image">
																		<a href="product-detail-left-sidebar.php?id=<?php echo $fetch_product['id'];?>">																				<img class="img-responsive"
																					src="img/product/<?php echo $fetch_product['image']; ?>"
																					alt="Product Image">
																			</a>
																		</div>
																	</div>
																	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
																		<div class="product-info">
																		<div class="product-title">
																		<a href="product-detail-left-sidebar.php?id=<?php echo $fetch_product['id'];?>">
																			<?php echo $fetch_product['name']; ?>
																			</a>
																		</div>

																			<div class="product-rating">
																				<div class="star on"></div>
																				<div class="star on"></div>
																				<div class="star on"></div>
																				<div class="star on"></div>
																				<div class="star"></div>
																				<span class="review-count">(3 Reviews)</span>
																			</div>

																			<div class="product-price">
																				<span class="sale-price">
																					$ <?php echo $fetch_product['price']; ?>
																				</span>
																			</div>

																			<!-- <div class="product-stock">
																				<i class="fa fa-check-square-o"
																					aria-hidden="true"></i>In stock
																			</div> -->

																			<div class="product-description">
																			<?php echo $fetch_product['description']; ?>
																			</div>

																			
																			<input type="hidden"  name="product_name" value="<?php echo $fetch_product['name']; ?>">
																	<input type="hidden"  name="product_price" value="<?php echo $fetch_product['price']; ?>">
																	<input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
																	<div class="product-buttons">
																			<input type="submit" name="add_to_cart" value="Add To cart" class="btn">
																		</div>
																		</div>
																		</div>
														</div>
														</form>
														</div>
														<?php
														}
													} else {
														echo '<div class="col-lg-12">';
														echo 'No products found.';
														echo '</div>';
													}
												}
												?>

										</div>


									</div>
								</div>
							</div>
						


							<!-- Pagination Bar -->
							<div class="pagination-bar">
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12">
										<div class="text">Showing 1-12 of 20 item(s)</div>
									</div>

									<div class="col-md-8 col-sm-8 col-xs-12">
										<div class="pagination">
											<ul class="page-list">
												<li><a href="#" class="prev">Previous</a></li>
												<li><a href="#" class="current">1</a></li>
												<li><a href="#">2</a></li>
												<li><a href="#" class="next">Next</a></li>
											</ul>
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
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="block text">
									<div class="block-content">
										<a href="index.html" class="logo-footer">
											<img src="img/logo-2.png" alt="Logo">
										</a>

										<div class="contact">
											<div class="item d-flex">
												<div class="item-left">
													<i class="zmdi zmdi-home"></i>
												</div>
												<div class="item-right">
													<span>123 Suspendis matti, VST District, NY Accums, North
														American</span>
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
									<h2 class="block-title"></h2>

									<div class="block-content">
										<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore
											eu fugiat nulla pariatur. </p>
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
										<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore
											eu fugiat nulla pariatur. </p>
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
											<div class="content">Proin gravida nibh vel velit auctor aliquet. Aenean
												lorem quis bibendum auctor</div>
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
											<div class="content">Proin gravida nibh vel velit auctor aliquet. Aenean
												lorem quis bibendum auctor</div>
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
											<div class="content">Proin gravida nibh vel velit auctor aliquet. Aenean
												lorem quis bibendum auctor</div>
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