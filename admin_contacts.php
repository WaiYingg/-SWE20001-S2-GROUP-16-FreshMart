<?php
session_start();
include("config.php");


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
    <link rel="stylesheet" href="css/admin.css">


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
</head>

<body>
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
    <!-- Main Content -->
    <div id="content" class="site-content">
        <div class="block-title" style="color:#78b144;">
            <h1 class="title mt-5" style="color:#78b144;font-weight:900;">User Message</h1>
        </div>
        <div class="row g-4">
            <?php
            $select_message = $conn->prepare("SELECT * FROM `contact`");
            $select_message->execute();
            $result = $select_message->get_result();
            if ($result->num_rows > 0) {
                while ($fetch_message = $result->fetch_assoc()) {
            ?>
                <div class="col-md-4 p-5">
                    <div class="card p-5" style>
                        <div class="text-center" style="background-color:#78b144;padding:.7rem 5rem;border-radius:50px;color:#fff;">
                            <p class="mt-2">Subject: <span><?= $fetch_message['subject']; ?></span></p>
                        </div>
                        <hr>
                        <div class="card-body">
                            <p>Image:</p>
                            <span><img src="<?= $fetch_message['image']; ?>" alt="Message Image" style="width: 150px; height: auto;"></span>
                            <hr class="mb-2">
                            <p>Name: <span><?= $fetch_message['name']; ?></span></p>
                            <p>Email: <span><?= $fetch_message['email']; ?></span></p>
                            <p>Content: <span><?= $fetch_message['content']; ?></span></p>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo '<p class="empty">You have no messages!</p>';
            }
            ?>
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
