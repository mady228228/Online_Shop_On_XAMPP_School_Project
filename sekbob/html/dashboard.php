<?php
require_worker();
$auth = auth_user();

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'orders';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!empty($auth['is_owner'])) {
    $orders = db_query_all(
        $conn,
        "SELECT o.*, c.nickname, c.phone_number, c.home_address, g.name AS good_name, g.type AS good_type, w.fullname AS worker_name
         FROM orders o
         JOIN clients c ON c.clientID = o.clientID
         JOIN goods g ON g.goodID = o.goodID
         JOIN workers w ON w.workerID = o.workerID
         ORDER BY o.status='new' DESC, o.date_of_order DESC"
    );
} else {
    $orders = db_query_all(
        $conn,
        "SELECT o.*, c.nickname, c.phone_number, c.home_address, g.name AS good_name, g.type AS good_type, w.fullname AS worker_name
         FROM orders o
         JOIN clients c ON c.clientID = o.clientID
         JOIN goods g ON g.goodID = o.goodID
         JOIN workers w ON w.workerID = o.workerID
         WHERE o.workerID = ?
         ORDER BY o.status='new' DESC, o.date_of_order DESC",
        'i',
        [(int)$auth['user_id']]
    );
}

$goods = db_query_all($conn, "SELECT * FROM goods ORDER BY created_at DESC");

$editGood = null;
if ($tab === 'goods' && $action === 'edit' && $editId > 0) {
    $editGood = db_query_one($conn, "SELECT * FROM goods WHERE goodID = ?", 'i', [$editId]);
}
?>
<div class="panel">
  <div class="tab">
    <a class="tab__btn <?= $tab === 'orders' ? 'tab__btn--active' : '' ?>" href="<?= e(current_url('dashboard', ['tab' => 'orders'])) ?>">orders</a>
    <a class="tab__btn <?= $tab === 'goods' ? 'tab__btn--active' : '' ?>" href="<?= e(current_url('dashboard', ['tab' => 'goods'])) ?>">add/update/delete goods</a>
    <?php if (!empty($auth['is_owner'])): ?>
      <a class="tab__btn <?= $tab === 'stats' ? 'tab__btn--active' : '' ?>" href="<?= e(current_url('statistics')) ?>">see statistic</a>
    <?php endif; ?>
  </div>

  <?php if ($tab === 'orders'): ?>
    <h1 class="h1">order</h1>

    <?php if (!$orders): ?>
      <div class="alert alert--info">No orders yet.</div>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Client</th>
            <th>Product</th>
            <th>Worker</th>
            <th>Status</th>
            <th class="right">Cost</th>
            <th class="right">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><?= (int)$o['orderID'] ?></td>
              <td>
                <div><b><?= e($o['nickname']) ?></b></div>
                <div class="muted"><?= e($o['phone_number']) ?> • <?= e($o['home_address']) ?></div>
              </td>
              <td>
                <div><b><?= e($o['good_name']) ?></b> × <?= (int)$o['quantity'] ?></div>
                <div class="muted"><?= e($o['good_type']) ?> • <?= e(date('Y-m-d H:i', strtotime($o['date_of_order']))) ?></div>
              </td>
              <td><?= e($o['worker_name']) ?></td>
              <td><span class="badge"><?= e($o['status']) ?></span></td>
              <td class="right"><?= number_format((float)$o['cost_of_order'], 0, '.', ' ') ?> ₸</td>
              <td class="right">
                <?php if ($o['status'] !== 'completed'): ?>
                  <form action="php/actions/order_update_status.php" method="post" style="display:inline-flex; gap:8px;">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="orderID" value="<?= (int)$o['orderID'] ?>">
                    <select class="input" name="status" style="padding:8px 12px; border-radius:12px; width:160px;">
                      <option value="new" <?= $o['status'] === 'new' ? 'selected' : '' ?>>new</option>
                      <option value="processing" <?= $o['status'] === 'processing' ? 'selected' : '' ?>>processing</option>
                      <option value="completed" <?= $o['status'] === 'completed' ? 'selected' : '' ?>>completed</option>
                      <option value="cancelled" <?= $o['status'] === 'cancelled' ? 'selected' : '' ?>>cancelled</option>
                    </select>
                    <button class="btn btn--ghost" type="submit">Save</button>
                  </form>
                <?php else: ?>
                  <span class="muted">Done</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($tab === 'goods'): ?>
    <h1 class="h1"><?= $editGood ? 'update' : 'add' ?></h1>

    <div class="row">
      <div class="panel">
        <form class="form" action="php/actions/goods_save.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="goodID" value="<?= $editGood ? (int)$editGood['goodID'] : 0 ?>">

          <div class="field">
            <label for="gname">name</label>
            <input class="input" id="gname" name="name" type="text" required value="<?= e($editGood['name'] ?? '') ?>">
          </div>

          <div class="field">
            <label for="gtype">type</label>
            <input class="input" id="gtype" name="type" type="text" required value="<?= e($editGood['type'] ?? '') ?>">
          </div>

          <div class="field">
            <label for="gcost">cost</label>
            <input class="input" id="gcost" name="cost" type="number" step="0.01" required value="<?= e((string)($editGood['cost'] ?? '')) ?>">
          </div>

          <div class="field">
            <label for="gcount">count</label>
            <input class="input" id="gcount" name="count" type="number" required value="<?= e((string)($editGood['count'] ?? 0)) ?>">
          </div>

          <div class="field">
            <label for="gdesc">description</label>
            <input class="input" id="gdesc" name="description" type="text" value="<?= e($editGood['description'] ?? '') ?>">
          </div>

          <div class="form__actions">
            <button class="btn btn--primary" type="submit"><?= $editGood ? 'update' : 'add' ?></button>
            <?php if ($editGood): ?>
              <a class="btn btn--ghost" href="<?= e(current_url('dashboard', ['tab' => 'goods'])) ?>">Cancel</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <div class="panel">
        <h2 style="margin-top:0;">goods list</h2>
        <?php if (!$goods): ?>
          <div class="alert alert--info">No goods yet.</div>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>Cost</th>
                <th>Stock</th>
                <th class="right">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($goods as $g): ?>
                <tr>
                  <td><?= (int)$g['goodID'] ?></td>
                  <td><b><?= e($g['name']) ?></b></td>
                  <td><?= e($g['type']) ?></td>
                  <td><?= number_format((float)$g['cost'], 0, '.', ' ') ?> ₸</td>
                  <td><?= (int)$g['count'] ?></td>
                  <td class="right">
                    <a class="btn btn--ghost" href="<?= e(current_url('dashboard', ['tab' => 'goods', 'action' => 'edit', 'id' => (int)$g['goodID']])) ?>">Edit</a>
                    <a class="btn btn--danger" data-confirm="Delete this good?" href="php/actions/goods_delete.php?id=<?= (int)$g['goodID'] ?>">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
