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

// Fetch product (only seller's product)
$query = mysqli_query($conn, "SELECT * FROM products WHERE product_id='$product_id' AND seller_id='$seller_id'");
if(mysqli_num_rows($query) == 0){
    header("Location: seller_products.php");
    exit;
}

$product = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins;
}

body{
    background:#f6f9ff;
    color:#1f2937;
}

/* ===== LAYOUT ===== */
.wrapper{
    display:flex;
    min-height:100vh;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:240px;
    background:#ffffff;
    border-right:1px solid #e5e7eb;
    padding:25px 20px;
}

.sidebar h2{
    text-align:center;
    color:#6366f1;
    margin-bottom:30px;
}

.sidebar a{
    display:block;
    padding:12px 15px;
    margin-bottom:10px;
    text-decoration:none;
    color:#374151;
    border-radius:10px;
    font-weight:500;
    transition:.3s;
}

.sidebar a:hover,
.sidebar a.active{
    background:#6366f1;
    color:white;
}

/* ===== CONTENT ===== */
.content{
    flex:1;
    padding:35px;
}

/* ===== HEADER ===== */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.header h3{
    font-size:26px;
}

.header span{
    color:#6b7280;
}

/* ===== FORM CARD ===== */
.form-card{
    background:white;
    max-width:700px;
    padding:30px;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.form-card h3{
    margin-bottom:20px;
    color:#111827;
}

/* ===== FORM ===== */
.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    margin-bottom:6px;
    font-weight:500;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:12px;
    border:1px solid #d1d5db;
    border-radius:10px;
    font-size:14px;
}

.form-group textarea{
    height:100px;
    resize:none;
}

.form-row{
    display:flex;
    gap:15px;
}

.form-row .form-group{
    flex:1;
}

/* OLD IMAGES */
.old-images{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:10px;
}

.image-wrapper{
    position: relative;
    display: inline-block;
}

.image-wrapper img{
    width:80px;
    height:80px;
    border-radius:12px;
    object-fit:cover;
    border:1px solid #e5e7eb;
}

.remove-img{
    position: absolute;
    top:2px;
    right:2px;
    background:#ef4444;
    color:white;
    cursor:pointer;
    padding:2px 5px;
    font-weight:bold;
    border-radius:50%;
}

/* BUTTON */
.btn-save{
    width:100%;
    background:#6366f1;
    color:white;
    padding:14px;
    border:none;
    border-radius:12px;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}

.btn-save:hover{
    background:#4f46e5;
}

.note{
    font-size:13px;
    color:#6b7280;
    margin-top:6px;
}

/* RESPONSIVE */
@media(max-width:900px){
    .form-row{ flex-direction:column; }
}
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Seller Panel</h2>
        <a href="seller_dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href="seller_products.php" class="active">My Products</a>
        <a href="view_orders.php">Orders</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="header">
            <h3>Edit Product</h3>
            <span><?php echo $_SESSION['Username']; ?></span>
        </div>

        <div class="form-card">
            <h3>Update Product Information</h3>

            <form action="update_product.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required><?php echo $product['description']; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (Rs)</label>
                        <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" required>
                    </div>
                </div>

                <!-- OLD IMAGES WITH CROSS -->
                <div class="form-group">
                    <label>Old Images</label>
                    <div class="old-images">
                        <?php 
                        if(!empty($product['images'])){
                            $imgs = explode(",", $product['images']);
                            foreach($imgs as $img){
                                echo "<div class='image-wrapper'>
                                        <img src='uploads/$img' alt='product'>
                                        <span class='remove-img'>×</span>
                                        <input type='hidden' name='old_images[]' value='$img'>
                                      </div>";
                            }
                        }else{
                            echo "<p class='note'>No images found.</p>";
                        }
                        ?>
                    </div>
                    <p class="note">Click × to remove old images. Changes will be saved on Update.</p>
                </div>

                <div class="form-group">
                    <label>Upload New Images (Optional)</label>
                    <input type="file" name="images[]" multiple>
                    <p class="note">Leave empty if you don’t want to change images.</p>
                </div>

                <button type="submit" name="update_product" class="btn-save">
                     Update Product
                </button>

            </form>
        </div>

    </div>

</div>

<script>
$(document).ready(function(){
    $('.remove-img').click(function(){
        var wrapper = $(this).closest('.image-wrapper');
        wrapper.hide();
        wrapper.find('input[name="old_images[]"]').prop('disabled', true);
    });
});
</script>

</body>
</html>
