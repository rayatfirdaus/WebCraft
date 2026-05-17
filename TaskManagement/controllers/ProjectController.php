<?php
session_start();
include '../config/db.php';
include '../models/ProjectModel.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['workspace_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'save_project') {
        $workspace_id = $_SESSION['workspace_id'];
        $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : null;
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $deadline = $_POST['deadline'];
        $color_label = $_POST['color_label'];
        $members = isset($_POST['members']) ? $_POST['members'] : array();

        if (empty($name) || empty($deadline) || empty($color_label)) {
            $_SESSION['p_error'] = "Name, Deadline and Color are required fields!";
            header("Location: ../views/project_form.php" . ($project_id ? "?id=$project_id" : ""));
            exit();
        }
        
        if (empty($members)) {
            $_SESSION['p_error'] = "At least one team member must be assigned!";
            header("Location: ../views/project_form.php" . ($project_id ? "?id=$project_id" : ""));
            exit();
        }

        if ($project_id) {
            updateProject($conn, $project_id, $name, $description, $deadline, $color_label);
            clearProjectMembers($conn, $project_id);
            $saved_id = $project_id;
        } else {
            $saved_id = createProject($conn, $workspace_id, $name, $description, $deadline, $color_label);
        }

        if ($saved_id) {
            foreach ($members as $user_id) {
                addProjectMember($conn, $saved_id, $user_id);
            }
            $_SESSION['p_success'] = "Project saved successfully!";
            header("Location: ../views/dashboard.php");
        } else {
            $_SESSION['p_error'] = "Database operation failed!";
            header("Location: ../views/project_form.php");
        }
    }

    if ($action == 'archive') {
        $project_id = $_POST['project_id'];
        if (archiveProject($conn, $project_id)) {
            $_SESSION['p_success'] = "Project archived successfully!";
        }
        header("Location: ../views/dashboard.php");
    }
}
?>
