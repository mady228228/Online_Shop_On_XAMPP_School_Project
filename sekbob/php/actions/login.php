<?php
require_once __DIR__ . '/../bootstrap.php';

if (!is_post()) {
    redirect(current_url('login'));
}

require_csrf();

$role = isset($_POST['role']) ? $_POST['role'] : 'client';
$name = trim(isset($_POST['name']) ? $_POST['name'] : '');
$pass = isset($_POST['password']) ? $_POST['password'] : '';

if ($name === '' || $pass === '') {
    flash_set('global', 'Fill in all fields.', 'warning');
    redirect(current_url('login'));
}

if ($role === 'worker') {
    $u = db_query_one($conn, "SELECT * FROM workers WHERE fullname = ?", 's', [$name]);

    if ($u && isset($u['password_hash']) && $pass === $u['password_hash']) {
        auth_login_worker($u);
        flash_set('global', 'Logged in successfully.', 'success');
        redirect(current_url('dashboard'));
    }
} else {
    $u = db_query_one($conn, "SELECT * FROM clients WHERE nickname = ?", 's', [$name]);

    if ($u && isset($u['password_hash']) && $pass === $u['password_hash']) {
        auth_login_client($u);
        flash_set('global', 'Logged in successfully.', 'success');
        redirect(current_url('home'));
    }
}

flash_set('global', 'Wrong login or password.', 'danger');
redirect(current_url('login'));
