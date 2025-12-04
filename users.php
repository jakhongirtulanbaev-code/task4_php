<?php

require __DIR__ . '/db.php';

$user = require_auth($pdo);
$page_title = 'User Management';

// MySQL syntax: NULL values go to the end
$stmt = $pdo->query('SELECT id, name, email, status, last_login_at FROM users ORDER BY last_login_at IS NULL, last_login_at DESC');
$users = $stmt->fetchAll();

include __DIR__ . '/layout_header.php';
?>
<div class="my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">User Management</h4>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">Logged in as <?= htmlspecialchars($user['email']) ?></span>
            <a href="<?= htmlspecialchars(base_url('logout.php')) ?>" class="btn btn-outline-secondary btn-sm">Logout</a>
        </div>
    </div>

    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars(base_url('user_actions.php')) ?>" id="user-actions-form">
        <div class="table-toolbar">
            <div class="btn-group" role="group" aria-label="User actions">
                <button type="submit" class="btn btn-outline-danger" name="action" value="block" disabled>
                    Block
                </button>
                <button type="submit" class="btn btn-outline-secondary" name="action" value="unblock" disabled>
                    <i class="bi bi-unlock"></i>
                </button>
                <button type="submit" class="btn btn-outline-secondary" name="action" value="delete_unverified" disabled>
                    <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Unverified</span>
                </button>
                <button type="submit" class="btn btn-outline-secondary" name="action" value="delete_verified" disabled>
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 40px;">
                        <input type="checkbox" id="select-all">
                    </th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Last Login</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input user-checkbox" name="ids[]" value="<?= (int)$u['id'] ?>">
                        </td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php elseif ($u['status'] === 'unverified'): ?>
                                <span class="badge bg-secondary">Unverified</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Blocked</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $u['last_login_at'] ? htmlspecialchars($u['last_login_at']) : '—' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
    (function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        const actionButtons = document.querySelectorAll('#user-actions-form button[type="submit"]');

        function updateButtons() {
            let anyChecked = false;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    anyChecked = true;
                }
            });
            actionButtons.forEach(btn => {
                btn.disabled = !anyChecked;
            });
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateButtons();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (!cb.checked && selectAll.checked) {
                    selectAll.checked = false;
                }
                updateButtons();
            });
        });
    })();
</script>

<?php include __DIR__ . '/layout_footer.php'; ?>


