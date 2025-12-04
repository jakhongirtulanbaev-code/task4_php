<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config.php';

try {
    $port = $config['db_port'] ?? 3306;
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $config['db_host'], $port, $config['db_name']);
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Database connection error: ' . htmlspecialchars($e->getMessage());
    exit;
}

function base_url(string $path = ''): string
{
    global $config;
    $base = rtrim($config['base_url'], '/');
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

function current_user(PDO $pdo): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user) {
        unset($_SESSION['user_id']);
        return null;
    }
    return $user;
}

function require_auth(PDO $pdo): array
{
    $user = current_user($pdo);
    if (!$user) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
    if ($user['status'] === 'blocked') {
        // Agar bloklangan bo'lsa ham login sahifasiga
        unset($_SESSION['user_id']);
        header('Location: ' . base_url('login.php'));
        exit;
    }
    return $user;
}


