<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: #f5f7fa;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
            url('https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }

    .login-box {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 420px;
        padding: 40px 35px;
    }

    h2 {
        color: #2c3e50;
        text-align: center;
        margin-bottom: 20px;
        font-size: 28px;
    }

    label {
        color: #34495e;
        font-weight: 500;
        font-size: 15px;
        margin-bottom: 5px;
        display: block;
    }

    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 14px 16px;
        border: 1.5px solid #e0e6ed;
        border-radius: 10px;
        font-size: 16px;
        margin-bottom: 18px;
    }

    input[type="submit"] {
        width: 100%;
        padding: 16px;
        background: linear-gradient(to right, #3498db, #2ecc71);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 17px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
    }

    .footer-text {
        text-align: center;
        margin-top: 25px;
        color: #4a5568;
        font-size: 15px;
    }

    .footer-text a {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
    }
    </style>
</head>

<body>

<div class="login-box">
    <h2>User Login</h2>

    <!-- ✅ MESSAGE SHOW HERE -->
    <?php
    if(isset($_SESSION['msg'])){
        echo "<div style='
            background: ".$_SESSION['color'].";
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 15px;
        '>
            ".$_SESSION['msg']."
        </div>";

        unset($_SESSION['msg']);
        unset($_SESSION['color']);
    }
    ?>

    <form action="page.php" method="POST">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="pass" placeholder="Enter your password" required>

        <input type="submit" name="login" value="Login">
    </form>

    <div class="footer-text">
        Don't have an account? <a href="login.php">Register</a>
    </div>
</div>

</body>
</html>
