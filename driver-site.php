<?php
session_start(); // Start the session
// Include the configuration file
include("config.php");

// Check if 'email' session variable is set, and handle it separately
if (isset($_SESSION['email'])) {
    // 'email' key exists in the $_SESSION array, so you can safely access it
    $email = $_SESSION['email'];
    
} else {
    // Only set a default value if the session email is not set
    $email = ""; // Set it to an empty string or handle it as needed
}

// Check if the user has submitted the login form
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    // Sanitize user input to prevent SQL injection (you should use prepared statements)
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Perform the query to fetch user data using a prepared statement
    $stmt = $conn->prepare("SELECT username, email, age, Carplate FROM driver WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        // Authentication successful
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['age'] = $row['age'];
        $_SESSION['Carplate'] = $row['Carplate'];

        // Redirect to a dashboard or another page after a successful login
        header("Location: dashboard.php");
        exit();
    } else {
        // Authentication failed, handle it accordingly
        header("Location: login.php?error=1"); // Add an error parameter for handling errors
        exit();
    }
}
?>

<!-- Rest of your HTML and PHP code -->


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
            <a class="list-group-item list-group-item-action py-2 ripple">
                    <i class="fas fa-chart-area fa-fw me-3"></i><span>Name: <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?></span>
            </a>

            <a class="list-group-item list-group-item-action py-2 ripple">
                    <i class="fas fa-chart-area fa-fw me-3"></i><span>Age: <?php echo isset($_SESSION['age']) ? $_SESSION['age'] : ''; ?></span>
            </a>

            <a class="list-group-item list-group-item-action py-2 ripple">
                    <i class="fas fa-chart-area fa-fw me-3"></i><span>Car Plate: <?php echo isset($_SESSION['Carplate']) ? $_SESSION['Carplate'] : ''; ?></span>
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
            <div class="block-title" style="color:#FF7F50;">
                <h1 class="title" style="color:#FF7F50; top:10%"><span>Driver Page</span>
                </h1>
            </div>
            <div class="col-md-12 px-5">
                <div class="card p-3 m-5">
                    <div class="card-body">
                        <div class="delivery-details" id="delivery-details">
                            <div class="form-group">
                                <div class="col-md-12 store-address">

                                    <div class="col-md-11">
                                        <p class="store-address"><i class="fa-regular fa-circle m-3"
                                                style="color:#FF7F50;"></i>Damen Mall</p>
                                    </div>
                                    <div class="col-md-1">

                                    </div>

                                </div>
                                <!-- <div class="col-md-12 stop">
                                <p class="stop-address"><i class="fa-solid fa-circle m-3" style="color:#FF7F50;"></i>SS15 Subang Jaya hahahah hahahah hahaha hahahah haha testibgggg testttttttttttttttttttttt</p>
                                </div> -->
                                <div class="col-md-12 destination">
                                    <div class="col-md-11">
                                        <p class="destination-address"><i class="fa-solid fa-location-dot m-3"
                                                style="color:#FF7F50;"></i>SS15 Subang Jaya,3, Jalan SS 15/8, Ss 15,
                                            47500 Subang Jaya, Selangor</p>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="customerDetails bg-white" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" style="border: none;">
                                            <span><i class="fa-solid fa-ellipsis mt-3"></i></span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mx-auto justify-content-center align-items-center p-4">
                <!-- Buttons for driver actions -->
                <section id="driverActions">
                    <div class="form-group text-center">
                        <input type="submit" class="btn" style="background-color:#FF7F50;padding:1rem 15rem;"
                            name="delivered" value="DELIVERED">
                    </div>
                </section>
            </div>

            <div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="exampleModalLabel">Customer Details</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="bottom:6%;">
                            <!-- Add your customer details here -->
                            <div class="row">
                                <div class="col-md-12 me-auto">
                                    <p class="">Name: </p>
                                    <p  class="">Email: </p>
                                    <p  class="">Address : </p>
                                    <p  class="">other: </p>
                                    <p  class="">other: </p>
                                    <p  class="">other : </p>
                                </div>
                            </div>

                            <!-- further details -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add your JavaScript for real-time updates here -->
<script src="script.js"></script>
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
<!-- MDB -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
</body>

</html>