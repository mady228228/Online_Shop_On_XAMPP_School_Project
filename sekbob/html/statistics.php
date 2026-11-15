<?php
require_owner();

$totalSalesRow = db_query_one($conn, "SELECT COALESCE(SUM(cost_of_order),0) AS s FROM orders WHERE status='completed'");
$totalOrdersRow = db_query_one($conn, "SELECT COUNT(*) AS c FROM orders");
$newOrdersRow = db_query_one($conn, "SELECT COUNT(*) AS c FROM orders WHERE status='new'");

$totalSales = (float)($totalSalesRow['s'] ?? 0);
$totalOrders = (int)($totalOrdersRow['c'] ?? 0);
$newOrders = (int)($newOrdersRow['c'] ?? 0);

$topGoods = db_query_all(
    $conn,
    "SELECT g.name, g.type, SUM(o.quantity) AS qty, SUM(o.cost_of_order) AS revenue
     FROM orders o
     JOIN goods g ON g.goodID = o.goodID
     WHERE o.status='completed'
     GROUP BY o.goodID
     ORDER BY revenue DESC
     LIMIT 10"
);

$byWorker = db_query_all(
    $conn,
    "SELECT w.fullname, w.owner_status, w.bonus,
            COUNT(o.orderID) AS orders_cnt,
            SUM(CASE WHEN o.status='completed' THEN o.cost_of_order ELSE 0 END) AS revenue
     FROM workers w
     LEFT JOIN orders o ON o.workerID = w.workerID
     GROUP BY w.workerID
     ORDER BY revenue DESC"
);
?>
<div class="panel">
  <h1 class="h1">see statistic</h1>

  <div class="grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card"><div class="card__body">
      <div class="muted">Total sales (completed)</div>
      <div style="font-size:26px; font-weight:900; color:var(--primary);"><?= number_format($totalSales, 0, '.', ' ') ?> ₸</div>
    </div></div>

    <div class="card"><div class="card__body">
      <div class="muted">Total orders</div>
      <div style="font-size:26px; font-weight:900; color:var(--primary);"><?= $totalOrders ?></div>
    </div></div>

    <div class="card"><div class="card__body">
      <div class="muted">New orders</div>
      <div style="font-size:26px; font-weight:900; color:var(--primary);"><?= $newOrders ?></div>
    </div></div>
  </div>

  <div class="row" style="margin-top:16px;">
    <div class="panel">
      <h2 style="margin-top:0;">Top goods</h2>
      <?php if (!$topGoods): ?>
        <div class="alert alert--info">No completed orders yet.</div>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr><th>Name</th><th>Type</th><th class="right">Qty</th><th class="right">Revenue</th></tr>
          </thead>
          <tbody>
            <?php foreach ($topGoods as $g): ?>
              <tr>
                <td><b><?= e($g['name']) ?></b></td>
                <td><?= e($g['type']) ?></td>
                <td class="right"><?= (int)$g['qty'] ?></td>
                <td class="right"><?= number_format((float)$g['revenue'], 0, '.', ' ') ?> ₸</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <div class="panel">
      <h2 style="margin-top:0;">Workers</h2>
      <table class="table">
        <thead>
          <tr><th>Name</th><th>Role</th><th class="right">Orders</th><th class="right">Revenue</th><th class="right">Bonus</th></tr>
        </thead>
        <tbody>
          <?php foreach ($byWorker as $w): ?>
            <tr>
              <td><b><?= e($w['fullname']) ?></b></td>
              <td><?= (int)$w['owner_status'] ? 'owner' : 'worker' ?></td>
              <td class="right"><?= (int)$w['orders_cnt'] ?></td>
              <td class="right"><?= number_format((float)$w['revenue'], 0, '.', ' ') ?> ₸</td>
              <td class="right"><?= number_format((float)$w['bonus'], 0, '.', ' ') ?> ₸</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="muted" style="margin-top:10px;">
        Bonus is added automatically when an order status becomes <b>completed</b> (<?= (int)($config['site']['worker_bonus_percent'] ?? 2) ?>% of order cost).
      </div>
    </div>
  </div>

  <div style="margin-top:14px;">
    <a class="btn btn--ghost" href="<?= e(current_url('dashboard')) ?>">Back to dashboard</a>
  </div>
</div>
