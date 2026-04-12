<?php
session_start();
include_once('db.php');

/* ================= USER CHECK ================= */
if(!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'seller'){
    header("Location: login_page.php");
    exit;
}

$seller_id = $_SESSION['User_ID'];

/* ================= HANDLE STATUS UPDATE ================= */
if(isset($_GET['id']) && isset($_GET['status'])){
    $order_id = intval($_GET['id']);
    $status   = strtolower($_GET['status']);

    $allowed = ['confirmed','cancelled'];

    if(in_array($status, $allowed)){

        // Update order status
        $update = mysqli_query($conn,
            "UPDATE orders 
             SET status='$status' 
             WHERE order_id='$order_id' 
             AND seller_id='$seller_id'"
        );

        if($update){

            // If cancelled → return stock
            if($status == 'cancelled'){
                $order_q = mysqli_query($conn,
                    "SELECT o.product_id, o.quantity AS order_qty, p.quantity AS stock_qty
                     FROM orders o
                     JOIN products p ON o.product_id = p.product_id
                     WHERE o.order_id='$order_id'"
                );

                if($order = mysqli_fetch_assoc($order_q)){
                    $new_qty = $order['stock_qty'] + $order['order_qty'];
                    mysqli_query($conn,
                        "UPDATE products 
                         SET quantity='$new_qty' 
                         WHERE product_id='{$order['product_id']}'"
                    );
                }
            }

            $_SESSION['message'] = "Order #$order_id updated to ".ucfirst($status);
            $_SESSION['message_type'] = "success";

        } else {
            $_SESSION['message'] = "Update failed: ".mysqli_error($conn);
            $_SESSION['message_type'] = "error";
        }

    } else {
        $_SESSION['message'] = "Invalid status!";
        $_SESSION['message_type'] = "error";
    }

    header("Location: view_orders.php");
    exit;
}

/* ================= FETCH ORDERS ================= */
$orders = mysqli_query($conn,
    "SELECT o.*, p.product_name
     FROM orders o
     LEFT JOIN products p ON o.product_id = p.product_id
     WHERE o.seller_id='$seller_id'
     ORDER BY o.order_id DESC"
);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Seller Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    /* ================= STYLES ================ */
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

    .wrapper {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 240px;
        background: #fff;
        border-right: 1px solid #e5e7eb;
        padding: 25px 20px;
    }

    .sidebar h2 {
        text-align: center;
        color: #6366f1;
        margin-bottom: 30px;
    }

    .sidebar a {
        display: block;
        padding: 12px 15px;
        margin-bottom: 10px;
        text-decoration: none;
        color: #374151;
        border-radius: 10px;
        font-weight: 500;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: #6366f1;
        color: #fff;
    }

    .content {
        flex: 1;
        padding: 35px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .header h3 {
        font-size: 26px;
    }

    .header span {
        color: #6b7280;
    }

    .message {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 10px;
        text-align: center;
        font-weight: 500;
    }

    .success {
        background: #d1fae5;
        color: #065f46;
    }

    .error {
        background: #fee2e2;
        color: #b91c1c;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    table th,
    table td {
        padding: 15px;
        border-bottom: 1px solid #e5e7eb;
    }

    table th {
        background: #6366f1;
        color: #fff;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .pending {
        background: #fef3c7;
        color: #92400e;
    }

    .confirmed {
        background: #d1fae5;
        color: #065f46;
    }

    .cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .btn {
        padding: 6px 12px;
        border-radius: 8px;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-confirm {
        background: #10b981;
    }

    .btn-cancel {
        background: #ef4444;
    }
    </style>
</head>

<body>

    <div class="wrapper">

        <div class="sidebar">
            <h2>Seller Panel</h2>
            <a href="seller_dashboard.php">Dashboard</a>
            <a href="add_product.php">Add Product</a>
            <a href="seller_products.php">My Products</a>
            <a href="view_orders.php" class="active">Orders</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="content">
            <div class="header">
                <h3>Orders</h3>
                <span><?php echo $_SESSION['Username']; ?></span>
            </div>

            <?php
    if(isset($_SESSION['message'])){
        echo "<div class='message {$_SESSION['message_type']}'>{$_SESSION['message']}</div>";
        unset($_SESSION['message'], $_SESSION['message_type']);
    }
    ?>

            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Buyer ID</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>

                <?php if(mysqli_num_rows($orders)>0): while($row=mysqli_fetch_assoc($orders)): ?>
                <tr>
                    <td>#<?php echo $row['order_id']; ?></td>

                    <!-- ✅ FIXED PART -->
                    <td>
                        <?php 
                echo $row['product_name'] 
                ? htmlspecialchars($row['product_name']) 
                : "<em style='color:#9ca3af;'>Product Deleted</em>";
            ?>
                    </td>

                    <td>#<?php echo $row['buyer_id']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><span class="badge <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span>
                    </td>
                    <td><?php echo date('d-m-Y H:i', strtotime($row['order_date'])); ?></td>
                    <td>
                        <?php if($row['status']=='pending'): ?>
                        <a class="btn btn-confirm"
                            href="view_orders.php?id=<?php echo $row['order_id']; ?>&status=confirmed">Confirm</a>
                        <a class="btn btn-cancel"
                            href="view_orders.php?id=<?php echo $row['order_id']; ?>&status=cancelled">Cancel</a>
                        <?php else: echo ucfirst($row['status']); endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No orders found</td>
                </tr>
                <?php endif; ?>
            </table>

        </div>
    </div>

</body>

</html>