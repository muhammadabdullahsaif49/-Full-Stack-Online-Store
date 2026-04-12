<?php
session_start();
include_once('db.php');

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $pass  = $_POST['pass'];

    $query = mysqli_query($conn,
        "SELECT * FROM registration WHERE Email='$email'"
    );

    // ❌ Email not found
    if(mysqli_num_rows($query) == 0){
        $_SESSION['msg'] = "Email not found!";
        $_SESSION['color'] = "red";
        header("Location: login_page.php");
        exit;
    }

    $row = mysqli_fetch_assoc($query);

    // ❌ Wrong password
    if(!password_verify($pass, $row['Password'])){
        $_SESSION['msg'] = "Wrong password!";
        $_SESSION['color'] = "red";
        header("Location: login_page.php");
        exit;
    }

    // ✅ Login success
    $_SESSION['User_ID']  = $row['id'];
    $_SESSION['Username'] = $row['Username'];
    $_SESSION['Email']    = $row['Email'];
    $_SESSION['Role']     = $row['role'];

    if($row['role'] == 'buyer'){
        header("Location: buyer_dashboard.php");
    } else {
        header("Location: seller_dashboard.php");
    }
    exit;
}
?>
