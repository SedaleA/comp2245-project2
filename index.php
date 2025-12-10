<?php
require_once 'dolphin_crm.php';
session_start();

$errorMessage = '';
$email = '';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errorMessage = 'Email is required.';
    } elseif ($password === '') {
        $errorMessage = 'Password is required.';
    } else {
        $stmt = $conn->prepare('SELECT id, firstname, lastname, password, email, role FROM Users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header('Location: dashboard.php');
            exit;
        }

        $errorMessage = 'The provided credentials do not match our records.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="login-header">
                <span class="icon">🐬</span>
                <h1>Dolphin CRM</h1>
            </header>
            
    <div class="login-container">
        <div class="login-box">


            <form id="loginForm" class="login-form" method="post">
                <?php if ($errorMessage): ?>
                    <div class="alert alert-error" aria-live="polite">
                        <?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>

                <button type="submit" class="submit-btn">Login</button>
            </form>
        </div>
        <div class="login-footer">
            <p class="copyright">Copyright © 2025 Dolphin CRM</p>
        </div>
    </div>

    <script src="scripts.js"></script>
</body>
</html>
