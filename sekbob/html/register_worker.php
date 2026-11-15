<div class="panel">
  <h1 class="h1">registration</h1>

  <form class="form" action="php/actions/register_worker.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

    <div class="field">
      <label for="fullname">fullname</label>
      <input class="input" id="fullname" name="fullname" type="text" placeholder="Petuhov Jamal" required>
    </div>

    <div class="field">
      <label for="phone">phone</label>
      <input class="input" id="phone" name="phone_number" type="text" placeholder="+7 (888) 888 88-88" required>
    </div>

    <div class="field">
      <label for="salary">salary</label>
      <input class="input" id="salary" name="salary" type="number" step="0.01" placeholder="100500" required>
    </div>

    <div class="field">
      <label for="pass">password</label>
      <input class="input" id="pass" name="password" type="password" placeholder="create password" required>
    </div>

    <div class="field">
      <label for="pass2">repeat password</label>
      <input class="input" id="pass2" name="password2" type="password" placeholder="repeat password" required>
    </div>

    <div class="form__actions">
      <button class="btn btn--primary" type="submit">registration</button>
    </div>

    <div class="linkrow">
      <a href="<?= e(current_url('login')) ?>">login</a>
      <a href="<?= e(current_url('register_user')) ?>">for users</a>
    </div>

    <div class="muted" style="padding-top:10px;">
      If you need an <b>owner</b>, create a worker and then set <b>owner_status=1</b> in phpMyAdmin (or use demo owner).
    </div>
  </form>
</div>
