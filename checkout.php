<?php
session_start();
include_once('db.php');

if(!isset($_SESSION['User_ID']) || $_SESSION['Role']!='buyer'){
    header("Location: login_page.php");
    exit;
}

$buyer_id = $_SESSION['User_ID'];
$selected_items = $_SESSION['checkout_items'] ?? [];
$qty_arr = $_SESSION['checkout_qty'] ?? [];

if(empty($selected_items)){
    echo "<p>No items selected. <a href='buyer_dashboard.php'>Go Back</a></p>";
    exit;
}

if(isset($_POST['place_order'])){
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $phone = trim($_POST['phone']);
    $payment = trim($_POST['payment_method']);

    if($address=="" || $city=="" || $phone=="" || $payment==""){
        $error = "Please fill all fields!";
    } else {
        $total_amount = 0;
        $order_date = date('Y-m-d H:i:s');
        foreach($selected_items as $pid){
            $pid = intval($pid);
            $qty = $qty_arr[$pid] ?? 1;

            $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE product_id='$pid'"));
            if(!$product) continue;

            if($qty > $product['quantity']) $qty = $product['quantity'];
            if($qty <=0) continue;

            $subtotal = $product['price'] * $qty;
            $total_amount += $subtotal;

            mysqli_query($conn, "INSERT INTO orders
            (product_id,buyer_id,seller_id,qty,status,order_date,quantity,total_price,address,city,phone,payment_method)
            VALUES
            ('$pid','$buyer_id','".$product['seller_id']."','$qty','Pending','$order_date','$qty','$subtotal','$address','$city','$phone','$payment')");

            // Reduce stock
            $new_stock = $product['quantity'] - $qty;
            mysqli_query($conn, "UPDATE products SET quantity='$new_stock' WHERE product_id='$pid'");

            // Remove from cart if exists
            unset($_SESSION['cart'][$pid]);
        }

        // Clear checkout session
        unset($_SESSION['checkout_items']);
        unset($_SESSION['checkout_qty']);

        echo "<div style='max-width:600px;margin:50px auto;padding:25px;background:#fff;border-radius:10px;font-family:Poppins;text-align:center;'>
        <h2 style='color:#22c55e;'>✅ Order Placed Successfully</h2>
        <p>Total Amount: Rs ".number_format($total_amount)."</p>
        <p>Payment Method: $payment</p>
        <a href='buyer_dashboard.php' style='display:inline-block;margin-top:15px;background:#4f46e5;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;'>Go to Dashboard</a>
        </div>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: Poppins;
        background: #f6f9ff;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 700px;
        margin: 50px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .1);
    }

    h2 {
        color: #4f46e5;
        text-align: center;
    }

    label {
        display: block;
        margin-top: 12px;
        font-weight: 500;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    textarea {
        height: 80px;
        resize: none;
    }

    button {
        margin-top: 18px;
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        background: #22c55e;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
    }

    a.back {
        display: inline-block;
        margin-top: 12px;
        color: #4f46e5;
        text-decoration: none;
    }

    .error {
        color: red;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Checkout 🧾</h2>
        <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <label>Full Address</label>
            <textarea name="address" placeholder="Enter full delivery address..." required></textarea>
            <label>City</label>
            <input type="text" name="city" placeholder="Enter city" required>
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="03xx-xxxxxxx" required>
            <label>Payment Method</label>
            <select name="payment_method" required>
                <option value="">-- Select Payment Method --</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="JazzCash">JazzCash</option>
                <option value="EasyPaisa">EasyPaisa</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
            <button type="submit" name="place_order">Place Order ✅</button>
        </form>
        <a href="cart.php" class="back">← Back to Cart</a>
    </div>
</body>

</html>