<?php
session_start();
include "config.php";
$message = array(); // Initialize an empty array to store error messages

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM product_db WHERE id = $id");
    header('location:admin-product-page.php');
};

if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_stock = $_POST['product_stock'];
    $product_feature = $_POST['featured'];
    $product_weight = $_POST['product_weight'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_category = $_POST['product_category'];
    $product_image_folder = 'img/product/' . $product_image;


    $product_stock = intval($_POST['product_stock']);

    // For product cart page
    $_SESSION['product_weight'] = $product_weight;

    if (empty($product_name) || empty($product_price) || empty($product_image) || empty($product_description) || empty($product_stock)) {
        // Add an error message if any field is empty
        $message[] = 'Please fill out all fields';
    } else {
        // Check if the product category already exists in the 'categories' table using prepared statement
        $check_category_stmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE name = ?");
        mysqli_stmt_bind_param($check_category_stmt, "s", $product_category);
        mysqli_stmt_execute($check_category_stmt);
        $check_category_result = mysqli_stmt_get_result($check_category_stmt);

        if (mysqli_num_rows($check_category_result) == 0) {
            // Insert the product category into the 'categories' table if it doesn't exist
            $insert_category_stmt = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
            mysqli_stmt_bind_param($insert_category_stmt, "s", $product_category);
            mysqli_stmt_execute($insert_category_stmt);
        }

        // Insert the new product into 'product_db' table using prepared statement
        $insert_product_stmt = mysqli_prepare($conn, "INSERT INTO product_db (name, price, image, category, description, featured, stock, product_weight) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($insert_product_stmt, "ssssssdi", $product_name, $product_price, $product_image, $product_category, $product_description, $product_feature, $product_stock, $product_weight);

        $upload = mysqli_stmt_execute($insert_product_stmt);

        if ($upload) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
            $message[] = 'New product added successfully';
        } else {
            $message[] = 'New product could not be added';
        }

    }
}

// Step 1: Fetch the unique order_ids from orders1 table
$unique_order_ids_sql = "SELECT DISTINCT order_id,product_id FROM orders1 WHERE order_status = 'completed'";
$unique_order_ids_result = $conn->query($unique_order_ids_sql);

if ($unique_order_ids_result->num_rows > 0) {
    while ($unique_order_ids_row = $unique_order_ids_result->fetch_assoc()) {
        $order_id = $unique_order_ids_row['order_id'];

        // Check if order_id has already been processed
        $is_processed_sql = "SELECT COUNT(*) as count FROM processed_orders WHERE order_id = $order_id";
        $is_processed_result = $conn->query($is_processed_sql);
        $is_processed_row = $is_processed_result->fetch_assoc();
        $is_processed = $is_processed_row['count'];

        if ($is_processed == 0) {
            // Fetch the relevant data for the unique order_id from orders1 and product_db tables
            $sql = "SELECT o.product_id, SUM(o.quantity) as total_quantity
                    FROM orders1 o
                    WHERE o.order_status = 'completed' AND o.order_id = $order_id
                    GROUP BY o.product_id";

            $result = $conn->query($sql);

            // Step 2: Calculate the new stock for each product and update product_db table
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $product_id = $row['product_id'];
                    $quantity_purchased = $row['total_quantity'];

                    // Fetch the current stock from the product_db table
                    $select_stock_sql = "SELECT stock FROM product_db WHERE id = $product_id";
                    $stock_result = $conn->query($select_stock_sql);

                    if ($stock_result->num_rows > 0) {
                        $stock_row = $stock_result->fetch_assoc();
                        $current_stock = $stock_row['stock'];

                        // Calculate the new stock, ensuring it doesn't go negative
                        $new_stock = max(0, $current_stock - $quantity_purchased);

                        // Update the product_db table with the new stock
                        $update_stock_sql = "UPDATE product_db SET stock = $new_stock WHERE id = $product_id";
                        $conn->query($update_stock_sql);
                    }
                }

                // Mark the order_id as processed
                $mark_processed_sql = "INSERT INTO processed_orders (order_id) VALUES ($order_id)";
                $conn->query($mark_processed_sql);
            }
        }
    }
}

// Function to check low stock quantity and display an alertfunction checkLowStockQuantity($conn)
function checkLowStockQuantity($conn)
{
    $low_stock_message = '';
    $select_low_stock = mysqli_query($conn, "SELECT id, name, stock FROM product_db WHERE stock < 10");
    if (mysqli_num_rows($select_low_stock) > 0) {
        while ($row = mysqli_fetch_assoc($select_low_stock)) {
            $low_stock_message .= "Product '{$row['name']}' (ID: {$row['id']}) is running low on stock. Current stock: {$row['stock']}.\n";
        }

        echo '
        <script>
            $(document).ready(function() {
                $("#lowStockModal").modal("show");
            });
        </script>';
    }
}
// Check for products with low stock (stock < 10)
$stmt = $conn->prepare("SELECT id, name, stock FROM product_db WHERE stock < 10");
$stmt->execute();
$result = $stmt->get_result();

