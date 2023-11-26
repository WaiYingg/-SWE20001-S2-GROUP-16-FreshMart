<?php
session_start();
include("config.php");


function checkLowStockQuantity($conn)
{
    $select_low_stock = mysqli_query($conn, "SELECT COUNT(*) AS low_stock_count FROM product_db WHERE stock < 10");
    $row = mysqli_fetch_assoc($select_low_stock);
    return $row['low_stock_count'];
}

if (isset($_SESSION['adminemail'])) {
    $logged_in_admin_email = $_SESSION['adminemail'];

    $admin_info_query = "SELECT username, email, password, user_type, age FROM admin WHERE email = '$logged_in_admin_email'";
    $admin_info_result = mysqli_query($conn, $admin_info_query);

    // Check if the query was successful
    if ($admin_info_result) {
        // Fetch the data and store it in an associative array
        $admin_info = mysqli_fetch_assoc($admin_info_result);

        // Use the fetched information as needed
        $username = $admin_info['username'];
        $email = $admin_info['email'];
        $password = $admin_info['password'];
        $user_type = $admin_info['user_type'];
       
        $age = $admin_info['age'];
    } else {
        echo '<div class="alert alert-danger">Failed to fetch driver information. Please try again.</div>';
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


    <head>
        <style>
            .dashboard .title {
                color: #262626;
                font-size: 4.3rem;
                margin-bottom: 2rem;
                font-family: "Playfair Display";
                font-weight: 900;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .small-box {
                font-size: 2rem;
                border: 1px solid #ccc;
                border-radius: 5px;
                margin: 1rem .5rem;
                font-weight: bold;
                margin-bottom: 20px;
                font-family: "Playfair Display";
                text-align: center;
            }

            .box {
                border-radius: 5px;
                padding: 1rem;
                margin: 1rem .5rem;
                font-weight: bold;
                margin-bottom: 20px;
                font-family: "Playfair Display";
                text-align: center;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                /* Increased blur radius */
            }

            .bar-chart {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 10px;
                /* Add other styles as needed */
            }

            .bar {
                min-height: 20px;
                height: auto;
                /* Add other styles as needed */
            }

            .bar-label {
                margin-top: 5px;
                /* Add other styles as needed */
            }

            #sidebar {
                position: fixed;
                width: 200px;
                height: 100vh;
                background-color: #262626;
                color: #fff;
                left: -200px;
                transition: left 0.3s;
                z-index: 1000;
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
            .alert-product-count {
                position: absolute;
                top: 40px;
                right: 4px;
                background-color: #262626;
                color: white;
                border-radius: 50%;
                width: 22px;
                height: 22px;
                font-size: 19px;
                padding: 2px 2px;
                line-height: 18px;
                text-align: center;
                display: flex;
                justify-content: center;
            }
        </style>
    </head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
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


            <div class="col-auto col-md-11 min-vh-100 p-5">
                <div class="col-md-12">
                    <div class="row d-flex justify-content-center g-3 dashboard" id="dashboardFirstRow">
                        <div class="row">
                            <div class="col-md-2">

                            </div>
                            
                            <div class="col-md-8">
                                <h1 class="title text-center">Dashboard</h1><br><br>
                            </div>
                            
                            <div class="col-md-2">
                            <a href="admin-product-page.php" class="circle d-flex align-items-start justify-content-end">
                                <?php
                                // Get the count of low stock products
                                $low_stock_count = checkLowStockQuantity($conn);
                                ?>
                                <h1 class="text-danger" style="font-size:5.5rem;">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span class="alert-product-count">
                                        <?php echo $low_stock_count; ?>
                                    </span>
                                </h1>
                            </a>
                        </div>
                        <!-- Move the sidebar to the left -->
                        <div class="col-md-10">
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
                                </div>
                            </nav>
                        </div>

                        </div>
                        <div class="col-md-4 text-center">
                            <div class="small-box p-5 text-light" style="background-color:#c7c1a5;">
                                <!-- Modified class: small-box -->
                                <div class="d-flex align-items-center justify-content-center">
                                    <span style="font-size: 3.5rem;margin-right:15px;"><i
                                            class="fa-sharp fa-solid fa-file-invoice"></i></span>
                                    <div class="ml-1">
                                        <?php
                                        $total_orders = 0;
                                        $select_orders = $conn->prepare("SELECT COUNT(DISTINCT order_id) AS total_orders FROM `orders1`");
                                        $select_orders->execute();
                                        $result_orders = $select_orders->get_result();
                                        if ($result_orders->num_rows > 0) {
                                            $fetch_orders = $result_orders->fetch_assoc();
                                            $total_orders = $fetch_orders['total_orders'];
                                        }
                                        ?>
                                        <h2>
                                            <?= $total_orders; ?>
                                        </h2> <!-- Modified tag: h4 -->
                                        <p>Total Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4 text-center">
                            <div class="small-box p-5 text-light" style="background-color:#c7c1a5;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span style="font-size: 3.5rem;margin-right:15px;">
                                    <i class="fa-solid fa-sack-dollar"></i></span>
                                    <div class="ml-1">
                                        <?php
                                        $total_this_month_orders = 0;
                                        $current_month = date("Y-m");

                                        $select_orders = $conn->prepare("SELECT COUNT(DISTINCT order_id) AS total_orders FROM `orders1` WHERE DATE_FORMAT(`date`, '%Y-%m') = ?");
                                        $select_orders->bind_param('s', $current_month);
                                        $select_orders->execute();
                                        $result_orders = $select_orders->get_result();
                                        if ($result_orders->num_rows > 0) {
                                            $fetch_orders = $result_orders->fetch_assoc();
                                            $total_this_month_orders = $fetch_orders['total_orders'];
                                        }
                                        ?>
                                        <h2>
                                            <?= ($total_this_month_orders > 0) ? $total_this_month_orders : '0'; ?>
                                        </h2>
                                        <p>Total Orders This Month</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-center">
                            <div class="small-box p-5 text-light" style="background-color:#c7c1a5;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span style="font-size: 3.5rem;margin-right:15px;"><i
                                            class="fa-solid fa-user"></i></span>
                                    <div class="ml-1">
                                        <?php
                                        $total_users = 0;
                                        $user_type = "user";

                                        $select_users = $conn->prepare("SELECT COUNT(*) AS total_users FROM `login` WHERE user_type = ?");
                                        $select_users->bind_param('s', $user_type);
                                        $select_users->execute();
                                        $result_users = $select_users->get_result();
                                        if ($result_users->num_rows > 0) {
                                            $fetch_users = $result_users->fetch_assoc();
                                            $total_users = $fetch_users['total_users'];
                                        }
                                        ?>
                                        <h2 class="firstRowNumber" style="font-size: 2rem; font-weight: 900;">
                                            <?= ($total_users > 0) ? $total_users : '0'; ?>
                                        </h2>
                                        <p>Total Users</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                    <br>
                    <div class="row d-flex justify-content-center align-items-center g-2 mt-5">
                        <div class="col-md-6">
                            <div class="col-md-6">
                                <div class="box text-center" style="height:200px;padding-top:4rem;font-size:2rem;">
                                    <?php
                                    $total_today_orders = 0;
                                    $current_date = date("Y-m-d");

                                    $select_orders = $conn->prepare("SELECT COUNT(DISTINCT order_id) AS total_orders FROM `orders1` WHERE `date` = ?");
                                    $select_orders->bind_param('s', $current_date);
                                    $select_orders->execute();
                                    $result_orders = $select_orders->get_result();
                                    if ($result_orders->num_rows > 0) {
                                        $fetch_orders = $result_orders->fetch_assoc();
                                        $total_today_orders = $fetch_orders['total_orders'];
                                    }
                                    ?>
                                    <h3>
                                        <?= ($total_today_orders > 0) ? $total_today_orders : '0'; ?>
                                    </h3>
                                    <p>Total Orders Today</p>
                                    <a href="admin_order.php" class="btn" style="background-color:#78b144;padding:1rem 2rem;">See orders</a>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="box text-center" style="height:200px;padding-top:4rem;font-size:2rem;">
                                    <?php
                                    $total_pending_orders = 0;
                                    $order_status = "pending";
                                    $select_orders = $conn->prepare("SELECT COUNT(DISTINCT order_id) AS total_orders FROM `orders1` WHERE order_status = ?");
                                    $select_orders->bind_param('s', $order_status);
                                    $select_orders->execute();
                                    $result_orders = $select_orders->get_result();
                                    if ($result_orders->num_rows > 0) {
                                        $fetch_orders = $result_orders->fetch_assoc();
                                        $total_pending_orders = $fetch_orders['total_orders'];
                                    }
                                    ?>
                                    <h3>
                                        <?= $total_pending_orders; ?>
                                    </h3>
                                    <p>Total Pending Orders</p>
                                    <a href="admin_order.php" class="btn" style="background-color:#78b144;padding:1rem 2rem;">See orders</a>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="box text-center" style="height:200px;padding-top:4rem;font-size:2rem;">

                                    <?php
                                    $total_products = 0;
                                    $select_products = $conn->query("SELECT COUNT(*) as total_products FROM `product_db`");
                                    if ($select_products->num_rows > 0) {
                                        $fetch_products = $select_products->fetch_assoc();
                                        $total_products = $fetch_products['total_products'];
                                    }
                                    ?>
                                    <h3>
                                        <?= $total_products; ?>
                                    </h3>
                                    <p>Total Products</p>
                                    <a href="admin-product-page.php" class="btn" style="background-color:#78b144;padding:1rem 2rem;">Manage products</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box text-center" style="height:200px;padding-top:4rem;font-size:2rem;">
                                    <?php
                                    $total_messages = 0;
                                    $select_messages = $conn->query("SELECT COUNT(*) as total_messages FROM `contact`");
                                    if ($select_messages->num_rows > 0) {
                                        $fetch_messages = $select_messages->fetch_assoc();
                                        $total_messages = $fetch_messages['total_messages'];
                                    }
                                    ?>
                                    <h3>
                                        <?= $total_messages; ?>
                                    </h3>
                                    <p>Total Messages</p>
                                    <a href="admin_contacts.php" class="btn" style="background-color:#78b144;padding:1rem 2rem;">View messages</a>
                                </div>

                            </div>

                        </div>


                        <div class="col-md-6">
                            <div class="col-md-12">
                                <div class="box text-center d-flex align-items-center justify-content-center"
                                    style="height: 430px;">
                                    <div class="bar-chart d-flex flex-column-reverse">
                                        <?php
                                        $select_products = $conn->query("SELECT name, COUNT(*) as count FROM `orders1` GROUP BY name ORDER BY count DESC LIMIT 5");
                                        $total_products = 0;
                                        $max_count = 0;
                                        while ($product = $select_products->fetch_assoc()) {
                                            $count = $product['count'];
                                            $max_count = max($max_count, $count);
                                        }
                                        mysqli_data_seek($select_products, 0); // Reset the result pointer to the beginning
                                        while ($product = $select_products->fetch_assoc()) {
                                            $name = $product['name'];
                                            $count = $product['count'];
                                            $percentage = round(($count / $total_this_month_orders) * 100);
                                            $color = '#' . substr(md5(rand()), 0, 6);
                                            echo '<div class="bar" style="width: ' . $percentage . '%; background-color: ' . $color . ';"></div>';
                                            echo '<div class="bar-label">' . $name . ' (' . $percentage . '%)</div>';
                                        }
                                        ?>
                                        <p style="margin-bottom: 20px;font-size:2rem;">The most popular product</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>



        </div>

    </div>














</body>






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