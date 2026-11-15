<?php
require_once __DIR__ . '/../bootstrap.php';

auth_logout();
flash_set('global', 'Logged out.', 'info');
redirect(current_url('home'));
