<?php
require_worker();
$auth = auth_user();
$workerID = (int)$auth['user_id'];

if (!empty($auth['is_owner'])) {
    $chats = db_query_all(
        $conn,
        "SELECT m.clientID, c.nickname, MAX(m.created_at) AS last_time
         FROM messages m
         JOIN clients c ON c.clientID = m.clientID
         GROUP BY m.clientID
         ORDER BY last_time DESC
         LIMIT 50"
    );
} else {
    $chats = db_query_all(
        $conn,
        "SELECT m.clientID, c.nickname, MAX(m.created_at) AS last_time
         FROM messages m
         JOIN clients c ON c.clientID = m.clientID
         WHERE m.workerID = ?
         GROUP BY m.clientID
         ORDER BY last_time DESC
         LIMIT 50",
        'i',
        [$workerID]
    );
}

$defaultClient = isset($chats[0]['clientID']) ? (int)$chats[0]['clientID'] : 0;
$selectedClient = isset($_GET['client']) ? (int)$_GET['client'] : $defaultClient;

$messages = [];
$clientName = '';
if ($selectedClient > 0) {
    if (!empty($auth['is_owner'])) {
        $row = db_query_one($conn, "SELECT workerID FROM messages WHERE clientID = ? ORDER BY created_at DESC LIMIT 1", 'i', [$selectedClient]);
        $targetWorker = isset($row['workerID']) ? (int)$row['workerID'] : $workerID;
    } else {
        $targetWorker = $workerID;
    }

    $messages = db_query_all(
        $conn,
        "SELECT m.*, c.nickname
         FROM messages m
         JOIN clients c ON c.clientID = m.clientID
         WHERE m.clientID = ? AND m.workerID = ?
         ORDER BY m.created_at ASC
         LIMIT 200",
        'ii',
        [$selectedClient, $targetWorker]
    );

    if (!empty($messages[0]['nickname'])) {
        $clientName = $messages[0]['nickname'];
    }
}
?>
<div class="panel">
  <h1 class="h1">chat</h1>

  <div class="row">
    <div class="panel">
      <h2 style="margin-top:0;">clients</h2>
      <?php if (!$chats): ?>
        <div class="alert alert--info">No chats yet.</div>
      <?php else: ?>
        <div style="display:grid; gap:10px;">
          <?php foreach ($chats as $c): ?>
            <a class="btn <?= (int)$c['clientID'] === $selectedClient ? 'btn--primary' : 'btn--ghost' ?>"
               href="<?= e(current_url('chat_admin', ['client' => (int)$c['clientID']])) ?>">
              <?= e($c['nickname']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="panel">
      <h2 style="margin-top:0;">conversation <?= $clientName ? 'with ' . e($clientName) : '' ?></h2>

      <div class="card">
        <div class="card__body" style="max-height:360px; overflow:auto; display:grid; gap:10px;">
          <?php if (!$messages): ?>
            <div class="muted">Select a client to view messages.</div>
          <?php endif; ?>
          <?php foreach ($messages as $m):
            $mine = ($m['sender_role'] === 'worker');
          ?>
            <div style="display:flex; <?= $mine ? 'justify-content:flex-end' : '' ?>;">
              <div style="max-width:70%; padding:10px 12px; border-radius:16px; border:1px solid var(--border);
                          background: <?= $mine ? 'rgba(122,0,25,.10)' : 'rgba(0,0,0,.04)' ?>;">
                <div style="font-weight:800; color: var(--primary); font-size:12px;">
                  <?= $mine ? 'you' : e($m['nickname']) ?> • <?= e(date('H:i', strtotime($m['created_at']))) ?>
                </div>
                <div><?= e($m['body']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if ($selectedClient > 0): ?>
        <form action="php/actions/chat_reply.php" method="post" style="margin-top:12px; display:flex; gap:10px;">
          <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="clientID" value="<?= $selectedClient ?>">
          <input class="input" name="body" placeholder="type reply..." required style="border-radius:16px;">
          <button class="btn btn--primary" type="submit">Send</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
