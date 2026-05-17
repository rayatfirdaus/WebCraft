<?php
function createWorkspace($conn, $name, $description, $owner_id, $invite_code) {
    $sql = "INSERT INTO workspaces (name, description, owner_id, invite_code) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssis", $name, $description, $owner_id, $invite_code);
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

function addWorkspaceMember($conn, $workspace_id, $user_id) {
    $sql = "INSERT INTO workspace_members (workspace_id, user_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $workspace_id, $user_id);
    return mysqli_stmt_execute($stmt);
}

function getWorkspaceByCode($conn, $invite_code) {
    $sql = "SELECT * FROM workspaces WHERE invite_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $invite_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

function isWorkspaceMember($conn, $workspace_id, $user_id) {
    $sql = "SELECT id FROM workspace_members WHERE workspace_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $workspace_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}

function getUserWorkspaces($conn, $user_id) {
    $sql = "SELECT w.* FROM workspaces w 
            JOIN workspace_members wm ON w.id = wm.workspace_id 
            WHERE wm.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $workspaces = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $workspaces[] = $row;
    }
    return $workspaces;
}

function getWorkspaceMembers($conn, $workspace_id) {
    $sql = "SELECT wm.id AS member_row_id, u.name, u.email, wm.joined_at, w.owner_id, u.id AS user_id 
            FROM workspace_members wm 
            JOIN users u ON wm.user_id = u.id 
            JOIN workspaces w ON wm.workspace_id = w.id
            WHERE wm.workspace_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $workspace_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $members = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $members[] = $row;
    }
    return $members;
}

function removeMember($conn, $member_row_id) {
    $sql = "DELETE FROM workspace_members WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $member_row_id);
    return mysqli_stmt_execute($stmt);
}
?>