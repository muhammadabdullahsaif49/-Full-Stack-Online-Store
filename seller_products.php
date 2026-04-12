<?php
session_start();
include_once('db.php');

if(!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'seller'){
    header("Location: login_page.php");
    exit;
}

$seller_id = $_SESSION['User_ID'];
$products = mysqli_query($conn, "SELECT * FROM products WHERE seller_id='$seller_id'");
if(!$products){
    die("Query Failed: ".mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>My Products</title>
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

/* ===== PRODUCT TABLE ===== */
.table-card{
    background:white;
    border-radius:18px;
    padding:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th, table td{
    padding:15px;
    text-align:left;
    border-bottom:1px solid #e5e7eb;
}

table th{
    background:#6366f1;
    color:white;
    border-radius:8px;
}

table td img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:10px;
}

/* ACTION BUTTONS */
.btn{
    padding:8px 14px;
    border:none;
    border-radius:10px;
    color:white;
    cursor:pointer;
    text-decoration:none;
    font-size:14px;
    font-weight:500;
    transition:.3s;
}

.btn-edit{
    background:#10b981;
}

.btn-edit:hover{
    background:#059669;
}

.btn-delete{
    background:#ef4444;
}

.btn-delete:hover{
    background:#b91c1c;
}

/* RESPONSIVE */
@media(max-width:900px){
    table th, table td{
        font-size:13px;
        padding:10px;
    }
}
</style>
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
            <h3>My Products</h3>
            <span><?php echo $_SESSION['Username']; ?></span>
        </div>

        <div class="table-card">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Images</th>
                    <th>Name</th>
                    <th>Price (Rs)</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
                <?php if(mysqli_num_rows($products) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><?php echo $row['product_id']; ?></td>
                        <td>
                            <?php 
                            $imgs = explode(",", $row['images']);
                            foreach($imgs as $img){
                                echo "<img src='uploads/$img' alt='product'>";
                            }
                            ?>
                        </td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <a class="btn btn-edit" href="edit_product.php?id=<?php echo $row['product_id']; ?>">Edit</a>
                            <a class="btn btn-delete" href="delete_product.php?id=<?php echo $row['product_id']; ?>" 
                            onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:20px;">No products found. <a href="add_product.php" style="color:#6366f1; text-decoration:underline;">Add a product</a></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

    </div>

</div>

</body>
</html>
