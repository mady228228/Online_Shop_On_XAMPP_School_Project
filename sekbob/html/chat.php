<?php
require_login();
$auth = auth_user();
if (($auth['role'] ?? '') !== 'client') {
    echo '<div class="alert alert--warning">Only clients can use this chat.</div>';
    return;
}

$clientID = (int)$auth['user_id'];
$workerID = 0;

$row = db_query_one($conn, "SELECT workerID FROM orders WHERE clientID = ? ORDER BY date_of_order DESC LIMIT 1", 'i', [$clientID]);
if ($row) {
    $workerID = (int)$row['workerID'];
} else {
    $row2 = db_query_one(
        $conn,
        "SELECT workerID
         FROM workers
         ORDER BY CASE WHEN owner_status = 0 THEN 0 ELSE 1 END ASC, created_at ASC
         LIMIT 1"
    );
    $workerID = (int)($row2['workerID'] ?? 0);
}

if ($workerID <= 0) {
    echo '<div class="alert alert--warning">No workers in DB. Create a worker first.</div>';
    return;
}

$messages = db_query_all(
    $conn,
    "SELECT m.*, w.fullname
     FROM messages m
     JOIN workers w ON w.workerID = m.workerID
     WHERE m.clientID = ? AND m.workerID = ?
     ORDER BY m.created_at ASC
     LIMIT 200",
    'ii',
    [$clientID, $workerID]
);

$workerRow = db_query_one($conn, "SELECT fullname FROM workers WHERE workerID = ?", 'i', [$workerID]);
$workerName = isset($workerRow['fullname']) ? $workerRow['fullname'] : 'worker';
?>
<div class="panel">
  <h1 class="h1">chat</h1>
  <div class="muted">Support: <b><?= e($workerName) ?></b></div>

  <div class="card" style="margin-top:12px;">
    <div class="card__body" style="max-height:360px; overflow:auto; display:grid; gap:10px;">
      <?php if (!$messages): ?>
        <div class="muted">No messages yet. Write your first message below.</div>
      <?php endif; ?>

      <?php foreach ($messages as $m):
        $mine = ($m['sender_role'] === 'client');
      ?>
        <div style="display:flex; <?= $mine ? 'justify-content:flex-end' : '' ?>;">
          <div style="max-width:70%; padding:10px 12px; border-radius:16px; border:1px solid var(--border);
                      background: <?= $mine ? 'rgba(122,0,25,.10)' : 'rgba(0,0,0,.04)' ?>;">
            <div style="font-weight:800; color: var(--primary); font-size:12px;">
              <?= $mine ? 'you' : e($workerName) ?> • <?= e(date('H:i', strtotime($m['created_at']))) ?>
            </div>
            <div><?= e($m['body']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <form action="php/actions/chat_send.php" method="post" style="margin-top:12px; display:flex; gap:10px;">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="workerID" value="<?= $workerID ?>">
    <input class="input" name="body" placeholder="type message..." required style="border-radius:16px;">
    <button class="btn btn--primary" type="submit">Send</button>
  </form>
</div>
