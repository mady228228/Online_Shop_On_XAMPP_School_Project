<?php

/**
 * Авторизует клиента и сохраняет его данные в сессии.
 */
function auth_login_client($client)
{
    $_SESSION['auth'] = [
        'role' => 'client',
        'user_id' => (int)$client['clientID'],
        'display_name' => $client['nickname'],
        'is_owner' => false,
    ];
}

/**
 * Авторизует работника и сохраняет его данные в сессии.
 */
function auth_login_worker($worker)
{
    $_SESSION['auth'] = [
        'role' => 'worker',
        'user_id' => (int)$worker['workerID'],
        'display_name' => $worker['fullname'],
        'is_owner' => (bool)$worker['owner_status'],
    ];
}

/**
 * Выход из аккаунта: удаляет авторизацию и корзину из сессии.
 */
function auth_logout()
{
    unset($_SESSION['auth']);
    unset($_SESSION['cart']);
}

/**
 * Возвращает массив с данными текущего пользователя.
 * Если пользователь не вошел, возвращает null.
 */
function auth_user()
{
    return isset($_SESSION['auth']) ? $_SESSION['auth'] : null;
}

/**
 * Проверяет, есть ли активная авторизация у пользователя.
 */
function is_logged_in()
{
    return !empty($_SESSION['auth']);
}

/**
 * Требует авторизацию для доступа к странице.
 * Если пользователь не вошел, перенаправляет на страницу логина.
 */
function require_login()
{
    if (!is_logged_in()) {
        flash_set('global', 'Please log in to continue.', 'warning');
        redirect(current_url('login'));
    }
}

/**
 * Разрешает доступ только работнику или владельцу.
 */
function require_worker()
{
    require_login();

    $user = auth_user();
    if (($user['role'] ?? '') !== 'worker') {
        http_response_code(403);
        exit('Access denied');
    }
}

/**
 * Разрешает доступ только владельцу магазина.
 */
function require_owner()
{
    require_worker();

    $user = auth_user();
    if (empty($user['is_owner'])) {
        http_response_code(403);
        exit('Owner access required');
    }
}
