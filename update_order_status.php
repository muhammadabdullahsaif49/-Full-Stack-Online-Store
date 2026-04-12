<?php
// update_order_status.php
session_start();
include_once('db.php');

if(!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'seller'){
    header("Location: login_page.php");
    exit;
}

if(isset($_GET['id']) && isset($_GET['status'])){

    $seller_id = $_SESSION['User_ID'];
    $order_id  = intval($_GET['id']);
    $status    = $_GET['status'];
    
    // Validate status
    $allowed_status = ['pending', 'confirmed', 'cancelled'];
    if(!in_array($status, $allowed_status)) {
        $_SESSION['message'] = "Invalid status!";
        $_SESSION['message_type'] = "error";
        header("Location: view_orders.php");
        exit;
    }

    // First check if order belongs to this seller
    $check_query = mysqli_query($conn, 
        "SELECT * FROM orders WHERE id='$order_id' AND seller_id='$seller_id'");
    
    if(mysqli_num_rows($check_query) == 0) {
        $_SESSION['message'] = "Order not found or you don't have permission!";
        $_SESSION['message_type'] = "error";
        header("Location: view_orders.php");
        exit;
    }
    
    $order = mysqli_fetch_assoc($check_query);

    // Update query with seller check
    $query = "UPDATE orders SET status='$status' WHERE id='$order_id' AND seller_id='$seller_id'";
    $result = mysqli_query($conn, $query);
    
    if($result) {
        // If order was cancelled, restore product quantity
        if($status == 'cancelled' && $order['status'] != 'cancelled') {
            // Get current product quantity
            $product_query = mysqli_query($conn, 
                "SELECT quantity FROM products WHERE product_id='{$order['product_id']}'");
            
            if($product = mysqli_fetch_assoc($product_query)) {
                $new_quantity = $product['quantity'] + $order['qty'];
                mysqli_query($conn, 
                    "UPDATE products SET quantity='$new_quantity' 
                     WHERE product_id='{$order['product_id']}'");
            }
        }
        // If order was confirmed from pending, reduce stock (if not already reduced)
        elseif($status == 'confirmed' && $order['status'] == 'pending') {
            $product_query = mysqli_query($conn, 
                "SELECT quantity FROM products WHERE product_id='{$order['product_id']}'");
            
            if($product = mysqli_fetch_assoc($product_query)) {
                $new_quantity = $product['quantity'] - $order['qty'];
                if($new_quantity < 0) $new_quantity = 0;
                mysqli_query($conn, 
                    "UPDATE products SET quantity='$new_quantity' 
                     WHERE product_id='{$order['product_id']}'");
            }
        }
        
        $_SESSION['message'] = "Order #$order_id status updated to " . ucfirst($status) . " successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating order: " . mysqli_error($conn);
        $_SESSION['message_type'] = "error";
    }

    header("Location: view_orders.php");
    exit;
} else {
    header("Location: view_orders.php");
    exit;
}

mysqli_close($conn);
?>