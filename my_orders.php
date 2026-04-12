<?php
session_start();
include_once('db.php');

// ================= USER CHECK =================
if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] !== 'buyer') {
    header("Location: login_page.php");
    exit;
}

$buyer_id = $_SESSION['User_ID'];

// ================= FETCH ORDERS =================
$sql = "
SELECT 
    o.order_id,
    o.product_id,
    o.quantity,
    o.status,
    o.order_date,
    o.total_price,
    p.product_name,
    p.price,
    p.images
FROM orders o
INNER JOIN products p ON o.product_id = p.product_id
WHERE o.buyer_id = '$buyer_id'
ORDER BY o.order_id DESC
";

$result = mysqli_query($conn, $sql);

$orders = [];
$grand_total = 0;

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
        $grand_total += $row['total_price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    body {
        background: #f1f5f9;
        font-family: Poppins, sans-serif;
    }

    .container {
        max-width: 1200px;
        margin: 30px auto;
    }

    .header {
        background: #fff;
        padding: 20px 30px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
    }

    .header h1 {
        color: #1e293b;
    }

    .back-btn {
        background: #4f46e5;
        color: #fff;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
    }

    .card {
        background: #fff;
        margin-top: 25px;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, .08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #4f46e5;
        color: #fff;
        padding: 15px;
        text-align: left;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #e5e7eb;
        color: #334155;
    }

    tr:hover {
        background: #f8fafc;
    }

    .product {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product img {
        width: 55px;
        height: 55px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }

    .status {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-confirmed {
        background: #dcfce7;
        color: #166534;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .total {
        font-weight: 700;
        color: #16a34a;
    }

    .summary {
        margin-top: 25px;
        background: #4f46e5;
        color: #fff;
        padding: 25px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .empty {
        text-align: center;
        padding: 60px;
        color: #64748b;
    }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h1><i class="fa-solid fa-bag-shopping"></i> My Orders</h1>
            <a href="buyer_dashboard.php" class="back-btn">Back to Shop</a>
        </div>

        <div class="card">
            <?php if(empty($orders)): ?>
            <div class="empty">
                <i class="fa-solid fa-cart-shopping" style="font-size:70px;"></i>
                <h2>No Orders Found</h2>
                <p>You haven't placed any orders yet.</p>
            </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $o):
                            $img = 'no-image.png';
                            if(!empty($o['images'])){
                                $imgs = explode(',', $o['images']);
                                $img = $imgs[0];
                            }
                            $status = strtolower($o['status']);
                        ?>
                    <tr>
                        <td>#<?= $o['order_id']; ?></td>
                        <td>
                            <div class="product">
                                <img src="uploads/<?= $img; ?>" onerror="this.src='uploads/no-image.png'">
                                <?= htmlspecialchars($o['product_name']); ?>
                            </div>
                        </td>
                        <td>Rs <?= number_format($o['price'],2); ?></td>
                        <td><?= $o['quantity']; ?></td>
                        <td class="total">Rs <?= number_format($o['total_price'],2); ?></td>
                        <td>
                            <span class="status status-<?= $status; ?>">
                                <?= ucfirst($o['status']); ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($o['order_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary">
                <h2>Total Spent</h2>
                <h1>Rs <?= number_format($grand_total,2); ?></h1>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php mysqli_close($conn); ?>