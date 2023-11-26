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

// Initialize $username to avoid undefined variable warning
$username = '';

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

// Fetch logged-in driver information based on the driver's email
if (isset($_SESSION['driveremail'])) {
    $logged_in_driver_email = $_SESSION['driveremail'];

    $driver_info_query = "SELECT username, email, password, user_type, Carplate, age FROM driver WHERE email = '$logged_in_driver_email'";
    $driver_info_result = mysqli_query($conn, $driver_info_query);

    // Check if the query was successful
    if ($driver_info_result) {
        // Fetch the data and store it in an associative array
        $driver_info = mysqli_fetch_assoc($driver_info_result);

        // Use the fetched information as needed
        $username = $driver_info['username'];
        $email = $driver_info['email'];
        $password = $driver_info['password'];
        $user_type = $driver_info['user_type'];
        $Carplate = $driver_info['Carplate'];
        $age = $driver_info['age'];
    } else {
        echo '<div class="alert alert-danger">Failed to fetch driver information. Please try again.</div>';
    }
}

$select_orders = mysqli_query($conn, "SELECT DISTINCT order_id FROM `orders1` WHERE order_status='$order_status' AND Driver = '$username' ORDER BY order_id") or die('query failed');
$order_number = 1; // Initialize the order number

while ($fetch_order = mysqli_fetch_assoc($select_orders)) {
    // Remaining code remains the same
    // ...

}
?>


<!DOCTYPE html>
<html lang="zxx">

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Driver Page</title>

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

    <!-- MDB -->
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" />

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
    <link rel="stylesheet" href="css/admin.css">

    <style>
        body {
            background-color: #fbfbfb;
        }

        @media (min-width: 991.98px) {
            main {
                padding-left: 240px;
            }
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding: 58px 0 0;
            /* Height of navbar */
            box-shadow: 0 2px 5px 0 rgb(0 0 0 / 5%), 0 2px 10px 0 rgb(0 0 0 / 5%);
            width: 240px;
            z-index: 600;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                width: 100%;
            }
        }

        .sidebar .active {
            border-radius: 5px;
            box-shadow: 0 2px 5px 0 rgb(0 0 0 / 16%), 0 2px 10px 0 rgb(0 0 0 / 12%);
            background-color: #FF7F50;
        }
    </style>
</head>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="collapse d-lg-block sidebar bg-white">
            <div class="list-group list-group-flush mx-3 mt-4">
            <!-- Inside the <div class="list-group list-group-flush mx-3 mt-4"> where you display driver information in the sidebar -->
            <a class="list-group-item list-group-item-action py-2 ripple">
                <i class="fas fa-chart-area fa-fw me-3"></i><span>Name: <?php echo isset($username) ? $username : ''; ?></span>
            </a>

            <a class="list-group-item list-group-item-action py-2 ripple">
                <i class="fas fa-chart-area fa-fw me-3"></i><span>Age: <?php echo isset($age) ? $age : ''; ?></span>
            </a>

            <a class="list-group-item list-group-item-action py-2 ripple">
                <i class="fas fa-chart-area fa-fw me-3"></i><span>Email: <?php echo isset($email) ? $email : ''; ?></span>
            </a>

            <a class="list-group-item list-group-item-action py-2 ripple">
                <i class="fas fa-chart-area fa-fw me-3"></i><span>Car Plate: <?php echo isset($Carplate) ? $Carplate : ''; ?></span>
            </a>



        </div>
        
        </nav>
            <!-- Sidebar -->

            <!-- Navbar -->
            <nav id="main-navbar" class="navbar navbar-expand-lg fixed-top">
                <!-- Container wrapper -->
                <div class="container-fluid">
                    <!-- Toggle button -->
                    <div class="row">
                        <div class="col-md-2" style="right:22rem;">
                            <button class="navbar-toggler" type="button" data-mdb-toggle="collapse"
                                data-mdb-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
                                aria-label="Toggle navigation">
                                <i class="fas fa-bars" style="font-size:2rem;"></i>
                            </button>
                        </div>

                    </div>
                </div>
                <!-- Container wrapper -->
            </nav>
            <!-- Navbar -->

        </div>
        <!-- Display delivery details here -->
        <div class="col-md-10 justify-content-center align-items-center mx-auto">
        <div class="block-title" style="color:#78b144;">
                <h1 class="title" style="color:#78b144;"><span>Driver Page</span>
                </h1>
            </div>
        <div class="col-auto col-md-11">
            <div class="page-cart">
                <div class="row justify-content-center">

                    <div class="col-12 order-buttons text-center" style="margin-bottom: 20px; margin-left: 100px;">
                        <form method="POST">
                            <input type="submit" value="Pending Orders" name="pending-order" class="btn"
                                style="margin: 10px;background-color:grey;padding:1.5rem 2rem;border-radius:50px;">
                            <input type="submit" value="Completed Orders" name="completed-order" class="btn"
                                style="background-color:#90EE90;margin: 10px;padding:1.5rem 2rem;border-radius:50px;">
                           
                            
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

                        $select_orders = mysqli_query($conn, "SELECT DISTINCT order_id FROM `orders1` WHERE order_status='$order_status'AND Driver = '$username' ORDER BY order_id") or die('query failed');
                        $order_number = 1; // Initialize the order number
                        
                        while ($fetch_order = mysqli_fetch_assoc($select_orders)) {
                            $order_id = $fetch_order['order_id'];
                            $select_order_items = mysqli_query($conn, "SELECT * FROM `orders1` WHERE order_id='$order_id'AND Driver = '$username'") or die('query failed');
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
                                                
                                                
            

                                                <?php
                                                // Display the order status line and update button for each order
                                                ?>
                                                <hr>
                                                <div class="card-body text-center">
                                                    <?php if ($order_status == 'completed'): ?>
                                                        <span class="text-center"
                                                            style="background-color:#90EE90;padding:0.5rem 1rem;border-radius:50px;">Order
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