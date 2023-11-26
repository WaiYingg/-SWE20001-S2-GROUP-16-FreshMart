<?php
session_start();
include "config.php";

if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];

    // Prepare the SQL query
    $sql = "SELECT * FROM product_db1 WHERE name LIKE '%$searchTerm%' OR category LIKE '%$searchTerm%'";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if any results were found
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Output the product information
            echo '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="hehe">';
            echo '<div class="product-item">';
            echo '<form action="" method="POST">';
            echo '<div class="product-image">';
            echo '<a href="product-detail-left-sidebar.php?id=' . $row['id'] . '">';
            echo '<img class="img-responsive" src="img/product/' . $row['image'] . '" alt="Product Image">';
            echo '</a>';
            echo '</div>';
            echo '<div class="product-title">';
            echo '<a href="product-detail-left-sidebar.php?id=' . $row['id'] . '">' . $row['name'] . '</a>';
            echo '</div>';
            echo '<div class="product-rating">';
            echo '<div class="star on"></div>';
            echo '<div class="star on"></div>';
            echo '<div class="star on"></div>';
            echo '<div class="star on"></div>';
            echo '<div class="star"></div>';
            echo '</div>';
            echo '<div class="product-price">';
            echo '<span class="sale-price">$ ' . $row['price'] . '</span>';
            echo '</div>';
            echo '<input type="hidden" name="product_name" value="' . $row['name'] . '">';
            echo '<input type="hidden" name="product_price" value="' . $row['price'] . '">';
            echo '<input type="hidden" name="product_image" value="' . $row['image'] . '">';
            echo '<div class="product-buttons">';
            echo '<input type="submit" value="Add To cart" name="add_to_cart" class="btn">';
            echo '</div>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="col-lg-12">';
        echo 'No results found.';
        echo '</div>';
    }
} else {
    header('Location: product-list-left-sidebar.php');
    exit();
}
?>

