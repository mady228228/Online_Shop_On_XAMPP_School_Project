<?php
require_once __DIR__ . '/../bootstrap.php';

if (!is_post()) {
    redirect(current_url('register_user'));
}

require_csrf();

$nickname = trim(isset($_POST['nickname']) ? $_POST['nickname'] : '');
$phone = trim(isset($_POST['phone_number']) ? $_POST['phone_number'] : '');
$addr = trim(isset($_POST['home_address']) ? $_POST['home_address'] : '');
$pass = isset($_POST['password']) ? $_POST['password'] : '';
$pass2 = isset($_POST['password2']) ? $_POST['password2'] : '';

if ($nickname === '' || $phone === '' || $addr === '' || $pass === '' || $pass2 === '') {
    flash_set('global', 'Fill in all fields.', 'warning');
    redirect(current_url('register_user'));
}

if (strlen($pass) < 6) {
    flash_set('global', 'Password must be at least 6 characters.', 'warning');
    redirect(current_url('register_user'));
}

if ($pass !== $pass2) {
    flash_set('global', 'Passwords do not match.', 'warning');
    redirect(current_url('register_user'));
}

$ok = db_execute(
    $conn,
    "INSERT INTO clients (nickname, password_hash, phone_number, home_address) VALUES (?, ?, ?, ?)",
    'ssss',
    [$nickname, $pass, $phone, $addr]
);

if ($ok) {
    flash_set('global', 'Registration successful. Please login.', 'success');
    redirect(current_url('login'));
}

if (mysqli_errno($conn) == 1062) {
    flash_set('global', 'This nickname is already taken.', 'warning');
} else {
    flash_set('global', 'DB error: ' . db_error($conn), 'danger');
}

redirect(current_url('register_user'));
