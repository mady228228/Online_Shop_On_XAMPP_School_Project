<?php
require_once __DIR__ . '/../bootstrap.php';

require_worker();

$goodID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($goodID <= 0) {
    redirect(current_url('dashboard', ['tab' => 'goods']));
}

$ok = db_execute($conn, "DELETE FROM goods WHERE goodID = ?", 'i', [$goodID]);

if ($ok) {
    flash_set('global', 'Good deleted.', 'info');
} else {
    flash_set('global', 'Cannot delete (maybe used in orders): ' . db_error($conn), 'warning');
}

redirect(current_url('dashboard', ['tab' => 'goods']));
