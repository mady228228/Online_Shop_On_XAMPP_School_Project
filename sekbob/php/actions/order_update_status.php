<?php
require_once __DIR__ . '/../bootstrap.php';

require_worker();

if (!is_post()) {
    redirect(current_url('dashboard', ['tab' => 'orders']));
}

require_csrf();

$orderID = isset($_POST['orderID']) ? (int)$_POST['orderID'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : 'new';
$allowed = ['new', 'processing', 'completed', 'cancelled'];

if ($orderID <= 0 || !in_array($status, $allowed, true)) {
    flash_set('global', 'Invalid request.', 'warning');
    redirect(current_url('dashboard', ['tab' => 'orders']));
}

$auth = auth_user();
$workerID = (int)$auth['user_id'];
$errorMessage = '';

db_begin_transaction($conn);

if (!empty($auth['is_owner'])) {
    $order = db_query_one($conn, "SELECT * FROM orders WHERE orderID = ? FOR UPDATE", 'i', [$orderID]);
} else {
    $order = db_query_one($conn, "SELECT * FROM orders WHERE orderID = ? AND workerID = ? FOR UPDATE", 'ii', [$orderID, $workerID]);
}

if (!$order) {
    $errorMessage = 'Order not found or not yours.';
}

if ($errorMessage === '') {
    $prev = $order['status'];
    $ok = db_execute($conn, "UPDATE orders SET status = ? WHERE orderID = ?", 'si', [$status, $orderID]);

    if (!$ok) {
        $errorMessage = db_error($conn);
    }

    if ($errorMessage === '' && $status === 'completed' && $prev !== 'completed') {
        $percent = (float)($config['site']['worker_bonus_percent'] ?? 2);
        $bonus = ((float)$order['cost_of_order']) * $percent / 100;

        $okBonus = db_execute(
            $conn,
            "UPDATE workers SET bonus = bonus + ? WHERE workerID = ?",
            'di',
            [$bonus, (int)$order['workerID']]
        );

        if (!$okBonus) {
            $errorMessage = db_error($conn);
        }
    }
}

if ($errorMessage !== '') {
    db_rollback_transaction($conn);
    flash_set('global', 'Error: ' . $errorMessage, 'danger');
    redirect(current_url('dashboard', ['tab' => 'orders']));
}

db_commit_transaction($conn);
flash_set('global', 'Order updated.', 'success');
redirect(current_url('dashboard', ['tab' => 'orders']));
