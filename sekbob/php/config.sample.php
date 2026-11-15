<?php
/**
 * Copy this file to config.php and edit DB credentials to match your phpMyAdmin/MySQL.
 */
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'comfort_shop',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'site' => [
        'name' => 'IP Komfort',
        // percent bonus added to worker when an order is completed
        'worker_bonus_percent' => 2,
    ],
];
