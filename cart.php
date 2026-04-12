<?php
session_start();
include_once('db.php');

// ================== USER CHECK ==================
if(!isset($_SESSION['User_ID']) || $_SESSION['Role']!='buyer'){
    header("Location: login_page.php");
    exit;
}

$buyer_id = $_SESSION['User_ID'];

// ================== CART LOGIC ==================
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// ADD ITEM TO CART
if(isset($_GET['add'])){
    $pid = intval($_GET['add']);
    $_SESSION['cart'][$pid] = isset($_SESSION['cart'][$pid]) ? $_SESSION['cart'][$pid]+1 : 1;
    header("Location: cart.php");
    exit;
}

// REMOVE ITEM FROM CART
if(isset($_GET['remove'])){
    $pid = intval($_GET['remove']);
    unset($_SESSION['cart'][$pid]);
    header("Location: cart.php");
    exit;
}

// UPDATE QUANTITY (from AJAX)
if(isset($_POST['update_qty'])){
    $pid = intval($_POST['pid']);
    $qty = intval($_POST['qty']);

    // Fetch stock
    $res = mysqli_query($conn, "SELECT quantity FROM products WHERE product_id=$pid");
    $stock = mysqli_num_rows($res) ? intval(mysqli_fetch_assoc($res)['quantity']) : 0;

    if($qty <= 0){
        unset($_SESSION['cart'][$pid]);
    } else if($qty > $stock){
        $_SESSION['cart'][$pid] = $stock;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }
    echo json_encode(['status'=>'ok','qty'=>$_SESSION['cart'][$pid]]);
    exit;
}

