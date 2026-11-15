<?php
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (!$cart) {
    echo '<div class="panel"><h1 class="h1">cart</h1><div class="alert alert--info">Cart is empty.</div><a class="btn btn--ghost" href="' . e(current_url('home')) . '">Go to store</a></div>';
    return;
}

$ids = [];
foreach (array_keys($cart) as $id) {
    $ids[] = (int)$id;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));
$goods = db_query_all($conn, "SELECT goodID, name, cost, type, count FROM goods WHERE goodID IN ($placeholders)", $types, $ids);

$goodsById = [];
foreach ($goods as $g) {
    $goodsById[(int)$g['goodID']] = $g;
}

$total = 0;
foreach ($cart as $gid => $qty) {
    $gid = (int)$gid;
    $qty = (int)$qty;

    if (!isset($goodsById[$gid])) {
        continue;
    }

    $total += (float)$goodsById[$gid]['cost'] * $qty;
}
?>
<div class="panel">
  <h1 class="h1">cart</h1>

  <table class="table">
    <thead>
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th style="width:160px;">Qty</th>
        <th>Sum</th>
        <th class="right">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cart as $gid => $qty):
          $gid = (int)$gid;
          $qty = (int)$qty;
          $g = isset($goodsById[$gid]) ? $goodsById[$gid] : null;
          if (!$g) continue;
          $max = (int)$g['count'];
          $line = (float)$g['cost'] * $qty;
      ?>
        <tr>
          <td>
            <div><b><?= e($g['name']) ?></b></div>
            <div class="muted"><?= e($g['type']) ?> • stock: <?= $max ?></div>
          </td>
          <td><?= number_format((float)$g['cost'], 0, '.', ' ') ?> ₸</td>
          <td>
            <form action="php/actions/cart_update.php" method="post" style="display:flex; gap:8px; align-items:center;">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <input type="hidden" name="goodID" value="<?= $gid ?>">
              <input class="input" style="padding:8px 12px; border-radius:12px; width:80px;" type="number" name="qty" min="1" max="<?= max(1, $max) ?>" value="<?= $qty ?>">
              <button class="btn btn--ghost" type="submit">Update</button>
            </form>
          </td>
          <td><?= number_format($line, 0, '.', ' ') ?> ₸</td>
          <td class="right">
            <a class="btn btn--danger" data-confirm="Remove item from cart?" href="php/actions/cart_remove.php?id=<?= $gid ?>">Remove</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3" class="right">Total</th>
        <th colspan="2"><?= number_format($total, 0, '.', ' ') ?> ₸</th>
      </tr>
    </tfoot>
  </table>

  <div class="form__actions" style="justify-content:space-between;">
    <a class="btn btn--ghost" href="<?= e(current_url('home')) ?>">Continue shopping</a>
    <a class="btn btn--primary" href="<?= e(current_url('checkout')) ?>">Checkout</a>
  </div>
</div>
