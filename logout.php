<?php

require __DIR__ . '/db.php';

session_destroy();

header('Location: ' . base_url('login.php'));
exit;


