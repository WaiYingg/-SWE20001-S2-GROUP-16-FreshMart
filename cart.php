<?php 
include "config.php";
$email = $_SESSION['email'] ?? '';
$message[]=""; // Initialize an empty array to store error messages

// Function to update product quantity
function updateProductQuantity($conn, $new_quantity, $cart_id, &$message)
{
    $update_query = mysqli_query($conn, "UPDATE `shopping_cart` SET quantity = '$new_quantity' WHERE id = '$cart_id'");
    if ($update_query) {
        $message[] = 'Product quantity updated successfully';
    } else {
        $message[] = 'Failed to update product quantity';
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
            // User is logged in
            // Fetch the product ID and stock quantity from the product_db table
            $select_product = mysqli_query($conn, "SELECT id, stock FROM product_db WHERE name='$product_name'");
            $fetch_product = mysqli_fetch_assoc($select_product);
            $product_id = $fetch_product['id'];
            $product_stock = $fetch_product['stock'];

            // Check if the requested quantity exceeds the available stock
            if ($product_quantity <= 0) {
                $message[] = "Invalid quantity. Quantity must be greater than 0.";
            } else if ($product_quantity > $product_stock) {
                $message[] = array('type' => 'error', 'message' => "Insufficient stock for product '{$product_name}'. Available stock: {$product_stock}. Our admin will restock as soon as possible.");
            } else {
                // Check if the product is already in the cart
                $select_cart = mysqli_query($conn, "SELECT * FROM shopping_cart WHERE name='$product_name' AND email='$email'");
                if (mysqli_num_rows($select_cart) > 0) {
                    // Product already exists in the cart, so update the quantity
                    $fetch_cart = mysqli_fetch_assoc($select_cart);
                    $new_quantity = $fetch_cart['quantity'] + $product_quantity;
                    $update_quantity_query = mysqli_query($conn, "UPDATE `shopping_cart` SET quantity = '$new_quantity' WHERE id = '{$fetch_cart['id']}'");

                    if ($update_quantity_query) {
                        $message[] = 'Product quantity updated successfully';
                    } else {
                        $message[] = 'Failed to update product quantity';
                    }
                } else {
                    // Product is not in the cart, so add it
                    $insert_product = mysqli_query($conn, "INSERT INTO shopping_cart (name, price, image, quantity, email, product_id) VALUES ('$product_name', '$product_price', '$product_image', '$product_quantity', '$email', '$product_id')");
                    if ($insert_product) {
                        $message[] = 'Product added to cart successfully';
                    } else {
                        $message[] = 'Failed to add product to cart';
                    }
                }
            }
        } else {
            // User is not logged in, redirect to the login page
            header('Location: login.php');
            exit();
        }
    }
}

// ...
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
        mysqli_query($conn, "DELETE FROM  shopping_cart WHERE id='$remove_id'");
        $message[] = 'Product removed from cart successfully';
    } else {
        // User is not logged in, redirect to the login page
        header('Location: login.php');
        exit();
    }
}

?>
