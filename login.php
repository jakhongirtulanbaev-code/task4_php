<?php

require __DIR__ . '/db.php';

if (current_user($pdo)) {
    header('Location: ' . base_url('users.php'));
    exit;
}

$page_title = 'Login';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid e-mail is required.';
    }
    if ($password === '') {
        $errors[] = 'Password must not be empty.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Invalid credentials.';
        } else {
            if ($user['status'] === 'blocked') {
                $errors[] = 'Your account is blocked.';
            } else {
                // unverified va active login qila oladi
                $_SESSION['user_id'] = $user['id'];
                $pdo->prepare('UPDATE users SET last_login_at = NOW(), last_activity_at = NOW() WHERE id = :id')
                    ->execute(['id' => $user['id']]);
                header('Location: ' . base_url('users.php'));
                exit;
            }
        }
    }
}

include __DIR__ . '/layout_header.php';
?>
<div class="app-container">
    <div class="card shadow-sm auth-card">
        <div class="card-body">
            <h5 class="card-title mb-3 text-center">Sign in</h5>

            <?php if ($errors): ?>
                <div class="text-danger small mb-3">
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Sign in</button>
                </div>
            </form>

            <div class="mt-3 text-center">
                <a href="<?= htmlspecialchars(base_url('register.php')) ?>">Don&apos;t have an account? Sign up</a>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/layout_footer.php'; ?>


