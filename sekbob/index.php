<?php
require_once __DIR__ . '/php/bootstrap.php';

$routes = [
    'home' => 'html/home.php',
    'product' => 'html/product.php',
    'cart' => 'html/cart.php',
    'checkout' => 'html/checkout.php',
    'my_orders' => 'html/my_orders.php',
    'login' => 'html/login.php',
    'register_user' => 'html/register_user.php',
    'register_worker' => 'html/register_worker.php',
    'dashboard' => 'html/dashboard.php',
    'statistics' => 'html/statistics.php',
    'chat' => 'html/chat.php',
    'chat_admin' => 'html/chat_admin.php',
    'about' => 'html/about.php',
    'contact' => 'html/contact.php',
];

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
if (!array_key_exists($page, $routes)) {
    $page = 'home';
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/' . $routes[$page];
require __DIR__ . '/includes/footer.php';
