<?php
session_start();
include "config.php";
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
if (isset($_POST['pending-order'])) {
    $order_status = 'pending';
} elseif (isset($_POST['completed-order'])) {
    $order_status = 'completed';
} else {
    $order_status = ''; // Show all orders by default
}

// Update order status when the "Update" button is pressed
if (isset($_POST['update_order'])) {
    $new_order_status = $_POST['order_status'];
    $order_id = $_POST['order_id'];

    // Perform the update query to change the order status
    $update_query = "UPDATE `orders1` SET order_status = '$new_order_status' WHERE order_id = '$order_id'";
    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
        echo '<div class="alert alert-success">Order status updated successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Failed to update order status. Please try again.</div>';
    }
}
if (isset($_POST['assign_driver'])) {
    
    $assigned_driver = $_POST['assigned_driver'];
    $order_id = $_POST['order_id'];

    // Update the "Driver" column in the "orders1" table with the assigned driver's name
    $update_driver_query = "UPDATE `orders1` SET Driver = '$assigned_driver' WHERE order_id = '$order_id'";
    $update_driver_result = mysqli_query($conn, $update_driver_query);

    if ($update_driver_result) {
        echo '<div class="alert alert-success">Driver assigned successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Failed to assign driver. Please try again.</div>';
    }
}


$select_orders = mysqli_query($conn, "SELECT DISTINCT order_id FROM `orders1` WHERE order_status='$order_status' ORDER BY order_id") or die('query failed');
$order_number = 1; // Initialize the order number

