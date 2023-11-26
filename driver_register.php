<?php
include("config.php");
$msg = "";

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $driveremail = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $code = mysqli_real_escape_string($conn, md5(rand()));
    $user_type = "driver"; // Set the default value of user_type as "user"
    $Carplate = mysqli_real_escape_string($conn, $_POST['Carplate']);
    $age = mysqli_real_escape_string($conn, $_POST['age']); // Add this line to get age value

    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM driver WHERE email='{$driveremail}'")) > 0) {
        $msg = "<div class='alert alert-danger'>{$driveremail} - This email address has already been used.</div>";
    } else {
        if ($password === $cpassword) {
            $sql = "INSERT INTO driver (username, email, password, code, user_type, Carplate, age) VALUES ('{$name}', '{$driveremail}', '{$password}', '{$code}', '{$user_type}', '{$Carplate}', '{$age}')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                $message[] = 'registered successfully!';
                header("Location: driverlogin.php");
            } else {
                $msg = "<div class='alert alert-danger'>Something went wrong.</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>Password and Confirm Password do not match</div>";
        }
    }
}
?>




<!DOCTYPE html>
<html lang="zxx">
	

<head>
		<!-- Basic Page Needs -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>FreshMart - Organic, Fresh Food, Farm Store HTML Template</title>
		
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
		
		<!-- Vendor CSS -->
        <link rel="stylesheet" href="asset/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="asset/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="asset/font-material/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="asset/nivo-slider/css/nivo-slider.css">
        <link rel="stylesheet" href="asset/nivo-slider/css/animate.css">
        <link rel="stylesheet" href="asset/nivo-slider/css/style.css">
        <link rel="stylesheet" href="asset/owl.carousel/assets/owl.carousel.min.css">
        <link rel="stylesheet" href="asset/slider-range/css/jslider.css">
		<link rel="stylesheet" href="asset/fontawesome-free-6.4.0-web/css/all.min.css">
        <link rel="stylesheet" href="asset/bootstrap-5.0.2-dist/css/bootstrap-grid.min.css">
        <link rel="stylesheet" href="asset/bootstrap-5.0.2-dist/css/bootstrap.min.css">
		
		<!-- Template CSS -->
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/reponsive.css">
		<style>
    .login-box {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
</style>
	</head>
	<body>	
				<div class="container">
                <div class="row justify-content-center align-items-center">
					<div class="register-page">
						<div class="register-form form" style="margin-bottom:30px;margin-top:30px;">
						<div class="login-box">
							<div class="block-title">
								<h2 class="title" style="color:#78b144;"><span>Create Driver Account</span></h2>
							</div>
							
							<form action="" method="POST" enctype="multipart/form-data">
							
							<?php echo $msg; ?>

								<div class="form-group">
									<label>UserName</label>
									<input type="text" value="<?php if (isset($_POST['submit'])) { echo $name; }?>" name="name" required>
								</div>
								
								<div class="form-group">
									<label>Email</label>
									<input type="email" value="<?php if (isset($_POST['submit'])) { echo $driveremail; }?>" name="email" required>
								</div>
								<div class="form-group">
									<label>Age</label>
									<input type="text" value="<?php if (isset($_POST['submit'])) { echo $age; } ?>" name="age" required>
								</div>

								<div class="form-group">
									<label>Carplate</label>
									<input type="text" value="<?php if (isset($_POST['submit'])) { echo $Carplate; }?>" name="Carplate" required>
								</div>
								
								<div class="form-group">
									<label>Password</label>
									<input type="password" value="" name="password" required>
								</div>

								<div class="form-group">
									<label>Confirm your Password</label>
									<input type="password" value="" name="cpassword">
								</div>
								
								
								<div class="form-group text-center">
									<input type="submit" style="background-color:#78b144;"class="btn btn-primary" value="Register" name="submit"></br></br>
									<p>Already have an account? <a href="driverlogin.php" style="color: #78b144;">LOGIN NOW</a></p>

								</div>

							</form>
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
		
		<!-- Template CSS -->
		<script src="js/main.js"></script>
	</body>

</html>