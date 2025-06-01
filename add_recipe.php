<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // --- CLEAN INPUTS (no filters that destroy multibyte chars) ---
    $title = trim($_POST['title'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $user_id = $_SESSION['user_id'];
    $image_path = null;

    // --- VALIDATION ---
    if (mb_strlen($title) < 3 || mb_strlen($title) > 150 ||
        mb_strlen($ingredients) < 10 || mb_strlen($ingredients) > 5000 ||
        mb_strlen($instructions) < 10 || mb_strlen($instructions) > 10000) {
        $error = "Παρακαλώ συμπληρώστε όλα τα πεδία με έγκυρο περιεχόμενο.";
    }

    // --- IMAGE UPLOAD ---
    if (!$error && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $detected_type = mime_content_type($_FILES['image']['tmp_name']);
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($detected_type, $allowed_types) && in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
            $upload_dir = 'assets/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $safe_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES['image']['name']));
            $filename = uniqid('recipe_', true) . '_' . $safe_name;
            $destination = $upload_dir . $filename;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $error = "Απέτυχε η αποθήκευση της εικόνας.";
            } else {
                $image_path = $destination;
            }
        } else {
            $error = "Μόνο εικόνες JPG ή PNG επιτρέπονται!";
        }
    }

    // --- INSERT TO DATABASE ---
    if (!$error) {
        try {
            $stmt = $pdo->prepare("INSERT INTO recipes (user_id, title, ingredients, instructions, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $ingredients, $instructions, $image_path]);

            header("Location: profile.php");
            exit();
        } catch (PDOException $e) {
            error_log("Recipe insert failed: " . $e->getMessage());
            $error = "Αποτυχία αποθήκευσης. Δοκιμάστε ξανά.";
        }
    }
}

require_once 'includes/header.php';
?>

<!-- ==================== -->
<!-- New Recipe Form -->
<!-- ==================== -->
<div class="recipe-form">
    <h2>Create New Recipe</h2>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

        <div class="form-group">
            <label>Recipe Title</label>
            <input type="text" name="title" required minlength="3" maxlength="150">
        </div>

        <div class="form-group">
           <label>Ingredients (one per line)</label>
            <textarea name="ingredients" rows="5" required minlength="10" maxlength="5000"></textarea>
        </div>

        <div class="form-group">
            <label>Instructions</label>
            <textarea name="instructions" rows="10" required minlength="10" maxlength="10000"></textarea>
        </div>

        <div class="form-group">
            <label>Recipe Image (optional)</label>
            <input type="file" name="image" accept="image/jpeg,image/png">
        </div>

       <button type="submit" class="btn">Publish Recipe</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
