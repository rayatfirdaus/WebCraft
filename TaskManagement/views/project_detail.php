<?php
session_start();
include '../config/db.php';
include '../models/ProjectModel.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$project_id = intval($_GET['id']);
$project = getProjectDetails($conn, $project_id);

if (!$project) {
    die("<h3 style='color:red;'>Error: Project not found or you don't have access to it!</h3><a href='dashboard.php'>Back to Dashboard</a>");
}

$members = getProjectMembersWithTaskCount($conn, $project_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Project Details - <?php echo $project['name']; ?></title>
</head>
<body style="font-family: Arial, sans-serif; margin: 20px;">
    
    <div style="margin-bottom: 20px; background: #f4f4f4; padding: 10px; border-radius: 5px;">
        <a href="dashboard.php" style="text-decoration:none; color:#3498db; margin-right: 15px;">&larr; Back to Dashboard</a> | 
        <a href="activity_feed.php?project_id=<?php echo $project_id; ?>" style="text-decoration:none; color:#27ae60; font-weight:bold; margin-left: 15px;">View Project Activity</a>
    </div>

    <h2>Project: <?php echo $project['name']; ?></h2>
    <p>Description: <?php echo $project['description']; ?></p>
    <p>Deadline: <b><?php echo $project['deadline']; ?></b></p>
    
    <h3>Task Summary Badges</h3>
    <span style="background:gray; color:white; padding:5px; margin:2px;">To Do: <?php echo $project['todo_count']; ?></span>
    <span style="background:#f39c12; color:black; padding:5px; margin:2px;">In Progress: <?php echo $project['progress_count']; ?></span>
    <span style="background:green; color:white; padding:5px; margin:2px;">Done: <?php echo $project['done_count']; ?></span>
    
    <h3>Assigned Members & Task Load</h3>
    <ul>
        <?php foreach($members as $m): ?>
            <li><?php echo $m['name']; ?> — Assigned Tasks: <b><?php echo $m['task_count']; ?></b></li>
        <?php endforeach; ?>
    </ul>

    <?php include 'task_board.php'; ?>

</body>
</html>