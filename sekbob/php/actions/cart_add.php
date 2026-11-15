<?php
require_once __DIR__ . '/../bootstrap.php';

if (!is_post()) {
    redirect(current_url('home'));
}

require_csrf();

$goodID = isset($_POST['goodID']) ? (int)$_POST['goodID'] : 0;
if ($goodID <= 0) {
    redirect(current_url('home'));
}

$row = db_query_one($conn, "SELECT count FROM goods WHERE goodID = ?", 'i', [$goodID]);
if (!$row) {
    flash_set('global', 'Product not found.', 'warning');
    redirect(current_url('home'));
}

if ((int)$row['count'] <= 0) {
    flash_set('global', 'Product is sold out.', 'warning');
    redirect(current_url('home'));
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$_SESSION['cart'][$goodID] = isset($_SESSION['cart'][$goodID]) ? (int)$_SESSION['cart'][$goodID] + 1 : 1;

flash_set('global', 'Added to cart.', 'success');
redirect(current_url('cart'));
