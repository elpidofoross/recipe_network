<?php
// =======================================
// delete_recipe.php - Delete user recipe
// =======================================

require_once 'includes/config.php'; // DB connection
require_once 'includes/auth.php';   // Auth functions
require_login();                    // Only logged-in users allowed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    $csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        die("Invalid CSRF token");
    }

    // Sanitize recipe ID from POST
    $recipe_id = filter_var($_POST['recipe_id'] ?? null, FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    if (!$recipe_id) {
        die("Invalid request");
    }

    // =========================
    // 🛡️ Verify user ownership
    // =========================
    $stmt = $pdo->prepare("SELECT image_path FROM recipes WHERE id = ? AND user_id = ?");
    $stmt->execute([$recipe_id, $user_id]);
    $recipe = $stmt->fetch();

    if (!$recipe) {
        die("Unauthorized access.");
    }

    // =========================
    //  Delete associated image
    // =========================
    if (!empty($recipe['image_path']) && file_exists($recipe['image_path'])) {
        unlink($recipe['image_path']);
    }

    // =========================
    //  Cascade-like deletes
    // =========================
    $pdo->prepare("DELETE FROM comments WHERE recipe_id = ?")->execute([$recipe_id]);
    $pdo->prepare("DELETE FROM likes WHERE recipe_id = ?")->execute([$recipe_id]);

    // Finally, delete recipe itself
    $pdo->prepare("DELETE FROM recipes WHERE id = ?")->execute([$recipe_id]);

    // Redirect back to profile with success flag
    header("Location: profile.php?deleted=1");
    exit;
}
?>
