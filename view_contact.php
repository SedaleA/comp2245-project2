<?php
require_once 'dolphin_crm.php';
require_once 'auth.php';
require_once 'admin_menu.php';

requireAdminAuthentication();
$activePage = 'home';

$contactId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$contact = null;
$notes = [];
$error = '';

if ($contactId > 0) {

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $comment = trim($_POST['comment'] ?? '');

        if ($comment === '') {
            $error = 'Please enter a note before submitting.';
        } else {
            try {
                $conn->beginTransaction();

                
                $noteStmt = $conn->prepare(
                    'INSERT INTO Notes (contact_id, comment, created_by, created_at)
                     VALUES (:contact_id, :comment, :created_by, NOW())'
                );

                $noteStmt->execute([
                    ':contact_id' => $contactId,
                    ':comment'    => $comment,
                    ':created_by' => (int)$_SESSION['user_id']

                ]);

                
                $updateStmt = $conn->prepare(
                    'UPDATE Contacts SET updated_at = NOW() WHERE id = :id'
                );
                $updateStmt->execute([':id' => $contactId]);

                $conn->commit();

                
                header("Location: view_contact.php?id=" . $contactId);
                exit;

            } catch (PDOException $e) {
                $conn->rollBack();
                $error = 'Unable to add note right now.';
            }
        }
    }

    
    if ($error === '') {
        try {
            $stmt = $conn->prepare(
                'SELECT c.*,
                        u.firstname  AS assigned_firstname, u.lastname  AS assigned_lastname,
                        cu.firstname AS created_firstname,  cu.lastname AS created_lastname
                 FROM Contacts c
                 LEFT JOIN Users u  ON c.assigned_to = u.id
                 LEFT JOIN Users cu ON c.created_by  = cu.id
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
    }

    
    if ($error === '' && $contact) {
        try {
            $notesStmt = $conn->prepare(
                'SELECT n.comment, n.created_at, u.firstname, u.lastname
                 FROM Notes n
                 JOIN Users u ON n.created_by = u.id
                 WHERE n.contact_id = :id
                 ORDER BY n.created_at DESC'
            );
            $notesStmt->execute([':id' => $contactId]);
            $notes = $notesStmt->fetchAll();
        } catch (PDOException $e) {
            
            $notes = [];
        }
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
            </div>
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
                            <p class="detail-value"><?php echo htmlspecialchars(trim(($contact['firstname'] ?? '').' '.($contact['lastname'] ?? '')), ENT_QUOTES); ?></p>
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
                            <span class="detail-label">Created By</span>
                            <p class="detail-value">
                                <?php
                                if (!empty($contact['created_firstname']) || !empty($contact['created_lastname'])) {
                                    echo htmlspecialchars(trim($contact['created_firstname'] . ' ' . $contact['created_lastname']), ENT_QUOTES);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </p>
                        </div>

                        <div>
                            <span class="detail-label">Created At</span>
                            <p class="detail-value"><?php echo htmlspecialchars($contact['created_at'] ?? '-', ENT_QUOTES); ?></p>
                        </div>

                        <div>
                            <span class="detail-label">Updated At</span>
                            <p class="detail-value"><?php echo htmlspecialchars($contact['updated_at'] ?? '-', ENT_QUOTES); ?></p>
                        </div>
                    </div>

                    <hr style="margin:24px 0;">

                    <h2 style="margin-bottom:10px;">Notes</h2>

                    <?php if (empty($notes)): ?>
                        <p>No notes yet.</p>
                    <?php else: ?>
                        <?php foreach ($notes as $note): ?>
                            <div class="note-card" style="padding:12px; border:1px solid #eee; border-radius:10px; margin-bottom:12px;">
                                <p style="margin:0 0 8px 0;">
                                    <strong><?php echo htmlspecialchars($note['firstname'].' '.$note['lastname'], ENT_QUOTES); ?></strong>
                                    <small style="margin-left:10px;">
                                        <?php echo htmlspecialchars($note['created_at'], ENT_QUOTES); ?>
                                    </small>
                                </p>
                                <p style="margin:0;"><?php echo nl2br(htmlspecialchars($note['comment'], ENT_QUOTES)); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <hr style="margin:24px 0;">

                    <h2 style="margin-bottom:10px;">Add a Note</h2>
                    <form method="POST" action="view_contact.php?id=<?php echo (int)$contactId; ?>">
                        <textarea name="comment" rows="4" style="width:100%; padding:10px;" placeholder="Enter details here"></textarea>
                        <div style="margin-top:10px;">
                            <button type="submit" class="action-btn">Add Note</button>
                        </div>
                    </form>

                <?php endif; ?>
            </section>
        </div>
    </main>
</div>
</body>
</html>
