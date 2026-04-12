<?php
session_start();
include_once('db.php');

if(!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'seller'){
    header("Location: login_page.php");
    exit;
}

if(isset($_POST['update_product'])){

    $seller_id  = $_SESSION['User_ID'];
    $product_id = $_POST['product_id'];

    $name     = $_POST['product_name'];
    $desc     = $_POST['description'];
    $price    = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Old images still selected by user
    $oldImages = isset($_POST['old_images']) ? $_POST['old_images'] : [];

    // Fetch old product data from DB
    $oldQuery = mysqli_query($conn, "SELECT * FROM products WHERE product_id='$product_id' AND seller_id='$seller_id'");
    $oldProduct = mysqli_fetch_assoc($oldQuery);
    $dbOldImages = !empty($oldProduct['images']) ? explode(",", $oldProduct['images']) : [];

    // Delete images removed by user
    foreach($dbOldImages as $img){
        if(!in_array($img, $oldImages)){
            $imgPath = "uploads/" . trim($img);
            if(file_exists($imgPath)){
                unlink($imgPath);
            }
        }
    }

    // Handle new uploaded images
    $newImages = [];
    $allowedExt = ['jpg','jpeg','png','gif','webp','svg']; // Allowed extensions
    if(!empty($_FILES['images']['name'][0])){
        foreach($_FILES['images']['name'] as $key => $img){
            if($_FILES['images']['error'][$key] == 0){
                $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
                if(in_array($ext, $allowedExt)){
                    $tmp = $_FILES['images']['tmp_name'][$key];
                    $newName = time().'_'.$img;
                    move_uploaded_file($tmp, "uploads/".$newName);
                    $newImages[] = $newName;
                } else {
                    echo "<p style='color:red;'>File $img not allowed! Only jpg, jpeg, png, gif, webp, svg allowed.</p>";
                }
            }
        }
    }

    // Merge old + new images
    $finalImages = array_merge($oldImages, $newImages);
    $imgString = implode(",", $finalImages);

    // Update product in database
    $update = "UPDATE products SET 
        product_name='$name',
        description='$desc',
        price='$price',
        quantity='$quantity',
        images='$imgString'
        WHERE product_id='$product_id' AND seller_id='$seller_id'";

    if(mysqli_query($conn, $update)){
        // Redirect to seller products page
        header("Location: seller_products.php");
        exit;
    }else{
        echo "Update Failed: " . mysqli_error($conn);
    }
}
?>
