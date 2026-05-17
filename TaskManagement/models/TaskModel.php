
<?php

function createTask($conn, $project_id, $title, $description, $assigned_to, $priority, $due_date) {
    $sql = "INSERT INTO tasks (project_id, title, description, assigned_to, priority, due_date, status) VALUES (?, ?, ?, ?, ?, ?, 'todo')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ississ", $project_id, $title, $description, $assigned_to, $priority, $due_date);
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

function getTasksByStatus($conn, $project_id, $status) {
    $sql = "SELECT t.*, u.name as assignee_name 
            FROM tasks t 
            JOIN users u ON t.assigned_to = u.id 
            WHERE t.project_id = ? AND t.status = ?
            ORDER BY t.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $project_id, $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $tasks = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = $row;
    }
    return $tasks;
}

function getTaskById($conn, $task_id) {
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function updateTaskStatus($conn, $task_id, $new_status) {
    $sql = "UPDATE tasks SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $task_id);
    return mysqli_stmt_execute($stmt);
}
?>
