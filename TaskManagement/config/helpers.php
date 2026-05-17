<?php

function log_activity($conn, $project_id, $user_id, $action_text) {
    $sql = "INSERT INTO activity_logs (project_id, user_id, action_text) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $project_id, $user_id, $action_text);
    mysqli_stmt_execute($stmt);
}
?>
