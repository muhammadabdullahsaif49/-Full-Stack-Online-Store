<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Registration</title>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
            url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da');
        background-size: cover;
        background-position: center;
    }

    .form-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 40px;
        border-radius: 16px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .role-box {
        display: flex;
        gap: 20px;
    }

    .role-box label {
        flex: 1;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }

    .btn {
        width: 100%;
        padding: 14px;
        border: none;
        background: linear-gradient(to right, #3498db, #2ecc71);
        color: white;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
    }
    </style>
</head>

<body>

    <div class="form-box">
        <h2>Create Account</h2>

        <!-- ✅ MESSAGE SHOW HERE -->
        <?php
    session_start();
    if(isset($_SESSION['msg'])){
        echo "<div style='
            background:".$_SESSION['color'].";
            color:white;
            padding:12px;
            border-radius:8px;
            text-align:center;
            margin-bottom:15px;
        '>
            ".$_SESSION['msg']."
        </div>";
        unset($_SESSION['msg']);
        unset($_SESSION['color']);
    }
    ?>

        <form action="data_ins.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="pass" required>
            </div>

            <div class="form-group">
                <label>User Type</label>
                <div class="role-box">
                    <label>
                        <input type="radio" name="role" value="buyer" required> Buyer
                    </label>
                    <label>
                        <input type="radio" name="role" value="seller"> Seller
                    </label>
                </div>
            </div>

            <button class="btn" type="submit" name="submit">Register</button>

            <div class="login-link">
                Already have an account?
                <a href="login_page.php">Login</a>
            </div>
        </form>
    </div>

</body>

</html>