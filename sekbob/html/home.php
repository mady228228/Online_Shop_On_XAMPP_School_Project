<?php
// Показываем каталог товаров для всех пользователей.
$goods = db_query_all($conn, "SELECT goodID, name, description, cost, type, count, image_url FROM goods ORDER BY created_at DESC");
?>
<div class="row">
  <div class="panel">
    <h1 class="h1">store</h1>
    <p class="muted">Browse products and make an order. Stock updates automatically after checkout.</p>
    <?php if (!$goods): ?>
      <div class="alert alert--info">No goods in database yet. Login as owner/worker → Dashboard → add goods.</div>
    <?php endif; ?>
    <div class="grid">
      <?php foreach ($goods as $g): ?>
        <div class="card">
          <div class="thumb"><?= e(mb_strtoupper(mb_substr($g['type'], 0, 12))) ?></div>
          <div class="card__body">
            <h3 class="card__title"><?= e($g['name']) ?></h3>
            <div class="card__meta">
              <span class="badge"><?= e($g['type']) ?></span>
              &nbsp; • &nbsp; <?= number_format((float)$g['cost'], 0, '.', ' ') ?> ₸
              &nbsp; • &nbsp; stock: <?= (int)$g['count'] ?>
            </div>
            <div class="card__actions">
              <a class="btn btn--ghost" href="<?= e(current_url('product', ['id' => (int)$g['goodID']])) ?>">Open</a>
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
      <?php endforeach; ?>
    </div>
  </div>
</div>
