<?php

require __DIR__ . '/db.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    header('Location: ' . base_url('login.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE confirmation_token = :token');
$stmt->execute(['token' => $token]);
$user = $stmt->fetch();

if ($user) {
    if ($user['status'] !== 'blocked') {
        $updateStatus = $pdo->prepare('UPDATE users SET status = :status, confirmation_token = NULL WHERE id = :id');
        $updateStatus->execute([
            'status' => 'active',
            'id'     => $user['id'],
        ]);
    } else {
        $pdo->prepare('UPDATE users SET confirmation_token = NULL WHERE id = :id')
            ->execute(['id' => $user['id']]);
    }
}

header('Location: ' . base_url('login.php'));
exit;


