<?php
require_once 'dolphin_crm.php';
require_once 'auth.php';
require_once 'admin_menu.php';

requireAdminAuthentication();
$activePage = 'home';

$contactId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$contact = null;
$error = '';

if ($contactId > 0) {
    try {
        $stmt = $conn->prepare(
            'SELECT c.*, u.firstname AS assigned_firstname, u.lastname AS assigned_lastname
             FROM Contacts c
             LEFT JOIN Users u ON c.assigned_to = u.id
             WHERE c.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $contactId]);
        $contact = $stmt->fetch();
        if (!$contact) {
            $error = 'We could not find that contact.';
        }
    } catch (PDOException $e) {
        $error = 'Unable to load the contact right now.';
    }
} else {
    $error = 'Invalid contact identifier.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Contact Details</title>
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
                        <h1>Contact Details</h1>
                        <p>Review the information stored for the selected contact.</p>
                    </div>
                    <a href="dashboard.php" class="action-btn secondary">Back to Contacts</a>
                </div>

                <section class="panel-body">
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
                    <?php else: ?>
                        <div class="detail-grid">
                            <div>
                                <span class="detail-label">Title</span>
                                <p class="detail-value"><?php echo htmlspecialchars($contact['title'] ?? '-', ENT_QUOTES); ?></p>
                            </div>
                            <div>
                                <span class="detail-label">Name</span>
                                <p class="detail-value"><?php echo htmlspecialchars(trim($contact['firstname'] . ' ' . $contact['lastname']), ENT_QUOTES); ?></p>
                            </div>
                            <div>
                                <span class="detail-label">Email</span>
                                <p class="detail-value"><?php echo htmlspecialchars($contact['email'] ?? '-', ENT_QUOTES); ?></p>
                            </div>
                            <div>
                                <span class="detail-label">Company</span>
                                <p class="detail-value"><?php echo htmlspecialchars($contact['company'] ?? '-', ENT_QUOTES); ?></p>
                            </div>
                            <div>
                                <span class="detail-label">Type</span>
                                <p class="detail-value"><?php echo htmlspecialchars($contact['type'] ?? '-', ENT_QUOTES); ?></p>
                            </div>
                            <div>
                                <span class="detail-label">Assigned to</span>
                                <p class="detail-value">
                                    <?php
                                    if (!empty($contact['assigned_firstname']) || !empty($contact['assigned_lastname'])) {
                                        echo htmlspecialchars(trim($contact['assigned_firstname'] . ' ' . $contact['assigned_lastname']), ENT_QUOTES);
                                    } else {
                                        echo 'Unassigned';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div>
                                <span class="detail-label">Created At</span>
                                <p class="detail-value"><?php echo htmlspecialchars($contact['created_at'] ?? '-', ENT_QUOTES); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
