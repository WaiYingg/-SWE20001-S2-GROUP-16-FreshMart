<?php session_start();

include("config.php");

if (isset($_POST['submit'])) {
    $driveremail = $_POST['driveremail']; // Corrected variable name
    $password = $_POST['pass'];

    $sql = "SELECT * FROM `driver` WHERE `email` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $driveremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Verify the password using password_verify() function
        if ($password === $row['password']) {
            if ($row['user_type'] == 'driver') {
                $_SESSION['driveremail'] = $row['email'];
                $_SESSION['driver_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                header('Location: driver_page.php');
                exit();
            }
        } else {
            // Incorrect password
            echo '<script>
                window.location.href = "driverlogin.php";
                alert("Login failed. Invalid password!");
            </script>';
        }
    } else {
        // User not found
        echo '<script>
            window.location.href = "driverlogin.php";
            alert("Login failed. Invalid email!");
        </script>';
    }
}
?>
