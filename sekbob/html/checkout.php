<?php
require_login();
$auth = auth_user();
if (($auth['role'] ?? '') !== 'client') {
    echo '<div class="alert alert--warning">Only clients can checkout.</div>';
    return;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (!$cart) {
    echo '<div class="panel"><h1 class="h1">checkout</h1><div class="alert alert--info">Cart is empty.</div></div>';
    return;
}

$ids = [];
foreach (array_keys($cart) as $id) {
    $ids[] = (int)$id;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));
$goods = db_query_all($conn, "SELECT goodID, name, cost, count FROM goods WHERE goodID IN ($placeholders)", $types, $ids);

$goodsById = [];
foreach ($goods as $g) {
    $goodsById[(int)$g['goodID']] = $g;
}

$errors = [];
$total = 0;
foreach ($cart as $gid => $qty) {
    $gid = (int)$gid;
    $qty = (int)$qty;
    $g = isset($goodsById[$gid]) ? $goodsById[$gid] : null;

    if (!$g) {
        $errors[] = 'Good #' . $gid . ' not found';
        continue;
    }

    if ($qty > (int)$g['count']) {
        $errors[] = 'Not enough stock for ' . $g['name'];
    }

    $total += (float)$g['cost'] * $qty;
}
?>
<div class="panel">
  <h1 class="h1">checkout</h1>

  <?php if ($errors): ?>
    <div class="alert alert--danger">
      <?= e(implode(' | ', $errors)) ?>
    </div>
    <a class="btn btn--ghost" href="<?= e(current_url('cart')) ?>">Back to cart</a>
    <?php return; endif; ?>

  <p class="muted">After confirmation we will create orders, decrease stock and assign a worker automatically.</p>

  <div class="card">
    <div class="card__body">
      <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
        <div><b>Client:</b> <?= e($auth['display_name']) ?></div>
        <div><b>Total:</b> <?= number_format($total, 0, '.', ' ') ?> ₸</div>
      </div>
    </div>
  </div>

  <form action="php/actions/order_create.php" method="post" style="margin-top:14px;">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
    <button class="btn btn--primary" type="submit">Confirm and create order</button>
    <a class="btn btn--ghost" href="<?= e(current_url('cart')) ?>">Cancel</a>
  </form>
</div>
