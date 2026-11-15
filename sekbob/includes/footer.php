  </div>
</main>

<footer class="footer">
  <div class="container footer__inner">
    <div>© <?= date('Y') ?> IP Komfort</div>
    <div class="footer__small">MVP: HTML • CSS • JS • PHP • MySQL</div>
  </div>
</footer>

<nav class="bottomnav">
  <a class="bottomnav__item" href="<?= e(current_url('home')) ?>">
    <span class="bottomnav__icon">🏠</span><span>Store</span>
  </a>
  <?php if (!empty($auth) && $auth['role']==='client'): ?>
    <a class="bottomnav__item" href="<?= e(current_url('cart')) ?>">
      <span class="bottomnav__icon">🧺</span><span>Cart</span>
    </a>
    <a class="bottomnav__item" href="<?= e(current_url('chat')) ?>">
      <span class="bottomnav__icon">💬</span><span>Chat</span>
    </a>
  <?php elseif (!empty($auth) && $auth['role']==='worker'): ?>
    <a class="bottomnav__item" href="<?= e(current_url('dashboard')) ?>">
      <span class="bottomnav__icon">📋</span><span>Panel</span>
    </a>
    <a class="bottomnav__item" href="<?= e(current_url('chat_admin')) ?>">
      <span class="bottomnav__icon">💬</span><span>Chat</span>
    </a>
  <?php else: ?>
    <a class="bottomnav__item" href="<?= e(current_url('login')) ?>">
      <span class="bottomnav__icon">🔑</span><span>Login</span>
    </a>
    <a class="bottomnav__item" href="<?= e(current_url('register_user')) ?>">
      <span class="bottomnav__icon">📝</span><span>Register</span>
    </a>
  <?php endif; ?>
</nav>

</body>
</html>
