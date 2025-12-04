<?php

require __DIR__ . '/db.php';

$page_title = 'Register';
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid e-mail is required.';
    }
    if ($password === '') {
        $errors[] = 'Password must not be empty (any non-empty string is allowed).';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, status, confirmation_token, registered_at) VALUES (:name, :email, :password_hash, :status, :token, NOW())');
            $token = bin2hex(random_bytes(32));
            $stmt->execute([
                'name'          => $name,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'status'        => 'unverified',
                'token'         => $token,
            ]);

            $confirmUrl = base_url('confirm.php?token=' . urlencode($token));

            // Simple mail sending (can be replaced with real async job)
            $subject = 'Confirm your account';
            $message = "Please confirm your account by clicking the link:\n\n" . $confirmUrl;
            $headers = 'From: ' . $config['mail_from'];
            @mail($email, $subject, $message, $headers);

            $success_message = 'Registration successful. Email confirmation sent.';
            if (!empty($config['debug_show_confirmation_link'])) {
                $success_message .= ' Confirmation link (dev only): ' . htmlspecialchars($confirmUrl);
            }
        } catch (PDOException $e) {
            if ((int)$e->getCode() === 23000) { // unique constraint
                $errors[] = 'User with this e-mail already exists.';
            } else {
                $errors[] = 'Unexpected error. Please try again.';
            }
        }
    }
}

include __DIR__ . '/layout_header.php';
?>
<div class="app-container">
    <div class="card shadow-sm auth-card">
        <div class="card-body">
            <h5 class="card-title mb-3 text-center">Sign up</h5>

            <?php if ($errors): ?>
                <div class="text-danger small mb-3">
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="text-success small mb-3">
                    <?= $success_message ?>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                    <div class="form-text">Any non-empty password is accepted.</div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Create account</button>
                </div>
            </form>

            <div class="mt-3 text-center">
                <a href="<?= htmlspecialchars(base_url('login.php')) ?>">Already have an account? Sign in</a>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/layout_footer.php'; ?>