while ($fetch_order = mysqli_fetch_assoc($select_orders)) {
    // Remaining code remains the same
    // ...
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
<style>
    body {
        font-family: "Playfair Display";
        font-size: 14px;
    }


    .dropdown-item {
        color: #ffffff;
        /* Text color */
        font-weight: bold;
        /* Bold text */
        display: flex;
        justify-content: center;
        font-family: "Playfair Display";
        font-size: 1.8rem;
        opacity: 1;
        /* Fully visible */
        transition: background-color 0.3s, opacity 0.5s ease-in-out;
        /* Transition for background color and opacity */

    }

    .dropdown:hover>.dropdown-menu {
        display: block;
        padding: 1rem 1rem;
    }

    .dropdown>.dropdown-toggle:active {
        /*Without this, clicking will make it sticky*/
        pointer-events: none;
    }
</style>

<br><br>
<div class="container-fluid">
    <div class="row">
        <div class="col-auto col-md-1 min-vh-100" style="background-color:#78b144;">
            <div class="mt-5" style="background-color:#78b144;">
                <ul class="nav nav-pills flex-column" style="font-family:Playfair Display;">
                    <li class="nav-item pt-5">
                        <a href="admin_page.php" class="nav-link text-white">
                            <i class="fa-solid fa-house d-inline d-lg-none" style="margin-right:1rem;"></i>
                            <span class="fs-2 d-none d-lg-inline">Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin-product-page.php" class="nav-link text-white">
                            <i class="fa-brands fa-product-hunt d-inline d-lg-none" style="margin-right:.8rem;"></i>
                            <span class="fs-2 d-none d-lg-inline">Product</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_order.php" class="nav-link text-white" aria-current="page">
                            <i class="fa-solid fa-receipt d-inline d-lg-none" style="margin-right:1.5rem;"></i>
                            <span class="fs-2 d-none d-lg-inline">Order</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="admin_contacts.php" class="nav-link text-white" aria-current="page">
                            <i class="fa-solid fa-phone d-inline d-lg-none" style="margin-right:1rem;"></i>
                            <span class="fs-2 d-none d-lg-inline">Contact</span>
                        </a>
                    </li>
                    <hr>
                    <li class="nav-item dropdown"> <!-- Add the 'dropdown' class here -->
                <a href="adminlogin.php" class="dropdown-toggle-no-caret text-white" id="dropdownMenuButton"
                    data-mdb-toggle="dropdown" aria-expanded="false"
                    style="font-weight:700;background-color:#767474;border-radius:50px;">
                    <i class="fa-solid fa-user d-inline d-lg-none" style="margin-right:1rem;"></i>
                    <span class="fs-2 d-none d-lg-inline">Profile</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?php
                    if (isset($_SESSION['adminemail'])) {
                        // User is logged in, show logout button
                        echo '
                        
                        <li><a class="dropdown-item" href="adminlogout.php" title="Logout">Logout</a></li>
                        ';
                    } else {
                        // User is not logged in, show login and register links
                        echo '
                        <li><a class="dropdown-item" href="adminlogin.php" title="Login">Login</a></li>
                        <li><a class="dropdown-item" href="admin_register.php" title="Register">Register</a></li>
                        ';
                    }
                    ?>
                </ul>
            </li>
                </ul>
            </div>
        </div>

        <div class="col-auto col-md-11">
            <div class="page-cart">
                <div class="row justify-content-center">
                    <div class="block-title mt-5" style="color: #FF7F50;">
                        <h2 class="title" style="color: #78b144;"><span>All user orders</span></h2>
                    </div>

                    <div class="order-buttons text-center" style="margin-bottom: 20px;">
                        <form method="POST">
                            <input type="submit" value="Pending Orders" name="pending-order" class="btn"
                                style="margin: 20px;background-color:grey;padding:1.5rem 2rem;border-radius:50px;">
                            <input type="submit" value="Completed Orders" name="completed-order" class="btn"
                                style="background-color:#90EE90;margin: 20px;padding:1.5rem 2rem;border-radius:50px;">
                        </form>
                    </div>
                    <div class="row">
                        <?php
                        if (isset($_POST['pending-order'])) {
                            $order_status = 'pending';
                        } elseif (isset($_POST['completed-order'])) {
                            $order_status = 'completed';
                        } else {
                            $order_status = 'pending'; // Show pending orders by default
                        }

                        $select_orders = mysqli_query($conn, "SELECT DISTINCT order_id FROM `orders1` WHERE order_status='$order_status' ORDER BY order_id") or die('query failed');
                        $order_number = 1; // Initialize the order number
                        
                        while ($fetch_order = mysqli_fetch_assoc($select_orders)) {
                            $order_id = $fetch_order['order_id'];
                            $select_order_items = mysqli_query($conn, "SELECT * FROM `orders1` WHERE order_id='$order_id'") or die('query failed');
                            $current_order_total = 0;
                            $shipping_fee = 0;
                            $total_weight = 0;

                            if (mysqli_num_rows($select_order_items) > 0) {
                                ?>
                                <div class="col-md-4">
                                    <div class="card p-3 mb-4">
                                        <div class="card-body">
                                            <div class="card-title product-price text-center">
                                                <h1 style="font-weight:800;">Order
                                                    <?php echo $order_number; ?>
                                                </h1>
                                            </div>
                                            <?php $order_number++; ?>
                                            <?php
                                            // Fetch and display the shipping address for the current order
                                            $fetch_order_item = mysqli_fetch_assoc($select_order_items);
                                            ?>
                                            <p class="card-text">Shipping Address: <br>
                                                <?php echo $fetch_order_item['last_name'] . ' ' . $fetch_order_item['first_name']; ?><br>
                                                (+60)
                                                <?php echo $fetch_order_item['contact_number']; ?><br>
                                                <?php echo $fetch_order_item['address'] . ', ' . $fetch_order_item['city'] . ', ' . $fetch_order_item['state'] . ', ' . $fetch_order_item['postcode']; ?>
                                            </p>
                                            <hr>
                                            <div class="order-items">
                                                <?php
                                                do {
                                                    $product_id = $fetch_order_item['product_id'];
                                                    $product_weight = floatval(getProductWeight($conn, $product_id)); // Convert to float
                                                    $total_weight += $product_weight * $fetch_order_item['quantity'];
                                                    ?>
                                                    <div class="row g-0">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span>
                                                                <img width="100" alt="Product Image" class="img-responsive"
                                                                    src="img/product/<?php echo $fetch_order_item['image']; ?>"
                                                                    style="width:150px;"></span>

                                                            <span>
                                                                <p class="card-text">
                                                                    <?php echo $fetch_order_item['name']; ?>
                                                                </p>
                                                                <p class="card-text">Unit price:
                                                                    <?php echo number_format($fetch_order_item['price']); ?>
                                                                </p>
                                                                <p class="card-text">Quantity:
                                                                    <?php echo $fetch_order_item['quantity']; ?>
                                                                </p>
                                                                <p class="card-text">Total:
                                                                    <?php echo number_format($fetch_order_item['price'] * $fetch_order_item['quantity']); ?>
                                                                </p>
                                                            </span>
                                                        </div>

                                                    </div>
                                                    <?php
                                                    $current_order_total += $fetch_order_item['price'] * $fetch_order_item['quantity'];
                                                } while ($fetch_order_item = mysqli_fetch_assoc($select_order_items));

                                                // Calculate shipping fee based on total weight
                                                if ($total_weight <= 5) {
                                                    $shipping_fee = 10;
                                                } elseif ($total_weight > 5 && $total_weight <= 10) {
                                                    $shipping_fee = 15;
                                                } else {
                                                    $shipping_fee = 20;
                                                }

                                                // Display the current order's subtotal row
                                                ?>
                                                <hr>
                                                <div class="card-body">
                                                    <p class="card-text text-right">Subtotal: $
                                                        <?php echo number_format($current_order_total, 2); ?>
                                                    </p>
                                                </div>

                                                <!-- Display the current order's shipping fee row -->
                                                <div class="card-body">
                                                    <p class="card-text text-right">Shipping: $
                                                        <?php echo number_format($shipping_fee, 2); ?>
                                                    </p>
                                                </div>
                                                <?php
                                                // Calculate and display the current order's total row
                                                $order_total = $current_order_total + $shipping_fee;
                                                ?>
                                                <div class="card-body">
                                                    <p class="card-text text-right">Total: $
                                                        <?php echo number_format($order_total, 2); ?>
                                                    </p>
                                                </div>

                                                <?php
                                                // Display the order status line and update button for each order
                                                ?>
                                                <hr>
                                                <div class="card-body text-center">
                                                    <?php if ($order_status == 'completed'): ?>
                                                        <span class="text-center"
                                                            style="background-color:#90EE90;padding:1rem 10rem;border-radius:50px;">Order
                                                            Status: Completed</span>
                                                    <?php else: ?>
                                                        <form method="POST">
                                                            <div class="form-group row">
                                                                <label class="col-sm-4 col-form-label text-left">Order
                                                                    Status:</label>
                                                                <div class="col-sm-12">
                                                                    <select class="form-control form-control-lg custom-select"
                                                                        name="order_status" style="border-radius:50px;">
                                                                        <option value="pending" <?php echo ($order_status == 'pending') ? 'selected' : ''; ?>>Pending
                                                                        </option>
                                                                        <option value="completed">Completed</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <input type="hidden" name="order_id"
                                                                    value="<?php echo $order_id; ?>">
                                                                <button type="submit" name="update_order" class="btn"
                                                                    style="background-color:#78b144;padding:1.5rem 2rem;border-radius:50px;">Update</button>
                                                            </div>
                                                        </form>
                                                        <!-- NEW ADDED start ; NOTE: have to display which driver was assigned when order completed-->
                                                        <form class="delivery-form" method="POST">
                                                            <div class="form-group row">
                                                                <label class="col-sm-4 col-form-label text-left">Driver:</label>
                                                                <div class="col-sm-12">
                                                                    <select name="assigned_driver"
                                                                        class="form-control form-control-lg custom-select"
                                                                        style="border-radius:50px;">
                                                                        <?php
                                                                        // Query to fetch driver usernames from the login table with user_type=driver
                                                                        $sql = "SELECT username FROM driver WHERE user_type='driver'";
                                                                        $result = mysqli_query($conn, $sql);

                                                                        // Loop through the results and populate the dropdown
                                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                                            $selected = ($row['username'] == $fetch_order['assigned_driver']) ? 'selected' : '';
                                                                            echo '<option value="' . $row['username'] . '" ' . $selected . '>' . $row['username'] . '</option>';
                                                                        }
                                                                        // Inside the while loop where you populate the dropdown
                                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                                            $selected = ($row['username'] == $assigned_driver) ? 'selected' : '';
                                                                            echo '<option value="' . $row['username'] . '" ' . $selected . '>' . $row['username'] . '</option>';
                                                                            // Add the following line for debugging
                                                                            echo 'Assigned Driver: ' . $assigned_driver . ', Current Driver: ' . $row['username'] . '<br>';
                                                                        }

                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <!-- NOTE : If want to make the update button to be 2in1 also can~ -->
                                                            <div class="text-right">
                                                                <input type="hidden" name="order_id"
                                                                    value="<?php echo $order_id; ?>">
                                                                <button type="submit" name="assign_driver" class="btn"
                                                                    style="background-color:#78b144;padding:1.5rem 2rem;border-radius:50px;">Assign</button>
                                                            </div>
                                                        </form>
                                                        <!-- NEW ADDED end -->

                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                // Close the row after every 3 columns
                                if ($order_number % 3 == 1) {
                                    echo '</div><div class="row">';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Go Up button -->
<div class="go-up">
    <a href="#">
        <i class="fa fa-long-arrow-up"></i>
    </a>
</div>

<!-- Page Loader -->
<!-- <div id="page-preloader">
        <div class="page-loading">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div> -->
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