<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Log In Page</title>
    
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
    <!--extra asset-->
    <link rel="stylesheet" href="asset/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="asset/fontawesome-free-6.4.0-web/css/all.min.css">
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
.password-toggle {
        position: relative;
    }

    .password-toggle .toggle-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    }

    .password-toggle .toggle-icon i {
        margin-bottom: 18px;
    }
</style>
</head>

<body class="home home-1">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-md-6">
                <div class="login-page">
                    <div class="login-form form">
                    <div class="login-box">
                        <div class="block-title">
                            <h2 class="title" style="color:#78b144;"><span>Admin Login</span></h2>
                        </div>

                        <form action="adminlogindata.php" onsubmit="return isValid()" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="" name="adminemail" id="adminemail" >
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <div class="password-toggle">
                                    <div class="col d-flex justify-content-between">
                                        <input type="password" value="" name="pass" id="pass" class="form-control">
                                        <label for="pass" class="toggle-icon" onclick="togglePasswordVisibility()">
                                            <i class="fa fa-eye"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <input type="submit" style="background-color:#78b144;" class="btn btn-primary" value="LOG IN" name="submit">
                            </div>
                            <div class="form-group text-center">
                                <div class="link">
                                    <p>Don't have an account? <a href="admin_register.php" style="color: #78b144;">REGISTER NOW</a></p>
                                </div>
                                <div class="form-group text-center">
                                </div>
                            </div>
                        </form>

                        <script>
                            function isValid() {
                                var adminemail = document.getElementById('email').value;
                                var pass = document.getElementById('pass').value;

                                if (adminemail.length === 0 && pass.length === 0) {
                                    alert("Email and password field is empty!!!");
                                    return false;
                                } else {
                                    if (adminemail.length === 0) {
                                        alert("User Name is empty!!!");
                                        return false;
                                    }
                                    if (pass.length === 0) {
                                        alert("Password is empty!!!");
                                        return false;
                                    }
                                }

                                return true;
                            }

                            function togglePasswordVisibility() {
                                var passwordInput = document.getElementById('pass');
                                var toggleIcon = document.querySelector('.toggle-icon i');

                                if (passwordInput.type === 'password') {
                                    passwordInput.type = 'text';
                                    toggleIcon.classList.remove('fa-eye');
                                    toggleIcon.classList.add('fa-eye-slash');
                                } else {
                                    passwordInput.type = 'password';
                                    toggleIcon.classList.remove('fa-eye-slash');
                                    toggleIcon.classList.add('fa-eye');
                                }
                            }
                        </script>
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
    <script src="asset/slider-range/js/jquery.slider.js
