<?php
session_start();

// Include the configuration file
include("config.php");

if (isset($_POST['submit'])) {
    $adminemail = $_POST['adminemail'];
    $password = $_POST['pass'];

    // Prepare and execute the SQL query
    $sql = "SELECT * FROM `admin` WHERE `email` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $adminemail);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Verify the password using password_verify() function (assuming it's hashed)
        // Note: If passwords are stored in plain text, use a secure password hashing method
        if ($password === $row['password']) {
            // Set session variables
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['adminemail'] = $row['email'];
            $_SESSION['username'] = $row['username'];

            // Redirect to the admin page
            header('Location: admin_page.php');
            exit();
        } else {
            // Incorrect password
            echo '<script>
                window.location.href = "adminlogin.php";
                alert("Login failed. Invalid email or password!");
            </script>';
        }
    } else {
        // User not found
        echo '<script>
            window.location.href = "adminlogin.php";
            alert("Login failed. Invalid email or password!");
        </script>';
    }
}
?>
