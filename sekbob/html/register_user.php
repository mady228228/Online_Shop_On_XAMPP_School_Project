<div class="panel">
  <h1 class="h1">registration</h1>

  <form class="form" action="php/actions/register_user.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

    <div class="field">
      <label for="nickname">nickname</label>
      <input class="input" id="nickname" name="nickname" type="text" placeholder="SUPA_LIGA337" required>
    </div>

    <div class="field">
      <label for="phone">phone</label>
      <input class="input" id="phone" name="phone_number" type="text" placeholder="+7 (777) 777 77-77" required>
    </div>

    <div class="field">
      <label for="addr">delivery address</label>
      <input class="input" id="addr" name="home_address" type="text" placeholder="Pushkin st. 52" required>
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
      <a href="<?= e(current_url('register_worker')) ?>">for workers</a>
    </div>
  </form>
</div>
