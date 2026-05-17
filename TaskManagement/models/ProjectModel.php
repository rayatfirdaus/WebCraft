<?php
function createProject($conn, $workspace_id, $name, $description, $deadline, $color_label) {
    $sql = "INSERT INTO projects (workspace_id, name, description, deadline, color_label) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issss", $workspace_id, $name, $description, $deadline, $color_label);
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

function addProjectMember($conn, $project_id, $user_id) {
    $sql = "INSERT INTO project_members (project_id, user_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $project_id, $user_id);
    return mysqli_stmt_execute($stmt);
}

function updateProject($conn, $project_id, $name, $description, $deadline, $color_label) {
    $sql = "UPDATE projects SET name = ?, description = ?, deadline = ?, color_label = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $name, $description, $deadline, $color_label, $project_id);
    return mysqli_stmt_execute($stmt);
}

function clearProjectMembers($conn, $project_id) {
    $sql = "DELETE FROM project_members WHERE project_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $project_id);
    return mysqli_stmt_execute($stmt);
}

function archiveProject($conn, $project_id) {
    $sql = "UPDATE projects SET is_archived = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $project_id);
    return mysqli_stmt_execute($stmt);
}

function getProjectsByWorkspace($conn, $workspace_id, $is_archived) {
    $sql = "SELECT p.*, 
            (SELECT COUNT(id) FROM tasks WHERE project_id = p.id) as total_tasks,
            (SELECT COUNT(id) FROM tasks WHERE project_id = p.id AND status = 'done') as done_tasks
            FROM projects p 
            WHERE p.workspace_id = ? AND p.is_archived = ? 
            ORDER BY p.created_at DESC";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $workspace_id, $is_archived);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $projects = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $projects[] = $row;
    }
    return $projects;
}

function getProjectDetails($conn, $project_id) {
    $sql = "SELECT *,
            (SELECT COUNT(id) FROM tasks WHERE project_id = ? AND status='todo') as todo_count,
            (SELECT COUNT(id) FROM tasks WHERE project_id = ? AND status='in-progress') as progress_count,
            (SELECT COUNT(id) FROM tasks WHERE project_id = ? AND status='done') as done_count
            FROM projects WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $project_id, $project_id, $project_id, $project_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function getProjectMembersWithTaskCount($conn, $project_id) {
    $sql = "SELECT u.name, u.id as user_id,
            (SELECT COUNT(id) FROM tasks WHERE project_id = ? AND assigned_to = u.id) as task_count
            FROM project_members pm
            JOIN users u ON pm.user_id = u.id
            WHERE pm.project_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $project_id, $project_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $members = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $members[] = $row;
    }
    return $members;
}

function isProjectMember($conn, $project_id, $user_id) {
    $sql = "SELECT id FROM project_members WHERE project_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $project_id, $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0;
}
?>