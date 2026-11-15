<?php
require_login();
$auth = auth_user();
if (($auth['role'] ?? '') !== 'client') {
    echo '<div class="alert alert--warning">Only clients can view this page.</div>';
    return;
}

$orders = db_query_all(
    $conn,
    "SELECT o.orderID, o.date_of_order, o.cost_of_order, o.status,
            g.name AS good_name, g.type AS good_type, o.quantity,
            w.fullname AS worker_name
     FROM orders o
     JOIN goods g ON g.goodID = o.goodID
     JOIN workers w ON w.workerID = o.workerID
     WHERE o.clientID = ?
     ORDER BY o.date_of_order DESC",
    'i',
    [(int)$auth['user_id']]
);
?>
<div class="panel">
  <h1 class="h1">my orders</h1>

  <?php if (!$orders): ?>
    <div class="alert alert--info">No orders yet.</div>
    <a class="btn btn--ghost" href="<?= e(current_url('home')) ?>">Go to store</a>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Product</th>
          <th>Worker</th>
          <th>Status</th>
          <th class="right">Cost</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= (int)$o['orderID'] ?></td>
            <td><?= e(date('Y-m-d H:i', strtotime($o['date_of_order']))) ?></td>
            <td>
              <div><b><?= e($o['good_name']) ?></b> × <?= (int)$o['quantity'] ?></div>
              <div class="muted"><?= e($o['good_type']) ?></div>
            </td>
            <td><?= e($o['worker_name']) ?></td>
            <td><span class="badge"><?= e($o['status']) ?></span></td>
            <td class="right"><?= number_format((float)$o['cost_of_order'], 0, '.', ' ') ?> ₸</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
