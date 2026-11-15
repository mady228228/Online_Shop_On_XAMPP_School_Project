<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$g = db_query_one($conn, "SELECT * FROM goods WHERE goodID = ?", 'i', [$id]);

if (!$g) {
    echo '<div class="alert alert--warning">Product not found.</div>';
    return;
}
?>
<div class="panel">
  <h1 class="h1">product</h1>

  <div class="row">
    <div class="card">
      <div class="thumb"><?= e(mb_strtoupper(mb_substr($g['type'], 0, 12))) ?></div>
      <div class="card__body">
        <h3 class="card__title"><?= e($g['name']) ?></h3>
        <div class="card__meta">
          <span class="badge"><?= e($g['type']) ?></span>
          &nbsp; • &nbsp; <?= number_format((float)$g['cost'], 0, '.', ' ') ?> ₸
          &nbsp; • &nbsp; stock: <?= (int)$g['count'] ?>
        </div>
        <p class="muted"><?= e($g['description'] ? $g['description'] : 'No description yet.') ?></p>
        <div class="card__actions">
          <a class="btn btn--ghost" href="<?= e(current_url('home')) ?>">Back</a>
          <?php if ((int)$g['count'] > 0): ?>
            <form action="php/actions/cart_add.php" method="post">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <input type="hidden" name="goodID" value="<?= (int)$g['goodID'] ?>">
              <button class="btn" type="submit">Add to cart</button>
            </form>
          <?php else: ?>
            <span class="badge">sold out</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="panel">
      <h2 style="margin-top:0;">How ordering works</h2>
      <ol class="muted">
        <li>Add items to cart</li>
        <li>Checkout → orders are saved to DB</li>
        <li>Stock decreases automatically</li>
        <li>Worker/Owner sees new orders in Dashboard</li>
      </ol>
    </div>
  </div>
</div>
