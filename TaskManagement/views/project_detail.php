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
    die("<div style='text-align:center; margin-top:50px;'><h3 style='color:red;'>Error: Project not found or you don't have access to it!</h3><a href='dashboard.php' style='color:#0066cc;'>Back to Dashboard</a></div>");
}

$members = getProjectMembersWithTaskCount($conn, $project_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Project Details - <?php echo $project['name']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .project-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .navbar {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eeeeee;
        }
        .navbar a {
            text-decoration: none;
            color: #0066cc;
            margin-right: 15px;
            font-weight: bold;
        }
        .navbar a.activity-btn {
            color: #27ae60;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        h2, h3 {
            color: #333333;
            margin-top: 0;
        }
        .project-desc {
            color: #555555;
            line-height: 1.5;
        }
        .deadline {
            color: #d35400;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            color: white;
            font-size: 13px;
            margin-right: 8px;
            font-weight: bold;
        }
        .badge-todo { background-color: #7f8c8d; }
        .badge-prog { background-color: #f39c12; color: #fff; }
        .badge-done { background-color: #27ae60; }
        
        .member-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .member-list li {
            background-color: #f9f9f9;
            border: 1px solid #dddddd;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 3px;
            color: #333333;
        }
        .board-container {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    
    <div class="project-container">
        <div class="navbar">
            <a href="dashboard.php">&larr; Back to Dashboard</a>
            <a href="activity_feed.php?project_id=<?php echo $project_id; ?>" class="activity-btn">View Project Activity &rarr;</a>
        </div>

        <h2>Project: <?php echo $project['name']; ?></h2>
        <p class="project-desc"><strong>Description:</strong> <?php echo $project['description']; ?></p>
        <p><strong>Deadline:</strong> <span class="deadline"><?php echo $project['deadline']; ?></span></p>
        
        <hr style="border: 0; border-top: 1px solid #eeeeee; margin: 20px 0;">

        <h3>Task Summary</h3>
        <div style="margin-bottom: 20px;">
            <span class="badge badge-todo">To Do: <?php echo $project['todo_count']; ?></span>
            <span class="badge badge-prog">In Progress: <?php echo $project['progress_count']; ?></span>
            <span class="badge badge-done">Done: <?php echo $project['done_count']; ?></span>
        </div>
        
        <h3>Assigned Members & Task Load</h3>
        <ul class="member-list">
            <?php foreach($members as $m): ?>
                <li>
                    <strong><?php echo $m['name']; ?></strong> — Assigned Tasks: <b style="color: #0066cc;"><?php echo $m['task_count']; ?></b>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="board-container">
        <?php include 'task_board.php'; ?>
    </div>

</body>
</html>