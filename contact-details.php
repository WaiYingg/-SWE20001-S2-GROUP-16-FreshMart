<?php
$servername = "localhost";
$username = "root";
$password = "";
$database_name = "database";

$conn = mysqli_connect($servername, $username, $password, $database_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['content'];

    $sql_query = "INSERT INTO contact (name, email, subject, content)
    VALUES ('$name', '$email', '$subject', '$message')";

    if (mysqli_query($conn, $sql_query)) {
        echo "New details entry inserted successfully!";
    } else {
        echo "Error: " . $sql_query . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>