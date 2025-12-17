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


    // Validate inputs
    if ($firstname === '' || $lastname === '' || $email === '' || $company === '') {
        $error = 'Firstname, Lastname, Email, and Company are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($type, ['Sales Lead', 'Support'], true)) {
        $error = 'Invalid contact type.';
    } else {
        try {
            // Check for duplicate email
            $check = $conn->prepare('SELECT id FROM Contacts WHERE email = :email LIMIT 1');
            $check->execute([':email' => $email]);
            if ($check->fetch()) {
                $error = 'A contact with that email already exists.';
            } else {
                $stmt = $conn->prepare(
                    'INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at)
                     VALUES (:title, :firstname, :lastname, :email, :telephone, :company, :type, NULL, :created_by, NOW(), NOW())'
                );
                // Execute insertion
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

                // Redirect to dashboard after successful creation
                header('Location: dashboard.php');
                exit;
            } // end if duplicate check
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
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <header class="top-bar">
        <div class="top-brand">
            <img src="assets/dolphin.svg" alt="Dolphin CRM" class="brand-logo">
            <div>
                <span class="brand-title">Dolphin CRM</span>
            </div>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <?php renderAdminSidebar($activePage); ?>
        </aside>

        <main class="page-shell">
            <div class="form-hero">
                <h1>New Contact</h1>
            </div>

            <div class="panel panel-compact">
                <section class="panel-body">
                    <form class="contact-form" action="#" method="post" novalidate>
                        <div class="form-grid-single">
                            <div class="form-group form-group-short">
                                <label for="title" class="form-label">Title</label>
                                <select id="title" name="title" class="form-input">
                                    <option>Mr</option>
                                    <option>Ms</option>
                                    <option>Mrs</option>
                                    <option>Dr</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-two">
                            <div class="form-group">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" id="firstname" name="firstname" class="form-input" placeholder="Jane">
                            </div>
                            <div class="form-group">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" id="lastname" name="lastname" class="form-input" placeholder="Doe">
                            </div>
                        </div>

                        <div class="form-grid-two">
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-input" placeholder="something@example.com">
                            </div>
                            <div class="form-group">
                                <label for="telephone" class="form-label">Telephone</label>
                                <input type="tel" id="telephone" name="telephone" class="form-input">
                            </div>
                        </div>

                        <div class="form-grid-two">
                            <div class="form-group">
                                <label for="company" class="form-label">Company</label>
                                <input type="text" id="company" name="company" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="type" class="form-label">Type</label>
                                <select id="type" name="type" class="form-input">
                                    <option>Sales Lead</option>
                                    <option>Support</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-single">
                            <div class="form-group">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <select id="assigned_to" name="assigned_to" class="form-input">
                                    <option>Andy Bernard</option>
                                    <option>Pam Beesley</option>
                                    <option>Dwight Schrute</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="submit-btn">Save</button>
                        </div>
                    </form>
                </section>
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

