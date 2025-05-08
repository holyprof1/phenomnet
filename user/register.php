<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Form validation
//Import my security function from function.php
include('function.php');
//Define empty variables
$fname = $lname = $email = $pass = $cpass = "";
//Define empty error variables
$fnameErr = $lnameErr = $emailErr = $passErr = $cpassErr = $cpassesErr = $checkErr = ""; 

//Test for valid(empty) and invalid(non-empty) form fields
if($_SERVER['REQUEST_METHOD']=="POST"){
  if(empty($_POST['fname'])){
    $fnameErr = "Firstname is required";
  }
  else{
    $fname = sanitize($_POST['fname']);
  }

  if(empty($_POST['lname'])){
    $lnameErr = "Lastname is required";
  }
  else{
    $lname = sanitize($_POST['lname']);
  }

  if(empty($_POST['email'])){
    $emailErr = "Email is required";
  }
  else{
    $email = sanitize($_POST['email']);
  }

  if(empty($_POST['pwd'])){
    echo "<style>.note{display:none !important;</style>";
    $passErr = "Password is required";
  }
  else{
    $pass = md5($_POST['pwd']);
  }

  if(empty($_POST['cpwd'])){
    echo "<style>.note{display:none !important;</style>";
    $cpassErr = "Confirm password is required";
  }
  else{
    $cpass = md5($_POST['cpwd']);
  }

  if($pass !== $cpass){
    $cpassesErr = "Both passwords do not match";
  }

  $coupon = "qw12ghf3agz"; // Define the correct coupon code

if(empty($_POST['coupon_code'])){
    $couponErr = "Coupon code is required";
}
else{
    $enteredCoupon = $_POST['coupon_code'];
    if($enteredCoupon !== $coupon){
        $couponErr = "Wrong coupon code";
    }
}

  if(!isset($_POST['agreement'])){
    $checkErr = "Please agree to our terms";
  }
}
?>
<?php
if(isset($_POST['reg'])){
  $affid = $_POST['affid'];
  if($pass===$cpass){
    $active = "";
    $sql_check_email_exists = "SELECT * FROM users WHERE user_email = '$email'";
    $sql_check_email_exec = $con->query($sql_check_email_exists);
    if(mysqli_num_rows($sql_check_email_exec)>0){$toast = "email";}
    else{
  $sqlIns = "INSERT INTO users(firstname,lastname,user_email,user_pass,affid)VALUES('$fname','$lname','$email','$cpass','$affid')";
  $sqlC = $con->query($sqlIns);

  $sqlIns2 = "INSERT INTO fund(user_email,amount,status)VALUES('$email',50,'approved')";
  $sqlC2 = $con->query($sqlIns2);
 if($sqlC){
  //Load Composer's autoloader
 require 'admin/vendor/autoload.php';

 //Create an instance; passing `true` enables exceptions
 $mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = ''; 
    $mail->SMTPAuth   = true; 
    $mail->Username   = ''; 
    $mail->Password   = ''; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port  = 465; 

    //Recipients
    $mail->setFrom();
    $mail->addAddress();
    $mail->isHTML(true);
    $mail->Subject = 'You have a new member';
    $mail->Body    = 'A new member has just registered on the platform. Login to the admin dashboard to see this person.';
    $mail->AltBody = 'A new member has just registered on the platform. Login to the admin dashboard to see this person';
    $mail->send();
} catch (Exception $e){echo " ";}

  $toast = "success";header("Refresh:2,url=preloader.php?fn=$fname&em=$email");
}else{$toast = "fail";} 
}
}}
 $con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Member Registration</title>
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />

<!-- v4.0.0-alpha.6 -->
<link rel="stylesheet" href="dist/bootstrap/css/bootstrap.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

<!-- Theme style -->
<link rel="stylesheet" href="dist/css/style.css">
<link rel="stylesheet" href="dist/css/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="dist/css/et-line-font/et-line-font.css">
<link rel="stylesheet" href="dist/css/themify-icons/themify-icons.css">
<link rel="stylesheet" href="dist/plugins/hmenu/ace-responsive-menu.css">
<link rel="icon" href="dist/img/p2pdarkicon.png">

