<?php
require_once 'dolphin_crm.php';
require_once 'auth.php';
require_once 'admin_menu.php';

requireAdminAuthentication();

$errors = [];
$successMessage = '';

$firstname = '';
$lastname = '';
$email = '';
$role = 'Member';
$activePage = 'add_user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'Member';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($firstname === '') {
        $errors[] = 'First name is required.';
    }

    if ($lastname === '') {
        $errors[] = 'Last name is required.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    $allowedRoles = ['Admin', 'Member'];
    if (!in_array($role, $allowedRoles, true)) {
        $errors[] = 'Please choose a valid role.';
    }

    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    if ($password !== '' && !preg_match($passwordPattern, $password)) {
        $errors[] = 'Password must be at least 8 characters long and include at least one number, one lowercase letter, and one uppercase letter.';
    }

    if ($password !== '' && $password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare(
                'INSERT INTO Users (firstname, lastname, password, email, role, created_at)
                 VALUES (:firstname, :lastname, :password, :email, :role, NOW())'
            );

            $stmt->execute([
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':email' => $email,
                ':role' => $role,
            ]);

            $successMessage = 'The new user was added successfully.';
            $firstname = '';
            $lastname = '';
            $email = '';
            $role = 'Member';
        } catch (PDOException $e) {
            $errors[] = 'Unable to add the user at this time. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Add a User</title>
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
    </header>

    <div class="layout">
        <aside class="sidebar">
            <?php renderAdminSidebar($activePage); ?>
        </aside>

        <main class="page-shell">
            <div class="form-hero">
                <h1>New User</h1>
            </div>

            <div class="panel panel-compact">
                <section class="panel-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error" aria-live="polite">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error, ENT_QUOTES); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($successMessage): ?>
                    <div class="alert alert-success" aria-live="polite">
                        <?php echo htmlspecialchars($successMessage, ENT_QUOTES); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="signup-form" novalidate>
                    <div class="form-grid-two">
                        <div class="form-group">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" id="firstname" name="firstname" class="form-input" value="<?php echo htmlspecialchars($firstname, ENT_QUOTES); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" id="lastname" name="lastname" class="form-input" value="<?php echo htmlspecialchars($lastname, ENT_QUOTES); ?>" required>
                        </div>
                    </div>

                    <div class="form-grid-two">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).{8,}"
                                title="At least 8 characters, with one number, one lowercase letter, and one uppercase letter."
                                required
                            >
                        </div>
                    </div>

                    <div class="form-grid-two">
                        <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <select id="role" name="role" class="form-input" required>
                                <option value="Admin" <?php echo $role === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="Member" <?php echo $role === 'Member' ? 'selected' : ''; ?>>Member</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                            <p class="form-note">Passwords are hashed before storing for security.</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn">Save</button>
                    </div>
                </form>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
