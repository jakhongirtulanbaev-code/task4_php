<?php

require __DIR__ . '/db.php';

$currentUser = require_auth($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('users.php'));
    exit;
}

$ids = $_POST['ids'] ?? [];
$action = $_POST['action'] ?? '';

if (!is_array($ids) || !$ids || !in_array($action, ['block', 'unblock', 'delete_unverified', 'delete_verified'], true)) {
    header('Location: ' . base_url('users.php'));
    exit;
}

// Sanitise IDs
$ids = array_map('intval', $ids);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$currentUserId = (int)$currentUser['id'];
$wasCurrentUserAffected = false;
$affectedRows = 0;

switch ($action) {
    case 'block':
        $stmt = $pdo->prepare("UPDATE users SET status = 'blocked' WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $affectedRows = $stmt->rowCount();
        // O'zimiz ham bloklangan bo'lsak
        if (in_array($currentUserId, $ids, true)) {
            $wasCurrentUserAffected = true;
        }
        break;
    case 'unblock':
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE status != 'unverified' AND id IN ($placeholders)");
        $stmt->execute($ids);
        $affectedRows = $stmt->rowCount();
        break;
    case 'delete_unverified':
        // Faqat unverified userlarni o'chiramiz
        // Avval tanlangan userlarning statusini tekshiramiz
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE status = 'unverified' AND id IN ($placeholders)");
        $checkStmt->execute($ids);
        $verifiedIds = $checkStmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($verifiedIds)) {
            $deletePlaceholders = implode(',', array_fill(0, count($verifiedIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($deletePlaceholders)");
            $stmt->execute($verifiedIds);
            $affectedRows = $stmt->rowCount();
            
            // O'zimiz ham o'chirilgan bo'lsak
            if (in_array($currentUserId, $verifiedIds, true)) {
                $wasCurrentUserAffected = true;
            }
        }
        break;
    case 'delete_verified':
        // Faqat verified (active yoki blocked) userlarni o'chiramiz
        $stmt = $pdo->prepare("DELETE FROM users WHERE status IN ('active','blocked') AND id IN ($placeholders)");
        $stmt->execute($ids);
        $affectedRows = $stmt->rowCount();
        // O'zimiz ham o'chirilgan bo'lsak
        if (in_array($currentUserId, $ids, true)) {
            // O'zimiz verified bo'lsa, o'chirilgan bo'lishimiz mumkin
            $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $check->execute([$currentUserId]);
            if ($check->fetchColumn() == 0) {
                $wasCurrentUserAffected = true;
            }
        }
        break;
}

// Agar hech narsa o'zgartirilmagan bo'lsa, xabar beramiz
if ($affectedRows == 0 && !$wasCurrentUserAffected) {
    $_SESSION['error_message'] = 'No users were affected. Make sure you selected users with the correct status.';
}

// Agar o'zimiz ham bloklangan / o'chirilgan bo'lsak, login sahifasiga qaytamiz
if ($wasCurrentUserAffected) {
    session_destroy();
    header('Location: ' . base_url('login.php'));
    exit;
}

header('Location: ' . base_url('users.php'));
exit;


