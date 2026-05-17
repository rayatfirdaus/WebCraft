
<?php
// models/ActivityModel.php

// 1. Comment Add Kora
function addComment($conn, $task_id, $user_id, $body) {
    $sql = "INSERT INTO comments (task_id, user_id, body) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $task_id, $user_id, $body);
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

// 2. Specific Comment Fetch Kora (AJAX Response er jonno)
function getCommentById($conn, $comment_id) {
    $sql = "SELECT c.*, u.name as author_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// 3. Task er shob comments ana
function getCommentsByTask($conn, $task_id) {
    $sql = "SELECT c.*, u.name as author_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.task_id = ? ORDER BY c.created_at ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) $comments[] = $row;
    return $comments;
}

// 4. Delete Comment
function deleteComment($conn, $comment_id) {
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    return mysqli_stmt_execute($stmt);
}

// 5. Activity Feed Fetch Kora (With Dynamic Filter & Max 50 rows)
function getProjectActivities($conn, $project_id, $filter_user_id = "") {
    $sql = "SELECT a.*, u.name as user_name FROM activity_logs a JOIN users u ON a.user_id = u.id WHERE a.project_id = ?";
    
    if (!empty($filter_user_id)) {
        $sql .= " AND a.user_id = ?";
    }
    $sql .= " ORDER BY a.created_at DESC LIMIT 50";

    $stmt = mysqli_prepare($conn, $sql);
    
    if (!empty($filter_user_id)) {
        mysqli_stmt_bind_param($stmt, "ii", $project_id, $filter_user_id);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $project_id);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $activities = [];
    while ($row = mysqli_fetch_assoc($result)) $activities[] = $row;
    return $activities;
}

// 6. Time Ago function (e.g., "2 hours ago")
function time_ago($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return "Just now";
    if ($diff < 3600) return round($diff / 60) . " mins ago";
    if ($diff < 86400) return round($diff / 3600) . " hours ago";
    return round($diff / 86400) . " days ago";
}
?>