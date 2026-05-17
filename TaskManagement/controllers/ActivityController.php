<?php
session_start();
include '../config/db.php';
include '../config/helpers.php';
include '../models/ActivityModel.php';
include '../models/TaskModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["ok" => false, "message" => "Unauthorized"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// --- POST COMMENT ---
if ($method == 'POST' && isset($_POST['action']) && $_POST['action'] == 'post_comment') {
    
    $task_id = $_POST['task_id'];
    $body = trim($_POST['body']);
    $user_id = $_SESSION['user_id'];

    if (empty($body)) {
        echo json_encode([
            "ok" => false,
            "message" => "Comment cannot be empty"
        ]);
        exit();
    }

    $comment_id = addComment($conn, $task_id, $user_id, $body);

    if ($comment_id) {

        $comment = getCommentById($conn, $comment_id);

        echo json_encode([
            "ok" => true,
            "comment" => $comment
        ]);

    } else {

        echo json_encode([
            "ok" => false,
            "message" => "Failed to post comment"
        ]);
    }

    exit();
}

// --- DELETE COMMENT ---
if ($method == 'DELETE' || 
   (isset($_GET['action']) && $_GET['action'] == 'delete_comment')) {

    if ($method == 'DELETE') {

        parse_str(file_get_contents("php://input"), $_DELETE);
        $comment_id = $_DELETE['comment_id'];

    } else {

        $comment_id = $_GET['comment_id'];
    }

    $comment = getCommentById($conn, $comment_id);

    // Only comment owner can delete
    if ($comment && $comment['user_id'] == $_SESSION['user_id']) {

        deleteComment($conn, $comment_id);

        echo json_encode([
            "ok" => true
        ]);

    } else {

        echo json_encode([
            "ok" => false,
            "message" => "Unauthorized to delete this comment"
        ]);
    }

    exit();
}
?>
