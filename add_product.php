<?php
include_once('db.php');
session_start();
if(!isset($_SESSION['User_ID']) || $_SESSION['Role']!='seller'){
    header("Location: login_page.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>
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
    margin-bottom:30px;
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
    padding:35px;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.form-card h3{
    margin-bottom:25px;
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
    padding:12px 14px;
    border:1px solid #d1d5db;
    border-radius:10px;
    font-size:14px;
}

.form-group input:focus,
.form-group textarea:focus{
    outline:none;
    border-color:#6366f1;
}

.form-group textarea{
    height:110px;
    resize:none;
}

.form-row{
    display:flex;
    gap:15px;
}

.form-row .form-group{
    flex:1;
}

.form-group small{
    color:#6b7280;
    font-size:12px;
}

/* ===== BUTTON ===== */
.btn-save{
    width:100%;
    background:#6366f1;
    color:white;
    padding:14px;
    border:none;
    border-radius:14px;
    font-size:16px;
    font-weight:500;
    cursor:pointer;
    transition:.3s;
}

.btn-save:hover{
    background:#4f46e5;
}
</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Seller Panel</h2>
        <a href="seller_dashboard.php">Dashboard</a>
        <a href="add_product.php" class="active">Add Product</a>
        <a href="seller_products.php">My Products</a>
        <a href="view_orders.php">Orders</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="header">
            <h3>Add Product</h3>
            <span><?php echo $_SESSION['Username']; ?></span>
        </div>

        <div class="form-card">
            <h3>New Product Information</h3>

            <form action="insert_product.php" method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (Rs)</label>
                        <input type="number" name="price" required>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Product Images</label>
                    <input type="file" name="images[]" multiple required>
                    <small>You can upload single or multiple images</small>
                </div>

                <button type="submit" name="add_product" class="btn-save">
                    Add Product
                </button>

            </form>
        </div>

    </div>
</div>

</body>
</html>