// ================== FETCH CART PRODUCTS ==================
$cart_items = [];
$total = 0;
if(!empty($_SESSION['cart'])){
    $ids = implode(',', array_keys($_SESSION['cart']));
    $qry = mysqli_query($conn, "SELECT * FROM products WHERE product_id IN ($ids)");
    while($p = mysqli_fetch_assoc($qry)){
        $p['cart_qty'] = $_SESSION['cart'][$p['product_id']];
        $p['subtotal'] = $p['price'] * $p['cart_qty'];
        $total += $p['subtotal'];
        $cart_items[] = $p;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f1f3f6;
        margin: 0;
    }

    .container {
        max-width: 1000px;
        margin: 50px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #1f2937;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
    }

    th {
        background: #3b82f6;
        color: white;
    }

    img {
        width: 80px;
        border-radius: 8px;
    }

    .qty-controls {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-controls button {
        padding: 2px 8px;
        margin: 0 3px;
        border: none;
        background: #3b82f6;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }

    input[type=number] {
        width: 60px;
        text-align: center;
        border: 1px solid #d1d5db;
        border-radius: 5px;
        padding: 5px;
    }

    .checkout {
        background: #10b981;
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    .checkout:hover {
        background: #059669;
    }

    .remove {
        background: #ef4444;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
    }

    .remove:hover {
        background: #b91c1c;
    }

    .total {
        font-weight: 600;
        font-size: 18px;
        text-align: right;
        margin-top: 15px;
    }

    .continue,
    .shop-now {
        display: inline-block;
        background: #3b82f6;
        color: white;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        margin-top: 20px;
    }

    .continue:hover,
    .shop-now:hover {
        background: #2563eb;
    }

    p {
        color: #374151;
        font-size: 16px;
    }
    </style>
</head>

<body>

    <div class="container">
        <h2>My Cart 🛒</h2>

        <?php if(empty($cart_items)): ?>
        <p>Your cart is empty!</p>
        <a href="buyer_dashboard.php" class="shop-now">Shop Now</a>
        <?php else: ?>
        <form id="cartForm">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Remove</th>
                </tr>
                <?php foreach($cart_items as $item): 
                    $stock = intval($item['quantity']);
                    $qty = min($item['cart_qty'], $stock);
                ?>
                <tr data-price="<?php echo $item['price']; ?>" data-stock="<?php echo $stock; ?>"
                    data-pid="<?php echo $item['product_id']; ?>">
                    <td><input type="checkbox" class="itemCheck" name="checkout_items[]"
                            value="<?php echo $item['product_id']; ?>" checked></td>
                    <td><img
                            src="uploads/<?php echo !empty($item['images']) ? explode(',',$item['images'])[0]:'no-image.png'; ?>">
                    </td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td>Rs <span class="price"><?php echo $item['price']; ?></span></td>
                    <td>
                        <div class="qty-controls">
                            <button type="button" class="decBtn">-</button>
                            <input type="number" class="qtyInput" min="1" max="<?php echo $stock; ?>"
                                value="<?php echo $qty; ?>" <?php echo $stock==0 ? 'disabled':''; ?>>
                            <button type="button" class="incBtn">+</button>
                        </div>
                    </td>
                    <td>Rs <span class="subtotal"><?php echo $item['subtotal']; ?></span></td>
                    <td><a href="cart.php?remove=<?php echo $item['product_id']; ?>" class="remove">Remove</a></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <div class="total">Total: Rs <span id="totalAmount"><?php echo $total; ?></span></div>

            <div style="margin-top:15px; display:flex; justify-content:space-between;">
                <a href="buyer_dashboard.php" class="continue">← Continue Shopping</a>
                <button type="button" class="checkout" id="proceedCheckout">Proceed to Checkout 🛒</button>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function() {

        function updateTotals() {
            var total = 0;
            $('tr[data-price]').each(function() {
                var price = parseFloat($(this).data('price'));
                var stock = parseInt($(this).data('stock'));
                var qtyInput = $(this).find('.qtyInput');

                var qty = parseInt(qtyInput.val());
                if (isNaN(qty) || qty < 1) qty = 1;
                if (qty > stock) qty = stock;
                qtyInput.val(qty);

                if ($(this).find('.itemCheck').is(':checked')) {
                    var subtotal = price * qty;
                    $(this).find('.subtotal').text(subtotal.toLocaleString());
                    total += subtotal;
                } else {
                    $(this).find('.subtotal').text('0');
                }
            });
            $('#totalAmount').text(total.toLocaleString());
        }

        // Increment / Decrement Buttons
        $('.incBtn').click(function() {
            var row = $(this).closest('tr');
            var input = row.find('.qtyInput');
            var stock = parseInt(row.data('stock'));
            var qty = parseInt(input.val());
            if (qty < stock) {
                qty++;
                input.val(qty);
                updateCart(row.data('pid'), qty);
            }
            updateTotals();
        });

        $('.decBtn').click(function() {
            var row = $(this).closest('tr');
            var input = row.find('.qtyInput');
            var qty = parseInt(input.val());
            if (qty > 1) {
                qty--;
                input.val(qty);
                updateCart(row.data('pid'), qty);
            }
            updateTotals();
        });

        // Manual input
        $('.qtyInput').on('change', function() {
            var row = $(this).closest('tr');
            var stock = parseInt(row.data('stock'));
            var qty = parseInt($(this).val());
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > stock) qty = stock;
            $(this).val(qty);
            updateCart(row.data('pid'), qty);
            updateTotals();
        });

        function updateCart(pid, qty) {
            $.post('cart.php', {
                update_qty: 1,
                pid: pid,
                qty: qty
            }, function(res) {
                // console.log(res);
            }, 'json');
        }

        $('.itemCheck').on('change', updateTotals);
        updateTotals();

        // Checkout
        $('#proceedCheckout').click(function() {
            var selected = {};
            $('.itemCheck:checked').each(function() {
                var pid = $(this).val();
                var qty = parseInt($('tr[data-pid="' + pid + '"]').find('.qtyInput').val());
                selected[pid] = qty;
            });
            if (Object.keys(selected).length == 0) {
                alert("Please select at least one product!");
                return;
            }
            $.post('set_checkout_session.php', {
                checkout_items: selected
            }, function() {
                window.location.href = 'checkout.php';
            });
        });
    });
    </script>

</body>

</html>