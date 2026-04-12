<?php
session_start();
include_once('db.php');

if(!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'buyer'){
    header("Location: login_page.php");
    exit;
}

// ================= GET PRODUCT =================
if(!isset($_GET['product_id'])){
    header("Location: buyer_dashboard.php");
    exit;
}
$product_id = intval($_GET['product_id']);
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE product_id='$product_id'"));
if(!$product){
    echo "Product not found!";
    exit;
}

// ================= CONTINUE TO CHECKOUT =================
if(isset($_POST['continue_checkout'])){
    $qty = intval($_POST['quantity']);
    if($qty < 1) $qty = 1;
    if($qty > $product['quantity']) $qty = $product['quantity'];

    // Store product & quantity in session
    $_SESSION['checkout_items'] = [$product_id];
    $_SESSION['checkout_qty'] = [$product_id => $qty];

    header("Location: checkout.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Buy Now</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: Poppins;
        background: #f6f9ff;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .1);
    }

    h2 {
        text-align: center;
        color: #4f46e5;
    }

    .product-img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 12px;
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: 500;
    }

    input[type=number] {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-top: 5px;
    }

    button {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        background: #6366f1;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
    }

    a.back {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #4f46e5;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Buy Now: <?php echo htmlspecialchars($product['product_name']); ?></h2>
        <img src="uploads/<?php echo (!empty($product['images'])) ? explode(',',$product['images'])[0] : 'no-image.png'; ?>"
            class="product-img">

        <p>Price: Rs <?php echo number_format($product['price']); ?></p>
        <p>Available Stock: <?php echo $product['quantity']; ?></p>

        <form method="POST">
            <label>Quantity</label>
            <input type="number" name="quantity" min="1" max="<?php echo $product['quantity']; ?>" value="1" required>
            <button type="submit" name="continue_checkout">Continue to Checkout 🛒</button>
        </form>

        <a href="buyer_dashboard.php" class="back">← Back to Dashboard</a>
    </div>
</body>

</html>