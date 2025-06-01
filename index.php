<?php
// =======================================
// index.php - Homepage (list all recipes)
// =======================================

require_once 'includes/config.php'; // DB connection

// =========================
// Fetch latest recipes
// =========================
try {
    $stmt = $pdo->query("
        SELECT recipes.*, users.username 
        FROM recipes 
        JOIN users ON recipes.user_id = users.id 
        ORDER BY recipes.created_at DESC
    ");
    $recipes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Index recipe fetch failed: " . $e->getMessage());
    $recipes = [];
}

// =========================
// Load site header
// =========================
require_once 'includes/header.php';
?>

<!-- =========================
     Hero section (page intro)
========================= -->
<div class="hero">
    <h1>Discover Recipes</h1>
    <p>Explore culinary creations from our community</p>
</div>

<!-- =========================
     Recipes grid display
========================= -->
<div class="recipe-grid">
    <?php foreach ($recipes as $recipe): ?>
        <div class="recipe-card">
            <?php 
            // Show recipe image or fallback to default
            if (!empty($recipe['image_path']) && file_exists($recipe['image_path'])): ?>
                <img src="<?= htmlspecialchars($recipe['image_path']) ?>" class="recipe-image" alt="<?= htmlspecialchars($recipe['title']) ?>">
            <?php else: ?>
                <img src="assets/img/default_recipe.jpg" class="recipe-image" alt="Default recipe image">
            <?php endif; ?>

            <div class="recipe-content">
                <h3><?= htmlspecialchars($recipe['title']) ?></h3>
                <p class="author">By <?= htmlspecialchars($recipe['username']) ?></p>
                <a href="recipe.php?id=<?= (int)$recipe['id'] ?>" class="btn">View Recipe</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php 
// =========================
// Load site footer
// =========================
require_once 'includes/footer.php'; 
?>
