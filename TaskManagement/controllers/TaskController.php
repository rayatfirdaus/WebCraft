
<?php

session_start();
include '../config/db.php';
include '../config/helpers.php';
include '../models/TaskModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["ok" => false, "message" => "Unauthorized"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    // --- CREATE TASK (AJAX) ---
    if ($action == 'create_task') {
        $project_id = $_POST['project_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $assigned_to = $_POST['assigned_to'];
        $priority = $_POST['priority'];
        $due_date = $_POST['due_date'];

        if (empty($title) || empty($due_date)) {
            echo json_encode(["ok" => false, "message" => "Title and Due Date are required"]);
            exit();
        }

        $task_id = createTask($conn, $project_id, $title, $description, $assigned_to, $priority, $due_date);
        
        if ($task_id) {
            echo json_encode(["ok" => true, "message" => "Task Created"]);
        } else {
            echo json_encode(["ok" => false, "message" => "Database Error"]);
        }
        exit();
    }

    // --- MOVE TASK STATUS (AJAX) ---
    if ($action == 'move_task') {
        $task_id = $_POST['task_id'];
        $new_status = $_POST['new_status'];
        
        $task = getTaskById($conn, $task_id);
        if ($task && updateTaskStatus($conn, $task_id, $new_status)) {
            
            // Log Activity requirements fulfilled
            $action_text = "Task '" . $task['title'] . "' moved to " . ucfirst($new_status);
            log_activity($conn, $task['project_id'], $_SESSION['user_id'], $action_text);

            echo json_encode(["ok" => true, "new_status" => $new_status]);
        } else {
            echo json_encode(["ok" => false, "message" => "Failed to update status"]);
        }
        exit();
    }
}
?>
