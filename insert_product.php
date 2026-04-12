<?php
session_start();
include_once('db.php');

if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'seller') {
    header("Location: login_page.php");
    exit;
}

if (isset($_POST['add_product'])) {

    $seller_id = $_SESSION['User_ID'];

    // ================= SANITIZE INPUT =================
    $name     = trim($_POST['product_name']);
    $desc     = trim($_POST['description']);
    $price    = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    // ================= IMAGE UPLOAD =================
    $images = [];
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $img) {

            if ($_FILES['images']['error'][$key] === 0) {

                $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

                if (in_array($ext, $allowedExt)) {

                    $tmp = $_FILES['images']['tmp_name'][$key];
                    $newName = uniqid('prod_', true) . '.' . $ext;

                    if (move_uploaded_file($tmp, "uploads/" . $newName)) {
                        $images[] = $newName;
                    }

                }
            }
        }
    }

    $imgString = implode(",", $images);

    // ================= PREPARED STATEMENT =================
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO products 
        (seller_id, product_name, description, price, quantity, images) 
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "issdis",
        $seller_id,
        $name,
        $desc,
        $price,
        $quantity,
        $imgString
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: seller_products.php");
        exit;
    } else {
        echo "<p style='color:red;'>Error: Product not added!</p>";
    }

    mysqli_stmt_close($stmt);
}
?>
