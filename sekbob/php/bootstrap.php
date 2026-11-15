<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Almaty');

$config = require __DIR__ . '/config.php';

// Автоматически вычисляем базовый путь проекта.
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$base = preg_replace('#/php/.*$#', '', $script);

if ($base === $script) {
    $base = rtrim(dirname($script), '/');
}

$GLOBALS['base_url'] = $base ? $base : '';

require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Создаем CSRF-токен один раз и храним его в сессии.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$conn = db_connect($config);
