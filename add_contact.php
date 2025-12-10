<?php
require_once 'auth.php';
require_once 'admin_menu.php';

requireAdminAuthentication();
$activePage = 'new_contact';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Add Contact</title>
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
                        <h1>Add Contact</h1>
                        <p>This form will let you add new contacts in a future update.</p>
                    </div>
                </div>

                <section class="panel-body">
                    <p>This functionality is coming soon. For now, use the contact filters above to get the right subset of contacts.</p>
                    <a href="dashboard.php" class="action-btn secondary">Back to Contacts</a>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
