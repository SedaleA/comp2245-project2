<?php
require_once 'dolphin_crm.php';
require_once 'auth.php';
require_once 'admin_menu.php';

requireAdminAuthentication();
$activePage = 'new_contact';

$error = '';
$success = '';

$title = '';
$firstname = '';
$lastname = '';
$email = '';
$telephone = '';
$company = '';
$type = 'Sales Lead'; // default

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = trim($_POST['title'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $company   = trim($_POST['company'] ?? '');
    $type      = trim($_POST['type'] ?? 'Sales Lead');

    // Basic validation
    if ($firstname === '' || $lastname === '' || $email === '' || $company === '') {
        $error = 'Firstname, Lastname, Email, and Company are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($type, ['Sales Lead', 'Support'], true)) {
        $error = 'Invalid contact type.';
    } else {
        try {
            // Optional: prevent duplicate emails
            $check = $conn->prepare('SELECT id FROM Contacts WHERE email = :email LIMIT 1');
            $check->execute([':email' => $email]);
            if ($check->fetch()) {
                $error = 'A contact with that email already exists.';
            } else {
                $stmt = $conn->prepare(
                    'INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at)
                     VALUES (:title, :firstname, :lastname, :email, :telephone, :company, :type, NULL, :created_by, NOW(), NOW())'
                );

                $stmt->execute([
                    ':title'      => $title,
                    ':firstname'  => $firstname,
                    ':lastname'   => $lastname,
                    ':email'      => $email,
                    ':telephone'  => $telephone,
                    ':company'    => $company,
                    ':type'       => $type,
                    ':created_by' => (int)$_SESSION['user_id']
                ]);

                // Redirect to dashboard (or the new contact page)
                header('Location: dashboard.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Unable to create contact right now.';
        }
    }
}
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
                    <p>Create a new contact record.</p>
                </div>
                <a href="dashboard.php" class="action-btn secondary">Back to Contacts</a>
            </div>

            <section class="panel-body">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
                <?php endif; ?>

                <form method="POST" class="form-stack">
                    <div class="form-row">
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>">
                    </div>

                    <div class="form-row">
                        <label>Firstname *</label>
                        <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname, ENT_QUOTES); ?>" required>
                    </div>

                    <div class="form-row">
                        <label>Lastname *</label>
                        <input type="text" name="lastname" value="<?php echo htmlspecialchars($lastname, ENT_QUOTES); ?>" required>
                    </div>

                    <div class="form-row">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>" required>
                    </div>

                    <div class="form-row">
                        <label>Telephone</label>
                        <input type="text" name="telephone" value="<?php echo htmlspecialchars($telephone, ENT_QUOTES); ?>">
                    </div>

                    <div class="form-row">
                        <label>Company *</label>
                        <input type="text" name="company" value="<?php echo htmlspecialchars($company, ENT_QUOTES); ?>" required>
                    </div>

                    <div class="form-row">
                        <label>Type</label>
                        <select name="type">
                            <option value="Sales Lead" <?php echo $type === 'Sales Lead' ? 'selected' : ''; ?>>Sales Lead</option>
                            <option value="Support" <?php echo $type === 'Support' ? 'selected' : ''; ?>>Support</option>
                        </select>
                    </div>

                    <button type="submit" class="action-btn">Save</button>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>

