<?php

/**
 * Безопасно выводит текст в HTML, чтобы спецсимволы не ломали страницу.
 */
function e($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Возвращает базовый путь проекта, чтобы ссылки работали из любой папки.
 */
function base_url()
{
    return isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : '/';
}

/**
 * Перенаправляет пользователя на другую страницу и завершает выполнение скрипта.
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Сохраняет одноразовое сообщение в сессию.
 * Оно показывается после перенаправления и затем удаляется.
 */
function flash_set($key, $message, $type = 'info')
{
    $_SESSION['flash'][$key] = [
        'message' => $message,
        'type' => $type,
    ];
}

/**
 * Возвращает одноразовое сообщение из сессии и сразу удаляет его.
 */
function flash_get($key)
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $value;
}

/**
 * Проверяет CSRF-токен у POST-запроса.
 * Если токен неправильный, скрипт останавливается.
 */
function require_csrf()
{
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    $sessionToken = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';

    if ($token === '' || $sessionToken === '' || !hash_equals($sessionToken, $token)) {
        http_response_code(400);
        exit('Invalid CSRF token');
    }
}

/**
 * Проверяет, был ли запрос отправлен методом POST.
 */
function is_post()
{
    return (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET') === 'POST';
}

/**
 * Собирает ссылку на внутреннюю страницу проекта с параметрами.
 */
function current_url($page, $params = [])
{
    $params = array_merge(['page' => $page], $params);
    return rtrim(base_url(), '/') . '/index.php?' . http_build_query($params);
}