// Handle search query
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM product_db WHERE name LIKE ?");
    $searchQuery = '%' . $searchQuery . '%';
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $searchResult = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Update</title>
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
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>
    <div class="flash_message success text-center mx-auto" style="font-family: Arial;">
        <?php
        // Call the checkLowStockQuantity function
        if (isset($message)) {
            foreach ($message as $msg) {
                echo '<h2>' . $msg . '</h2>';
            }
        }
        ?>
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
            width: 40%;
            /* You can adjust this width as needed */
            padding: 1em;
        }

        .flash_message h2 {
            padding: 1rem 1rem;
            background-color: grey;
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

            .form-search {
        margin-top: 10px;
    }

    .form-input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 250px;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
    }

    .btn-primary:hover {
        background-color: #0069d9;
        cursor: pointer;
    }

    .fa.fa-search {
        color: #C0C0C0;
        font-size: 16px;
        margin-left: 6px;
        border: none;
    }

    .message-icon {
        font-size: 4rem;
        color: red;
        position: relative;
        display: inline-block;
        margin-left: 28rem;
    }

    .message-icon:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }

    .tooltip {
        position: absolute;
        top: 5px;
        right:29rem;
        z-index: 999;
        visibility: hidden;
        opacity: 0;
        background-color: #C0C0C0;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 14px;
        white-space: nowrap;
        transition: visibility 0s, opacity 0.3s;
    }


    
    </style>
    <!-- Inside admin-product-page.php -->
    <?php
    // Check for products with low stock (stock < 10)
    $stmt = $conn->prepare("SELECT id, name, stock FROM product_db WHERE stock < 10");
    $stmt->execute();
    $result = $stmt->get_result();
    ?>




