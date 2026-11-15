<?php

/**
 * Создает подключение к MySQL через procedural mysqli.
 * Возвращает ресурс подключения, который используется во всем проекте.
 */
function db_connect($config)
{
    $db = $config['db'];

    $conn = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);

    if (!$conn) {
        die('Database connection error: ' . mysqli_connect_error());
    }

    mysqli_set_charset($conn, $db['charset']);

    return $conn;
}

/**
 * Привязывает параметры к prepared statement по ссылке.
 * Отдельная функция нужна потому, что mysqli_stmt_bind_param принимает аргументы только по ссылке.
 */
function db_bind_params($stmt, $types, $params)
{
    $bindArguments = [$stmt, $types];

    foreach ($params as $key => $value) {
        $params[$key] = $value;
        $bindArguments[] = &$params[$key];
    }

    return call_user_func_array('mysqli_stmt_bind_param', $bindArguments);
}

/**
 * Выполняет SELECT-запрос и возвращает все найденные строки в виде массива.
 * Можно вызывать как без параметров, так и с prepared statement параметрами.
 */
function db_query_all($conn, $sql, $types = '', $params = [])
{
    if ($types === '' || empty($params)) {
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die('SQL query error: ' . mysqli_error($conn));
        }

        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        mysqli_free_result($result);
        return $rows;
    }

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die('SQL prepare error: ' . mysqli_error($conn));
    }

    if (!db_bind_params($stmt, $types, $params)) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        die('SQL bind error: ' . $error);
    }

    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        die('SQL execute error: ' . $error);
    }

    $result = mysqli_stmt_get_result($stmt);
    $rows = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

/**
 * Выполняет SELECT-запрос и возвращает только первую строку.
 * Если запись не найдена, возвращает null.
 */
function db_query_one($conn, $sql, $types = '', $params = [])
{
    $rows = db_query_all($conn, $sql, $types, $params);

    if (!empty($rows)) {
        return $rows[0];
    }

    return null;
}

/**
 * Выполняет INSERT, UPDATE или DELETE запрос.
 * Возвращает true при успехе и false при ошибке.
 */
function db_execute($conn, $sql, $types = '', $params = [])
{
    if ($types === '' || empty($params)) {
        return mysqli_query($conn, $sql) ? true : false;
    }

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    if (!db_bind_params($stmt, $types, $params)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $success;
}

/**
 * Запускает транзакцию, чтобы несколько SQL-запросов выполнились как одна операция.
 */
function db_begin_transaction($conn)
{
    mysqli_begin_transaction($conn);
}

/**
 * Подтверждает транзакцию и сохраняет все изменения в базе.
 */
function db_commit_transaction($conn)
{
    mysqli_commit($conn);
}

/**
 * Отменяет транзакцию, если внутри произошла ошибка.
 */
function db_rollback_transaction($conn)
{
    mysqli_rollback($conn);
}

/**
 * Возвращает ID последней добавленной записи.
 */
function db_last_insert_id($conn)
{
    return mysqli_insert_id($conn);
}

/**
 * Возвращает текст последней ошибки MySQL.
 */
function db_error($conn)
{
    return mysqli_error($conn);
}
