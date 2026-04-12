<?php
session_start();
include_once('db.php');

if(!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'seller'){
    header("Location: login_page.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: seller_products.php");
    exit;
}

$seller_id  = $_SESSION['User_ID'];
$product_id = $_GET['id'];

// 1) Pehle check karo product isi seller ka hai ya nahi
$check = mysqli_query($conn, "SELECT * FROM products WHERE product_id='$product_id' AND seller_id='$seller_id'");
if(mysqli_num_rows($check) == 0){
    // Agar product is seller ka nahi hai to back
    header("Location: seller_products.php");
    exit;
}

$product = mysqli_fetch_assoc($check);

// 2) Images delete from folder (uploads)
if(!empty($product['images'])){
    $imgs = explode(",", $product['images']);
    foreach($imgs as $img){
        $imgPath = "uploads/" . trim($img);
        if(file_exists($imgPath)){
            unlink($imgPath);
        }
    }
}

// 3) Product delete from database
$delete = mysqli_query($conn, "DELETE FROM products WHERE product_id='$product_id' AND seller_id='$seller_id'");

if($delete){
    header("Location: seller_products.php");
    exit;
}else{
    echo "Delete Failed: " . mysqli_error($conn);
}
?>
