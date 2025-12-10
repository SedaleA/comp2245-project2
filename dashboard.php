<?php
require_once 'dolphin_crm.php';
require_once 'auth.php';
require_once 'admin_menu.php';

requireAdminAuthentication();
$activePage = 'home';

$filter = $_GET['filter'] ?? 'all';
$filterLabels = [
    'all' => 'All Contacts',
    'sales' => 'Sales Leads',
    'support' => 'Support',
    'assigned' => 'Assigned to me',
];

$filter = array_key_exists($filter, $filterLabels) ? $filter : 'all';
$contacts = [];
$error = '';

try {
    $where = [];
    $params = [];
    $currentUserId = $_SESSION['user_id'] ?? null;

    if ($filter === 'sales') {
        $where[] = 'type = :sales';
        $params[':sales'] = 'Sales Lead';
    } elseif ($filter === 'support') {
        $where[] = 'type = :support';
        $params[':support'] = 'Support';
    } elseif ($filter === 'assigned') {
        if ($currentUserId) {
            $where[] = 'assigned_to = :assigned';
            $params[':assigned'] = $currentUserId;
        } else {
            $where[] = '1 = 0';
        }
    }

    $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $stmt = $conn->prepare("
        SELECT id, title, firstname, lastname, email, company, type, assigned_to, created_at
        FROM Contacts
        {$whereSql}
        ORDER BY created_at DESC
    ");
    $stmt->execute($params);
    $contacts = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Unable to load contacts at the moment. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Dashboard</title>
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
                        <h1>Contacts</h1>
                        <p>Browse all contacts and filter by type or who they are assigned to.</p>
                    </div>
                    <a href="add_contact.php" class="action-btn">Add New Contact</a>
                </div>

                <section class="panel-body">
                    <div class="filters-row">
                        <span class="filter-label">Filter By:</span>
                        <div class="filters">
                            <?php foreach ($filterLabels as $key => $label): ?>
                                <a
                                    class="filter-pill<?php echo $filter === $key ? ' active' : ''; ?>"
                                    href="?filter=<?php echo urlencode($key); ?>"
                                >
                                    <?php echo htmlspecialchars($label, ENT_QUOTES); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
                    <?php endif; ?>

                    <div class="table-wrapper">
                        <table class="contacts-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Type of Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($contacts)): ?>
                                    <tr>
                                        <td colspan="5" class="empty-row">No contacts match this filter.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($contacts as $contact): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $fullName = trim($contact['title'] . ' ' . $contact['firstname'] . ' ' . $contact['lastname']);
                                                echo htmlspecialchars($fullName, ENT_QUOTES);
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($contact['email'], ENT_QUOTES); ?></td>
                                            <td><?php echo htmlspecialchars($contact['company'], ENT_QUOTES); ?></td>
                                            <td>
                                                <?php
                                                $typeValue = $contact['type'] ?? '-';
                                                $badgeClass = '';
                                                if (strcasecmp($typeValue, 'support') === 0) {
                                                    $badgeClass = 'badge-support';
                                                } elseif (strcasecmp($typeValue, 'sales lead') === 0) {
                                                    $badgeClass = 'badge-sales';
                                                } else {
                                                    $badgeClass = 'badge-default';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <?php echo htmlspecialchars(strtoupper($typeValue), ENT_QUOTES); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a class="text-link" href="view_contact.php?id=<?php echo urlencode($contact['id']); ?>">
                                                    View details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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
