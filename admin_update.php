<?php
include "config.php";

$id = $_GET['edit'];
$message = array();

// Retrieve existing product image
$select = "SELECT image FROM product_db WHERE id=?";
$stmt = mysqli_prepare($conn, $select);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $existing_image);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (isset($_POST['update_product'])) {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_stock = $_POST['product_stock'];
    $product_feature = isset($_POST['featured']) ? $_POST['featured'] : 'No';
    $product_weight = $_POST['product_weight'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'img/product/' . $product_image;

    // Check if a new image file is uploaded
    if (empty($product_name) || empty($product_price)) {
        $message[] = 'Please fill out all fields';
    } else {
        // Use prepared statement to avoid SQL injection
        if (!empty($product_image)) {
            $update = "UPDATE product_db SET name=?, description=?, price=?, image=?, featured=?, stock=?, product_weight=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt, "ssssissi", $product_name, $product_description, $product_price, $product_image, $product_feature, $product_stock, $product_weight, $id);
            if (move_uploaded_file($product_image_tmp_name, $product_image_folder)) {
                // File upload successful
            } else {
                $message[] = 'Failed to upload product image';
            }
        } else {
            $update = "UPDATE product_db SET name=?, description=?, price=?, featured=?, stock=?, product_weight=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt, "ssssisi", $product_name, $product_description, $product_price, $product_feature, $product_stock, $product_weight, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $message[] = 'Product updated successfully';
        } else {
            $message[] = 'Product could not be updated: ' . mysqli_stmt_error($stmt);
        }
    }
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
</head>

<body>
    <?php
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '<div class="col-5 mx-auto my-3 messages">' . $msg . '</div>';
        }
    }
    ?>

    <div class="container">
        <div class="admin-product-form-container d-flex justify-content-center mt-5">
            <?php
            $select = mysqli_query($conn, "SELECT * FROM product_db WHERE id=$id");
            while ($row = mysqli_fetch_assoc($select)) {
                ?>
                <div class="form d-flex justify-content-center">
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?edit=' . $id; ?>" method="POST"
                        enctype="multipart/form-data">
                        <div class="block-title" style="color:#FF7F50;">
                            <h2 class="title" style="color:#78b144;"><span>Update Product</span></h2>
                        </div>
                        <div class="form-group">
                            <label for="product_name">Product Name:</label>
                            <input type="text" placeholder="Enter product name" name="product_name"
                                value="<?php echo $row['name']; ?>" class="box">
                        </div>
                        <div class="form-group">
                        <label for="product_name">Product Description:</label>
                            <input type="text" placeholder="Enter product description" name="product_description"
                                class="box" value="<?php echo $row['description']; ?>">
                        </div>
                        <div class="form-group">
                        <label for="product_name">Product Price:</label>
                            <input type="number" placeholder="Enter product price" name="product_price"
                                value="<?php echo $row['price']; ?>" class="box">
                        </div>
                        <div class="form-group">
                        <label for="product_name">Product Stock:</label>
                            <input type="number" placeholder="Enter product stock" name="product_stock"
                                value="<?php echo $row['stock']; ?>" class="box">
                        </div>
                        <div class="form-group">
                        <label for="product_name">Product Weight:</label>
                            <input type="number" step="0.01" placeholder="Enter product weight" name="product_weight"
                                value="<?php echo $row['product_weight']; ?>" class="box">
                        </div>
                        <div class="form-group" style="position: relative; overflow: hidden;">
                            <label for="product_image" class="file-label text-center"
                                style="display: block; padding: 10px 20px;border-radius: 50px; cursor: pointer;border:1px #222 solid;">Select
                                Product Image +</label>
                            <input type="file" name="product_image" id="product_image" class="file-input"
                                style="position: absolute; top: 0; left: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                        </div>

                        <div class="form-group">
                            <label for="">Best Seller:</label>
                            <input type="radio" name="featured" value="Yes" <?php if ($row['featured'] == 'Yes')
                                echo 'checked'; ?> class="box">Yes
                            <input type="radio" name="featured" value="No" <?php if ($row['featured'] == 'No')
                                echo 'checked'; ?> class="box">No
                        </div>
                        <input type="submit" class="btn" name="update_product" value="Update Product" style="background-color:#78b144;">
                        <a href="admin-product-page.php" class="btn" style="background-color:#78b144;">Go Back</a>
                    </form>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</body>
</html>