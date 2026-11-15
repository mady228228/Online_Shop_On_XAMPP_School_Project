<?php
require_once __DIR__ . '/../bootstrap.php';

require_login();
require_csrf();

$auth = auth_user();
if (($auth['role'] ?? '') !== 'client') {
    flash_set('global', 'Only clients can send here.', 'warning');
    redirect(current_url('home'));
}

$workerID = isset($_POST['workerID']) ? (int)$_POST['workerID'] : 0;
$body = trim(isset($_POST['body']) ? $_POST['body'] : '');

if ($workerID <= 0 || $body === '') {
    redirect(current_url('chat'));
}

$ok = db_execute(
    $conn,
    "INSERT INTO messages (clientID, workerID, sender_role, body) VALUES (?, ?, 'client', ?)",
    'iis',
    [(int)$auth['user_id'], $workerID, $body]
);

if (!$ok) {
    flash_set('global', 'Message was not sent: ' . db_error($conn), 'danger');
}

redirect(current_url('chat'));