<!--Toastr-->
<link rel="stylesheet" type="text/css" href="dist/css/toastr.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="../custom.css">
</head>
<body class="hold-transition login-page sty1">
<div class="login-box sty1">
  <div class="login-box-body sty1">
  <div class="login-logo m-t-0 p-t-0">
    <a href="#"><span class="lead"><span class="lead cursive">PHENOM<span class="orange">NET</span></span></a>
  </div>
    <p class="login-box-msg m-t-0 p-t-0">Fill the form to create a PHENOMnet account</p>
     <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST" name="regForm">
      <div class="form-group has-feedback">
        <input type="hidden" name="affid" class="form-control sty1" value="<?= mt_rand(100000,999999);?>">
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="fname" class="form-control sty1" placeholder="Firstname" required>
        <span class="err"><?= $fnameErr; ?></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="lname" class="form-control sty1" placeholder="Lastname" required>
        <span class="err"><?= $lnameErr; ?></span>
      </div>
      <div class="form-group has-feedback">
        <input type="email" name="email" class="form-control sty1" placeholder="Email" title="Your email is your username" required>
        <span class="err"><?= $emailErr; ?></span>        
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="pwd" class="form-control sty1" placeholder="Password" pattern="(?=.*\d)(?=.*[A-Z])(?=.*[a-z]).{8,}" title="password must contain atleast 1 digit, atleast 1 uppercase letter, atleast 1 lowercase letter and minimum of 8 characters in all" required>
      <span class="note"><small>atleast 1 digit, atleast 1 uppercase letter, atleast 1 lowercase letter and minimum of 8 characters total</small></span>
      <span class="err"><?= $passErr; ?></span>
      <div class="form-group has-feedback">
        <input type="password" name="cpwd" class="form-control sty1" title="Confirm password must match the initial password entered in the box above" placeholder="Confirm Password" required><span class="note"><small>must match password entered in the box above</small></span>
        <span class="err"><?= $cpassErr; ?></span><br>
         <span class="err"><?= $cpassesErr; ?></span>
      </div>
      </div>
    <div class="form-group has-feedback">
    <input type="text" name="coupon_code" class="form-control sty1" placeholder="Enter Coupon Code" required>
    <span class="err"><?= $couponErr; ?></span>
</div>
      <div>
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" name="agreement" required>
               I agree to the <a href="https://PHENOMNETtrade.org/terms-of-use.php" title="View terms of use" target="_blank" rel="noopener noreferrer">Terms Of Use</a></label><br>
               <span class="err"><?= $checkErr; ?></span>
             </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4 m-t-1">
          <div class="">Already have an account? <a href="login.php" class="text-center">Sign In</a></div>
          <button type="submit" class="btn btn-warning btn-block btn-flat" name="reg">Sign Up</button>
        </div>
        <!-- /.col --> 
      </div> 
    </form>
  </div>  
   </div>
  </div>
  <!-- /.login-box-body --> 
</div>
<!-- /.login-box --> 

<!-- jQuery 3 --> 
<script src="dist/js/jquery.min.js"></script> 

<!-- v4.0.0-alpha.6 --> 
<script src="dist/bootstrap/js/bootstrap.min.js"></script> 

<!-- template --> 
<script src="dist/js/niche.js"></script>

<!--Toastr-->
<script type="text/javascript" src="dist/js/toastr.min.js"></script>
</body>
</html>
<?php
if(isset($toast) && $toast==='success'){
  echo "<script>toastr.success('Lets verify and create your account', 'Notice')</script>";
}

if(isset($toast) && $toast==='fail'){
  echo "<script>toastr.error('You have not been successfully registered', 'Failure')</script>";
}

if(isset($toast) && $toast==='email'){
  echo "<script>toastr.error('Get a real coupon from a vendor', 'Failure')</script>";
}
?>