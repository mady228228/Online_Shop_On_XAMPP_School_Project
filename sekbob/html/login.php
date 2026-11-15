<?php
// Show login form. POST goes to actions/login.php
?>
<div class="panel" data-tabs>
  <div class="tab">
    <button class="tab__btn" type="button" data-tab="client">login (user)</button>
    <button class="tab__btn" type="button" data-tab="worker">login (worker/owner)</button>
  </div>

  <section data-panel="client">
    <h1 class="h1">login</h1>
    <form class="form" action="php/actions/login.php" method="post">
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="role" value="client">
      <div class="field">
        <label for="login_nickname">name</label>
        <input class="input" id="login_nickname" name="name" type="text" placeholder="nickname" required>
      </div>
      <div class="field">
        <label for="login_pass">password</label>
        <input class="input" id="login_pass" name="password" type="password" placeholder="password" required>
      </div>
      <div class="form__actions">
        <button class="btn btn--primary" type="submit">Login</button>
        <a class="btn btn--ghost" href="<?= e(current_url('register_user')) ?>">Registration</a>
      </div>
      <div class="muted">Demo user: <b>demo</b> / <b>demo123</b></div>
    </form>
  </section>

  <section data-panel="worker" hidden>
    <h1 class="h1">login</h1>
    <form class="form" action="php/actions/login.php" method="post">
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="role" value="worker">
      <div class="field">
        <label for="login_fullname">name</label>
        <input class="input" id="login_fullname" name="name" type="text" placeholder="fullname" required>
      </div>
      <div class="field">
        <label for="login_pass2">password</label>
        <input class="input" id="login_pass2" name="password" type="password" placeholder="password" required>
      </div>
      <div class="form__actions">
        <button class="btn btn--primary" type="submit">Login</button>
        <a class="btn btn--ghost" href="<?= e(current_url('register_worker')) ?>">Registration</a>
      </div>
      <div class="muted">Demo owner: <b>owner</b> / <b>owner123</b> • Demo worker: <b>worker</b> / <b>worker123</b></div>
    </form>
  </section>
</div>
