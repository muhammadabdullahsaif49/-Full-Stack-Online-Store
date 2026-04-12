<?php
session_start();

if(!isset($_SESSION['User_ID']) || $_SESSION['Role']!='buyer'){
    exit;
}

if(isset($_POST['checkout_items'])){
    $items = $_POST['checkout_items']; // array(pid => qty)
    $_SESSION['checkout_items'] = array_keys($items);
    $_SESSION['checkout_qty'] = $items;
}
?>