<div class="container-fluid">
    <div class="row">
        <div class="col-auto col-md-1 min-vh-100" style="background-color:#78b144;">
            <div class="mt-5" style="background-color:#78b144;">
                <ul class="nav nav-pills flex-column text-center" style="font-family:Playfair Display;">
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
            </li>                </ul>
            </div>
        </div>
        <div class="col-auto col-md-11 mt-5">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-4">
                    <div class="form-search">
                <form action="admin-product-page.php" method="GET" style="margin-left:8px;">
                    <input type="text" class="form-input" name="search" placeholder="Search" style="border-radius: 50px;">
                    <button type="submit" class="fa fa-search"></button>
                </form>
            </div>
                    </div>
                    <div class="col-md-6">
                    </div>
               
                    <div class="col-md-2">
                    <div class="row justify-content-center align-items-center">
                    <?php if ($result->num_rows > 0): ?>
                        <div class="message-icon">
                        <i class="fa-solid fa-circle-exclamation"></i>
                            <div class="tooltip">
                                <h3>The following products are running low on stock:</h3>
                                <ul>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <li><?php echo $row['name']; ?></li>
                                    <?php endwhile; ?>
                                </ul>

                                
                            </div>
                        </div>
                    <?php endif; ?>
                        </div>
                    </div>

                </div>






            <div class="col-auto col-md-11">
                <div class="form d-flex justify-content-center align-items-center mx-auto my-3">
                <div class="form d-flex justify-content-center align-items-center mx-auto my-3">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="block-title" style="color:#78b144;">
                            <h2 class="title" style="color:#78b144;"><span>Add New Product</span></h2>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Enter product name" name="product_name" class="box">
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Enter product description" name="product_description"
                                class="box">
                        </div>
                        <div class="form-group">
                            <input type="number" step="0.01" placeholder="Enter product price" name="product_price"
                                class="box">
                        </div>

                        <div class="form-group">
                            <input type="number" step="any" placeholder="Enter product stock" name="product_stock"
                                class="box">
                        </div>

                        <div class="form-group">
                            <input type="number" step="0.01" placeholder="Enter product weight" name="product_weight"
                                class="box">
                        </div>

                        <label for="category">Category:</label>
                        <div class="form-group" style="position: relative;">
                            <span class="select-icon"
                                style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%);">
                                <i class="fas fa-caret-down text-center" style="color: #222;"></i>
                            </span>
                            <select name="product_category" class="text-center"
                                style="font-size:1.2rem;display: block; padding: 10px 40px 10px 20px; border:1px #222 solid; border-radius: 50px; cursor: pointer; width: 100%; appearance: none; -webkit-appearance: none; -moz-appearance: none;">
                                <option value="Vegetable Fruit">Vegetable and Fruit</option>
                                <option value="Meat">Meat</option>
                                <option value="Seafood">Seafood</option>
                                <option value="Food Essentials">Food Essentials</option>
                                <option value="Household">Household</option>
                                <option value="Beverage">Beverage</option>
                                <option value="Snacks">Snacks</option>
                            </select>
                        </div>

                        <div class="form-group" style="position: relative; overflow: hidden;">
                            <label for="product_image" class="file-label text-center"
                                style="display: block; padding: 10px 20px;border-radius: 50px; cursor: pointer;border:1px #222 solid;">Select
                                Product Image +</label>
                            <input type="file" name="product_image" id="product_image" class="file-input"
                                style="position: absolute; top: 0; left: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                        </div>
                        <div class="form-group" style="font-size:1.5rem;">
                            <label for="">Best Seller:</label>
                            <input type="radio" name="featured" value="Yes" class="box" checked>Yes
                            <input type="radio" name="featured" value="No" class="box">No
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn" style="background-color:#78b144;padding:1rem 10rem;" name="add_product" value="Add a product">
                        </div>
                    </form>
                </div>                </div>

                <div class="col-auto col-md-12 m-5">
                    <?php if (isset($searchResult)): ?>
                        <?php if ($searchResult && $searchResult->num_rows > 0): ?>
                            <div class="product-display">
                                <table class="table table-bordered">
                                    <thead class="admin-product-table-header" style="background-color:grey;">
                                        <tr>
                                            <td class="text-center" style="width: 10%;"><b>Product Image</b></td>
                                            <td class="text-center" style="width: 10%;">Product Name</td>
                                            <td class="text-center" style="width: 25%;">Product Description</td>
                                            <td class="text-center" style="width: 15%;">Product Category</td>
                                            <td class="text-center" style="width: 5%;">Product Price</td>
                                            <td class="text-center" style="width: 10%;">Product Stock</td>
                                            <td class="text-center" style="width: 5%;">Best Seller</td>
                                            <td class="text-center" style="width: 20%;" colspan="2">Action</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $searchResult->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center"><img src="img/product/<?php echo $row['image'] ?>" height="200px" width="200px" alt=""></td>
                                                <td class="text-center" style="text-transform: capitalize;"><?php echo $row['name'] ?></td>
                                                <td class="text-center"><?php echo $row['description'] ?></td>
                                                <td class="text-center"><?php echo $row['category'] ?></td>
                                                <td class="text-center"><?php echo number_format($row['price'], 2) ?></td>
                                                <td class="text-center"><?php echo $row['stock'] ?></td>
                                                <td class="text-center"><?php echo $row['featured'] ?></td>
                                                <td class="text-center">
                                    <a href="admin_update.php?edit=<?php echo $row['id']; ?>" class="btn"><i
                                            class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="admin-product-page.php?delete=<?php echo $row['id']; ?>" class="btn"><i
                                            class="fa-solid fa-trash"></i></a>
                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No products found.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php
$select = mysqli_query($conn, "SELECT * FROM product_db");
?>
                        <div class="product-display">
                            <table class="table table-bordered">
                                <thead class="admin-product-table-header" style="background-color:grey;">
                                    <tr>
                                        <td class="text-center" style="width: 10%;"><b>Product Image</b></td>
                                        <td class="text-center" style="width: 10%;">Product Name</td>
                                        <td class="text-center" style="width: 25%;">Product Description</td>
                                        <td class="text-center" style="width: 15%;">Product Category</td>
                                        <td class="text-center" style="width: 5%;">Product Price</td>
                                        <td class="text-center" style="width: 10%;">Product Stock</td>
                                        <td class="text-center" style="width: 5%;">Featured</td>
                                        <td class="text-center" style="width: 20%;" colspan="2">Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($select)): ?>
                                        <tr>
                                            <td class="text-center"><img src="img/product/<?php echo $row['image'] ?>" height="200px" width="200px" alt=""></td>
                                            <td class="text-center" style="text-transform: capitalize;"><?php echo $row['name'] ?></td>
                                            <td class="text-center"><?php echo $row['description'] ?></td>
                                            <td class="text-center"><?php echo $row['category'] ?></td>
                                            <td class="text-center"><?php echo number_format($row['price'], 2) ?></td>
                                            <td class="text-center"><?php echo $row['stock'] ?></td>
                                            <td class="text-center"><?php echo $row['featured'] ?></td>
                                            <td class="text-center">
                                    <a href="admin_update.php?edit=<?php echo $row['id']; ?>" class="btn"><i
                                            class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="admin-product-page.php?delete=<?php echo $row['id']; ?>" class="btn"><i
                                            class="fa-solid fa-trash"></i></a>
                                </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
