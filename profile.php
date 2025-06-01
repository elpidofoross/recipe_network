<?php
// Include configuration (DB connection, constants) and authentication utilities
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Require the user to be logged in; redirect to login if not
require_login();

try {
    // Prepare and execute SQL to fetch all recipes of the current user
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $recipes = $stmt->fetchAll();
} catch (PDOException $e) {
    // Log any database error and fallback to an empty recipe list
    error_log("Profile recipe fetch failed: " . $e->getMessage());
    $recipes = [];
}

// Include the page header (HTML template)
require_once 'includes/header.php';
?>

<!-- ========================= -->
<!-- User Profile Page -->
<!-- ========================= -->
<div class="profile-header">
    <!-- Display welcome message with sanitized username -->
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <!-- Link to add a new recipe -->
    <a href="add_recipe.php" class="btn">Add New Recipe</a>
</div>

<!-- Display success message if a recipe was deleted -->
<?php if (isset($_GET['deleted'])): ?>
    <div class="alert success">Recipe deleted successfully.</div>
<?php endif; ?>

<!-- Recipes Grid -->
<div class="recipe-grid">
    <!-- Check if the user has recipes -->
    <?php if (count($recipes) > 0): ?>
        <?php foreach ($recipes as $recipe): ?>
            <div class="recipe-card">
                <!-- Display recipe image or fallback to default image -->
                <?php if (!empty($recipe['image_path'])): ?>
                    <img src="<?= htmlspecialchars($recipe['image_path']) ?>" class="recipe-image" alt="Recipe image">
                <?php else: ?>
                    <img src="assets/img/default_recipe.jpg" class="recipe-image" alt="Default image">
                <?php endif; ?>

                <!-- Display recipe title -->
                <h3><?= htmlspecialchars($recipe['title']) ?></h3>

                <!-- Buttons for viewing and deleting recipe -->
                <div class="recipe-meta" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <!-- View Recipe button -->
                    <a href="recipe.php?id=<?= (int)$recipe['id'] ?>" class="btn">View Recipe</a>

                    <!-- Delete Recipe form with CSRF protection and confirmation prompt -->
                    <form action="delete_recipe.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this recipe?');">
                        <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <button class="btn" style="background-color:#dc3545;">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Message if user has no recipes -->
        <p>No recipes found. Start by <a href="add_recipe.php">adding one</a>!</p>
    <?php endif; ?>
</div>

<!-- Include the page footer (HTML template) -->
<?php require_once 'includes/footer.php'; ?>
