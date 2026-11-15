<?php
require_once __DIR__ . '/../bootstrap.php';

require_worker();
require_csrf();

$clientID = isset($_POST['clientID']) ? (int)$_POST['clientID'] : 0;
$body = trim(isset($_POST['body']) ? $_POST['body'] : '');

if ($clientID <= 0 || $body === '') {
    redirect(current_url('chat_admin'));
}

$auth = auth_user();
$workerID = (int)$auth['user_id'];

if (!empty($auth['is_owner'])) {
    $row = db_query_one($conn, "SELECT workerID FROM messages WHERE clientID = ? ORDER BY created_at DESC LIMIT 1", 'i', [$clientID]);
    if ($row) {
        $workerID = (int)$row['workerID'];
    }
}

$ok = db_execute(
    $conn,
    "INSERT INTO messages (clientID, workerID, sender_role, body) VALUES (?, ?, 'worker', ?)",
    'iis',
    [$clientID, $workerID, $body]
);

if (!$ok) {
    flash_set('global', 'Message was not sent: ' . db_error($conn), 'danger');
}

redirect(current_url('chat_admin', ['client' => $clientID]));
