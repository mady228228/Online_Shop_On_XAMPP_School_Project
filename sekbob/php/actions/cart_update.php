<?php
require_once __DIR__ . '/../bootstrap.php';

if (!is_post()) {
    redirect(current_url('cart'));
}

require_csrf();

$goodID = isset($_POST['goodID']) ? (int)$_POST['goodID'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($goodID <= 0) {
    redirect(current_url('cart'));
}

if ($qty < 1) {
    $qty = 1;
}

$row = db_query_one($conn, "SELECT count FROM goods WHERE goodID = ?", 'i', [$goodID]);
$max = (int)($row['count'] ?? 0);

if ($max <= 0) {
    unset($_SESSION['cart'][$goodID]);
    flash_set('global', 'Product is sold out, removed from cart.', 'warning');
    redirect(current_url('cart'));
}

if ($qty > $max) {
    $qty = $max;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$_SESSION['cart'][$goodID] = $qty;

flash_set('global', 'Cart updated.', 'success');
redirect(current_url('cart'));
