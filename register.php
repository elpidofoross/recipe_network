<?php
// Include config (DB, constants) and authentication utilities
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Initialize error variable
$error = '';

// =========================
// Handle registration form
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token for security
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // Sanitize inputs
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password_raw = $_POST['password']; // Raw password (don’t sanitize or hash mismatch)

    // Validate input length
    if (strlen($username) < 3 || strlen($email) < 5 || strlen($password_raw) < 6) {
        $error = "All fields must be valid and meet minimum length requirements.";
    } else {
        // Hash password with bcrypt for security
        $password = password_hash($password_raw, PASSWORD_BCRYPT);

        try {
            // Check for existing username or email
            $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $check->execute([$username, $email]);
            if ($check->fetchColumn() > 0) {
                $error = "Username or email already in use.";
            } else {
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $password]);

                // Successful registration: login the user
                session_regenerate_id(true); // mitigate session fixation
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;

                // Redirect to profile page
                header("Location: profile.php");
                exit();
            }
        } catch (PDOException $e) {
            // Log error but don’t show to user
            error_log("Registration error: " . $e->getMessage());
            $error = "Registration failed. Please try again later.";
        }
    }
}

// Include site header
require_once 'includes/header.php';
?>

<!-- ========================= -->
<!-- Registration Form -->
<!-- ========================= -->
<div class="auth-form">
    <h2>Register</h2>

    <!-- Display error message -->
    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- User registration form -->
    <form method="POST" onsubmit="return validatePassword()">
        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Register</button>
    </form>

    <!-- Link to login -->
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>
