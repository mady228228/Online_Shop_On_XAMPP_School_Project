<?php
require_once __DIR__ . '/../bootstrap.php';

require_login();
require_csrf();

$auth = auth_user();
if (($auth['role'] ?? '') !== 'client') {
    flash_set('global', 'Only clients can create orders.', 'warning');
    redirect(current_url('home'));
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (!$cart) {
    flash_set('global', 'Cart is empty.', 'warning');
    redirect(current_url('cart'));
}

$worker = db_query_one(
    $conn,
    "SELECT w.workerID
     FROM workers w
     LEFT JOIN orders o ON o.workerID = w.workerID AND o.status IN ('new','processing')
     GROUP BY w.workerID, w.owner_status, w.created_at
     ORDER BY COUNT(o.orderID) ASC,
              CASE WHEN w.owner_status = 0 THEN 0 ELSE 1 END ASC,
              w.created_at ASC
     LIMIT 1"
);

$workerID = (int)($worker['workerID'] ?? 0);
if ($workerID <= 0) {
    flash_set('global', 'No workers in DB. Create a worker first.', 'danger');
    redirect(current_url('cart'));
}

$clientID = (int)$auth['user_id'];
$errorMessage = '';

db_begin_transaction($conn);

foreach ($cart as $goodID => $qty) {
    $goodID = (int)$goodID;
    $qty = (int)$qty;

    $g = db_query_one($conn, "SELECT cost, count FROM goods WHERE goodID = ? FOR UPDATE", 'i', [$goodID]);

    if (!$g) {
        $errorMessage = 'Good not found: ' . $goodID;
        break;
    }

    $stock = (int)$g['count'];
    if ($qty > $stock) {
        $errorMessage = 'Not enough stock for good #' . $goodID;
        break;
    }

    $cost = (float)$g['cost'];
    $orderCost = $cost * $qty;

    $okInsert = db_execute(
        $conn,
        "INSERT INTO orders (clientID, workerID, goodID, quantity, cost_of_order, status) VALUES (?, ?, ?, ?, ?, 'new')",
        'iiiid',
        [$clientID, $workerID, $goodID, $qty, $orderCost]
    );

    if (!$okInsert) {
        $errorMessage = db_error($conn);
        break;
    }

    $okUpdate = db_execute(
        $conn,
        "UPDATE goods SET count = count - ? WHERE goodID = ?",
        'ii',
        [$qty, $goodID]
    );

    if (!$okUpdate) {
        $errorMessage = db_error($conn);
        break;
    }
}

if ($errorMessage !== '') {
    db_rollback_transaction($conn);
    flash_set('global', 'Order failed: ' . $errorMessage, 'danger');
    redirect(current_url('checkout'));
}

db_commit_transaction($conn);
unset($_SESSION['cart']);
flash_set('global', 'Order created successfully! Worker assigned.', 'success');
redirect(current_url('my_orders'));
