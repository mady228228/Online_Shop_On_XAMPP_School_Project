<?php
require_once __DIR__ . '/../bootstrap.php';

require_worker();

if (!is_post()) {
    redirect(current_url('dashboard', ['tab' => 'goods']));
}

require_csrf();

$goodID = isset($_POST['goodID']) ? (int)$_POST['goodID'] : 0;
$name = trim(isset($_POST['name']) ? $_POST['name'] : '');
$type = trim(isset($_POST['type']) ? $_POST['type'] : '');
$cost = isset($_POST['cost']) ? (float)$_POST['cost'] : 0;
$count = isset($_POST['count']) ? (int)$_POST['count'] : 0;
$desc = trim(isset($_POST['description']) ? $_POST['description'] : '');

if ($name === '' || $type === '') {
    flash_set('global', 'Name and type are required.', 'warning');
    redirect(current_url('dashboard', ['tab' => 'goods']));
}

if ($cost < 0) {
    $cost = 0;
}

if ($count < 0) {
    $count = 0;
}

if ($goodID > 0) {
    $ok = db_execute(
        $conn,
        "UPDATE goods SET name = ?, type = ?, cost = ?, count = ?, description = ? WHERE goodID = ?",
        'ssdisi',
        [$name, $type, $cost, $count, $desc, $goodID]
    );

    if ($ok) {
        flash_set('global', 'Good updated.', 'success');
    } else {
        flash_set('global', 'DB error: ' . db_error($conn), 'danger');
    }
} else {
    $ok = db_execute(
        $conn,
        "INSERT INTO goods (name, type, cost, count, description) VALUES (?, ?, ?, ?, ?)",
        'ssdis',
        [$name, $type, $cost, $count, $desc]
    );

    if ($ok) {
        flash_set('global', 'Good added.', 'success');
    } else {
        flash_set('global', 'DB error: ' . db_error($conn), 'danger');
    }
}

redirect(current_url('dashboard', ['tab' => 'goods']));
