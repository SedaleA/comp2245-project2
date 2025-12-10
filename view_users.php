<?php
require_once 'dolphin_crm.php';
require_once 'auth.php';
require_once 'admin_menu.php';

requireAdminAuthentication();

$activePage = 'users';
$stmt = $conn->query('SELECT firstname, lastname, email, role, created_at FROM Users ORDER BY created_at DESC');
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Users</title>
    <link rel="stylesheet" href="add_user.css">
</head>
<body>
    <header class="top-bar">
        <div class="top-brand">
            <img src="assets/dolphin.svg" alt="Dolphin CRM" class="brand-logo">
            <div>
                <span class="brand-title">Dolphin CRM</span>
                <span class="brand-caption">Admin Panel</span>
            </div>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <?php renderAdminSidebar($activePage); ?>
        </aside>

        <main class="page-shell">
            <div class="panel panel-compact">
                <div class="panel-heading">
                    <div>
                        <h1>Users</h1>
                        <p>Only admins can see and manage the full list of accounts.</p>
                    </div>
                    <a href="add_user.php" class="action-btn">+ Add User</a>
                </div>

                <section class="panel-body">
                    <div class="table-wrapper">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname'], ENT_QUOTES); ?></td>
                                        <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?></td>
                                        <td><?php echo htmlspecialchars($user['role'], ENT_QUOTES); ?></td>
                                        <td>
                                            <?php
                                            $createdAt = $user['created_at'];
                                            $formatted = $createdAt ? (new DateTime($createdAt))->format('Y-m-d H:i') : '—';
                                            echo htmlspecialchars($formatted, ENT_QUOTES);
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="4" class="empty-row">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
