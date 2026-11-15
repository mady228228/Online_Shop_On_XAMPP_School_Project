<?php
$auth = auth_user();
$flash = flash_get('global');
$siteName = $config['site']['name'] ?? 'Store';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($siteName) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <script defer src="js/main.js"></script>
</head>
<body>
<header class="topbar">
  <div class="container topbar__inner">
    <a class="brand" href="<?= e(current_url('home')) ?>">
      <span class="brand__dot"></span>
      <span class="brand__name"><?= e($siteName) ?></span>
    </a>

    <nav class="nav" id="mainNav">
      <a class="nav__link" href="<?= e(current_url('home')) ?>">Store</a>
      <a class="nav__link" href="<?= e(current_url('about')) ?>">About</a>
      <a class="nav__link" href="<?= e(current_url('contact')) ?>">Contact</a>
      <?php if ($auth && $auth['role'] === 'client'): ?>
        <a class="nav__link" href="<?= e(current_url('my_orders')) ?>">My orders</a>
        <a class="nav__link" href="<?= e(current_url('cart')) ?>">Cart<?= !empty($_SESSION['cart']) ? ' (' . array_sum($_SESSION['cart']) . ')' : '' ?></a>
        <a class="nav__link" href="<?= e(current_url('chat')) ?>">Chat</a>
      <?php elseif ($auth && $auth['role'] === 'worker'): ?>
        <a class="nav__link" href="<?= e(current_url('dashboard')) ?>">Dashboard</a>
        <a class="nav__link" href="<?= e(current_url('chat_admin')) ?>">Chat</a>
      <?php endif; ?>
    </nav>

    <div class="auth">
      <?php if ($auth): ?>
        <div class="auth__user">
          <div class="chip">
            <span class="chip__label"><?= e($auth['display_name']) ?></span>
            <span class="chip__sub"><?= e($auth['role'] === 'worker' ? (($auth['is_owner'] ?? false) ? 'owner' : 'worker') : 'client') ?></span>
          </div>
          <a class="btn btn--ghost" href="php/actions/logout.php">Logout</a>
        </div>
      <?php else: ?>
        <a class="btn btn--ghost" href="<?= e(current_url('login')) ?>">Login</a>
        <a class="btn" href="<?= e(current_url('register_user')) ?>">Registration</a>
      <?php endif; ?>

      <button class="burger" type="button" data-burger aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<main class="main">
  <div class="container">
    <?php if ($flash): ?>
      <div class="alert alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
