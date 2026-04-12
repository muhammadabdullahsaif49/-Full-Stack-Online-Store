<?php
session_start();
include_once('db.php');

if(isset($_POST['submit'])){

    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['pass'];
    $role  = $_POST['role'];

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // 🔎 Check duplicate email
    $check = mysqli_query($conn, "SELECT Email FROM registration WHERE Email='$email'");

    if(mysqli_num_rows($check) > 0){
        $_SESSION['msg']   = "Email already exists!";
        $_SESSION['color'] = "red";
        header("Location: login.php");
        exit;
    }

    // ➕ Insert new user
    $insert = "INSERT INTO registration (Email, Password, Username, role)
               VALUES ('$email', '$hash', '$name', '$role')";

    if(mysqli_query($conn, $insert)){
        $_SESSION['msg']   = "Registration successful! Please login.";
        $_SESSION['color'] = "green";
        header("Location: login.php");
        exit;
    } 
    else {
        $_SESSION['msg']   = "Something went wrong!";
        $_SESSION['color'] = "red";
        header("Location: login.php");
        exit;
    }
}
?>
