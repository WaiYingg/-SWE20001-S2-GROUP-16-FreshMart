<?php session_start();

include("config.php");

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    $sql = "SELECT * FROM `login` WHERE `email` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Verify the password using password_verify() function
        if ($password === $row['password']) {
            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_id'] = $row['id'];
                header('Location: admin_page.php');
                exit();
            } else if ($row['user_type'] == 'user') {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['username'] = $row['username'];
                header('Location: account.php');
                exit();
            } else if ($row['user_type'] == 'driver') {
                $_SESSION['driver_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                header('Location: driver-site.php');
                exit();
            }
        } else {
            // Incorrect password
            echo '<script>
                window.location.href = "login.php";
                alert("Login failed. Invalid password!");
            </script>';
        }
    } else {
        // User not found
        echo '<script>
            window.location.href = "login.php";
            alert("Login failed. Invalid email!");
        </script>';
    }
}
?>
