<?php
session_start();
include '../config/db.php';
include '../models/WorkspaceModel.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'create') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $owner_id = $_SESSION['user_id'];

        if (empty($name)) {
            $_SESSION['w_error'] = "Workspace Name is required!";
            header("Location: ../views/dashboard.php");
            exit();
        }

        $invite_code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

        $workspace_id = createWorkspace($conn, $name, $description, $owner_id, $invite_code);
        if ($workspace_id) {
            addWorkspaceMember($conn, $workspace_id, $owner_id); 
            $_SESSION['workspace_id'] = $workspace_id; 
            $_SESSION['w_success'] = "Workspace Created Successfully!";
        } else {
            $_SESSION['w_error'] = "Failed to create workspace!";
        }
        header("Location: ../views/dashboard.php");
        exit();
    }

    if ($action == 'join') {
        $invite_code = strtoupper(trim($_POST['invite_code']));
        $user_id = $_SESSION['user_id'];

        if (empty($invite_code)) {
            $_SESSION['w_error'] = "Invite code is required!";
            header("Location: ../views/dashboard.php");
            exit();
        }

        $workspace = getWorkspaceByCode($conn, $invite_code);
        if ($workspace) {
            if (isWorkspaceMember($conn, $workspace['id'], $user_id)) {
                $_SESSION['w_error'] = "You are already a member of this workspace!";
            } else {
                addWorkspaceMember($conn, $workspace['id'], $user_id);
                $_SESSION['workspace_id'] = $workspace['id']; 
                $_SESSION['w_success'] = "Successfully joined the workspace!";
            }
        } else {
            $_SESSION['w_error'] = "Invalid Invite Code!";
        }
        header("Location: ../views/dashboard.php");
        exit();
    }

    if ($action == 'remove_member') {
        $member_row_id = $_POST['member_row_id'];
        
        if (removeMember($conn, $member_row_id)) {
            echo "success"; 
        } else {
            echo "error";
        }
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['switch_id'])) {
    $workspace_id = $_GET['switch_id'];
    $user_id = $_SESSION['user_id'];

    if (isWorkspaceMember($conn, $workspace_id, $user_id)) {
        $_SESSION['workspace_id'] = $workspace_id; 
    }
    header("Location: ../views/dashboard.php");
    exit();
}
?>
