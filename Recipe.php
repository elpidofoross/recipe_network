<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$recipe_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$recipe_id) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $comment = trim($_POST['comment']);
    $rid = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT);
    if ($comment && $rid === $recipe_id) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, text) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $recipe_id, $comment]);
            header("Location: recipe.php?id=" . $recipe_id);
            exit();
        } catch (PDOException $e) {
            error_log("Comment insert failed: " . $e->getMessage());
        }
    }
}

$stmt = $pdo->prepare("SELECT recipes.*, users.username FROM recipes JOIN users ON recipes.user_id = users.id WHERE recipes.id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE recipe_id = ? ORDER BY created_at DESC");
$stmt->execute([$recipe_id]);
$comments = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$like_count = $stmt->fetchColumn();

$is_liked = false;
if (is_logged_in()) {
    $stmt = $pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    $is_liked = $stmt->fetchColumn();
}

require_once 'includes/header.php';
?>

<div class="recipe-container" data-recipe-id="<?= $recipe_id ?>">
    <h1><?= htmlspecialchars($recipe['title']) ?></h1>
    <p class="author">By <?= htmlspecialchars($recipe['username']) ?></p>

    <?php if ($recipe['image_path']): ?>
        <img src="<?= htmlspecialchars($recipe['image_path']) ?>" class="recipe-image">
    <?php endif; ?>

    <div class="recipe-section">
        <h2>Ingredients</h2>
        <div class="recipe-box">
            <ul>
                <?php foreach (explode("\n", $recipe['ingredients']) as $line): ?>
                    <li><?= htmlspecialchars(trim($line)) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="recipe-section">
        <h2>Instructions</h2>
        <div class="recipe-box instructions-box">
            <?= nl2br(htmlspecialchars($recipe['instructions'])) ?>
        </div>
    </div>

    <div class="interaction-section">
        <div class="like-section">
            <?php if (is_logged_in()): ?>
                <button class="like-btn <?= $is_liked ? 'liked' : '' ?>" data-recipe-id="<?= $recipe_id ?>">
                    ❤️ <?= $like_count ?>
                </button>
            <?php else: ?>
                <div class="visitor-notice">
                    <p><a href="login.php">Login</a> to like this recipe</p>
                    <span class="like-count">❤️ <?= $like_count ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="comments-section">
            <h3>Comments (<?= count($comments) ?>)</h3>
            <?php if (is_logged_in()): ?>
                <form class="comment-form" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">
                    <textarea name="comment" placeholder="Add a comment..." required></textarea>
                    <button type="submit" class="btn">Post Comment</button>
                </form>
            <?php else: ?>
                <div class="visitor-notice">
                    <p><a href="login.php">Login</a> to post a comment</p>
                </div>
            <?php endif; ?>

            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment" data-comment-id="<?= $comment['id'] ?>">
                        <div class="comment-header" style="display:flex; justify-content:space-between;">
                            <div>
                                <strong><?= htmlspecialchars($comment['username']) ?></strong>
                                <span><?= date('M j, Y', strtotime($comment['created_at'])) ?></span>
                            </div>
                            <?php if ($comment['user_id'] == ($_SESSION['user_id'] ?? 0)): ?>
                                <button class="delete-comment-btn btn" style="background-color:#dc3545; font-size:0.8rem; padding:4px 8px;">Delete</button>
                            <?php endif; ?>
                        </div>
                        <p><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
