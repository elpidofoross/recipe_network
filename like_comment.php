<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
header('Content-Type: application/json');

try {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!is_logged_in()) {
        throw new Exception("User not authenticated");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if (!hash_equals($_SESSION['csrf_token'], $input['csrf_token'] ?? '')) {
        throw new Exception("Invalid CSRF token");
    }

    $recipe_id = filter_var($input['recipe_id'] ?? null, FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    if (!$recipe_id || !$user_id) {
        throw new Exception("Invalid request parameters");
    }

    // ================
    // 📝 COMMENT POST
    // ================
    if (isset($input['post_comment'])) {
        $text = trim($input['comment'] ?? '');

        if (mb_strlen($text) < 1 || mb_strlen($text) > 2000) {
            throw new Exception("Comment must be between 1 and 2000 characters.");
        }

        $stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, text) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $recipe_id, $text]);

        echo json_encode([
            'status' => 'success',
            'comment' => [
                'id' => $pdo->lastInsertId(),
                'text' => nl2br($text),
                'username' => $_SESSION['username'],
                'date' => date('M j, Y'),
                'user_id' => $user_id
            ]
        ]);
        exit;
    }

    // ================
    // 🗑️ COMMENT DELETE
    // ================
    if (isset($input['delete_comment'])) {
        $comment_id = filter_var($input['comment_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$comment_id) {
            throw new Exception("Invalid comment ID.");
        }

        // Check ownership
        $stmt = $pdo->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
        $stmt->execute([$comment_id, $user_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Not authorized to delete this comment.");
        }

        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);

        echo json_encode([
            'status' => 'success',
            'deleted_id' => $comment_id
        ]);
        exit;
    }

    // ================
    // ❤️ LIKE / UNLIKE
    // ================
    $action = $input['action'] ?? null;

    if ($action === 'like') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO likes (user_id, recipe_id) VALUES (?, ?)");
    } elseif ($action === 'unlike') {
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND recipe_id = ?");
    }

    if (isset($stmt)) {
        $stmt->execute([$user_id, $recipe_id]);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
        $like_count = $stmt->fetchColumn();

        echo json_encode([
            'status' => 'success',
            'likes' => $like_count
        ]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
