<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $order_id = $_POST['order_id'];
    $driver_username = $_POST['driver_username'];
    $driver_email = $_POST['driver_email'];

    // Perform the database update to assign the order to the selected driver
    // You should implement your specific database update logic here

    // Example update query (modify as needed):
    $update_query = "UPDATE `orders1` SET assigned_driver = '$driver_username', assigned_driver_email = '$driver_email' WHERE order_id = '$order_id'";
    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
        echo "Order assigned to " . $driver_username . " (" . $driver_email . ") successfully!";
    } else {
        echo "Failed to assign the order to " . $driver_username . " (" . $driver_email . "). Please try again.";
    }
}
?>
