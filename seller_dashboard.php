<?php
session_start();
include_once('db.php');

if(!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'seller'){
    header("Location: login_page.php");
    exit;
}

$seller_id = $_SESSION['User_ID'];

$totalProducts = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM products WHERE seller_id='$seller_id'")
)['total'];

$pendingOrders = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM orders WHERE seller_id='$seller_id' AND status='pending'")
)['total'];

$confirmedOrders = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM orders WHERE seller_id='$seller_id' AND status='confirmed'")
)['total'];

$recentProducts = mysqli_query($conn,"SELECT * FROM products WHERE seller_id='$seller_id' ORDER BY product_id DESC LIMIT 6");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Seller Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Poppins;
    }

    body {
        background: #f6f9ff;
        color: #1f2937;
    }

    /* ===== LAYOUT ===== */
    .wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
        width: 240px;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        padding: 25px 20px;
    }

    .sidebar h2 {
        color: #6366f1;
        margin-bottom: 30px;
        text-align: center;
    }

    .sidebar a {
        display: block;
        padding: 12px 15px;
        margin-bottom: 10px;
        text-decoration: none;
        color: #374151;
        border-radius: 10px;
        font-weight: 500;
        transition: .3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: #6366f1;
        color: white;
    }

    /* ===== CONTENT ===== */
    .content {
        flex: 1;
        padding: 35px;
    }

    /* ===== HEADER ===== */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .header h3 {
        font-size: 26px;
    }

    .header span {
        color: #6b7280;
    }

    /* ===== STATS ===== */
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 35px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
    }

    .stat-card p {
        color: #6b7280;
        margin-bottom: 10px;
    }

    .stat-card h2 {
        font-size: 32px;
    }

    .blue {
        border-left: 6px solid #6366f1;
    }

    .orange {
        border-left: 6px solid #f59e0b;
    }

    .green {
        border-left: 6px solid #10b981;
    }

    /* ===== QUICK ACTION ===== */
    .quick {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        margin-bottom: 35px;
    }

    .quick a {
        display: inline-block;
        margin-right: 15px;
        margin-top: 10px;
        padding: 12px 25px;
        background: #6366f1;
        color: white;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 500;
        transition: .3s;
    }

    .quick a:hover {
        background: #4f46e5;
    }

    /* ===== PRODUCTS ===== */
    .products {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        transition: .3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .product-card .info {
        padding: 15px;
    }

    .product-card h4 {
        font-size: 17px;
        margin-bottom: 6px;
    }

    .product-card p {
        font-size: 14px;
        color: #6b7280;
    }

    .actions {
        display: flex;
        gap: 10px;
        padding: 15px;
    }

    .actions a {
        flex: 1;
        text-align: center;
        padding: 8px 0;
        border-radius: 10px;
        text-decoration: none;
        color: white;
        font-weight: 500;
    }

    .edit {
        background: #10b981;
    }

    .delete {
        background: #ef4444;
    }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>Seller Panel</h2>
            <a href="seller_dashboard.php" class="active">Dashboard</a>
            <a href="add_product.php">Add Product</a>
            <a href="seller_products.php">My Products</a>
            <a href="view_orders.php">Orders</a>
            <a href="logout.php">Logout</a>
        </div>

        <!-- CONTENT -->
        <div class="content">

            <div class="header">
                <h3>Dashboard</h3>
                <span>Welcome, <?php echo $_SESSION['Username']; ?></span>
            </div>

            <div class="stats">
                <div class="stat-card blue">
                    <p>Total Products</p>
                    <h2><?php echo $totalProducts; ?></h2>
                </div>
                <div class="stat-card orange">
                    <p>Pending Orders</p>
                    <h2><?php echo $pendingOrders; ?></h2>
                </div>
                <div class="stat-card green">
                    <p>Confirmed Orders</p>
                    <h2><?php echo $confirmedOrders; ?></h2>
                </div>
            </div>

            <div class="quick">
                <h3>Quick Actions</h3>
                <a href="add_product.php">Add Product</a>
                <a href="view_orders.php">Manage Orders</a>
            </div>

            <h3 style="margin-bottom:15px;">Recent Products</h3>
            <div class="products">
                <?php while($p=mysqli_fetch_assoc($recentProducts)): 
                $img = explode(',',$p['images'])[0] ?? 'no-image.png';
            ?>
                <div class="product-card">
                    <img src="uploads/<?php echo $img; ?>">
                    <div class="info">
                        <h4><?php echo $p['product_name']; ?></h4>
                        <p>Rs <?php echo $p['price']; ?></p>
                        <p>Qty: <?php echo $p['quantity']; ?></p>
                    </div>
                    <div class="actions">
                        <a href="edit_product.php?id=<?php echo $p['product_id']; ?>" class="edit">Edit</a>
                        <a href="delete_product.php?id=<?php echo $p['product_id']; ?>" class="delete">Delete</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

        </div>
    </div>

</body>

</html>