<?php
@include 'config.php';

if(isset($_POST['submit'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password =  mysqli_real_escape_string(md5($_POST['password']));
   $cpassword = mysqli_real_escape_string(md5($_POST['cpassword']));

   $select = "SELECT * FROM login WHERE email = '$email'";
   $result = mysqli_query($conn, $select);
    if ($password===$cpassword){

    }
    else{
        $msg = "<div class='alert alert-danger'>Password and Confirm Password do not match</div>";
    }
   
}
?>
