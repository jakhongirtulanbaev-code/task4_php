<?php

require __DIR__ . '/db.php';

if (current_user($pdo)) {
    header('Location: ' . base_url('users.php'));
} else {
    header('Location: ' . base_url('login.php'));
}
exit;


