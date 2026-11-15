<?php
require_once __DIR__ . '/../bootstrap.php';

if (!is_post()) {
    redirect(current_url('register_worker'));
}

require_csrf();

$fullname = trim(isset($_POST['fullname']) ? $_POST['fullname'] : '');
$phone = trim(isset($_POST['phone_number']) ? $_POST['phone_number'] : '');
$salary = isset($_POST['salary']) ? (float)$_POST['salary'] : 0;
$pass = isset($_POST['password']) ? $_POST['password'] : '';
$pass2 = isset($_POST['password2']) ? $_POST['password2'] : '';

if ($fullname === '' || $phone === '' || $pass === '' || $pass2 === '') {
    flash_set('global', 'Fill in all fields.', 'warning');
    redirect(current_url('register_worker'));
}

if (strlen($pass) < 6) {
    flash_set('global', 'Password must be at least 6 characters.', 'warning');
    redirect(current_url('register_worker'));
}

if ($pass !== $pass2) {
    flash_set('global', 'Passwords do not match.', 'warning');
    redirect(current_url('register_worker'));
}

$ok = db_execute(
    $conn,
    "INSERT INTO workers (fullname, password_hash, phone_number, salary, bonus, owner_status) VALUES (?, ?, ?, ?, 0, 0)",
    'sssd',
    [$fullname, $pass, $phone, $salary]
);

if ($ok) {
    flash_set('global', 'Registration successful. Please login as worker.', 'success');
    redirect(current_url('login'));
}

if (mysqli_errno($conn) == 1062) {
    flash_set('global', 'This fullname is already taken.', 'warning');
} else {
    flash_set('global', 'DB error: ' . db_error($conn), 'danger');
}

redirect(current_url('register_worker'));
