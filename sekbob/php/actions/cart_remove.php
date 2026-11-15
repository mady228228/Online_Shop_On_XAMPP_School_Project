<?php
require_once __DIR__ . '/../bootstrap.php';

$goodID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($goodID > 0 && !empty($_SESSION['cart'][$goodID])) {
    unset($_SESSION['cart'][$goodID]);
}

flash_set('global', 'Removed from cart.', 'info');
redirect(current_url('cart'));
