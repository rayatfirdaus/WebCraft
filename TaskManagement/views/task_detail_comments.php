<?php
session_start();
include '../config/db.php';
include '../models/TaskModel.php';
include '../models/ActivityModel.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['task_id']) || empty($_GET['task_id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = intval($_GET['task_id']);
$current_user_id = $_SESSION['user_id'];

$task = getTaskById($conn, $task_id);
if (!$task) {
    die("Task not found or access denied.");
}

$comments = getCommentsByTask($conn, $task_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Details & Comments</title>
    <script src="../assets/js/activity.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .main-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .navbar {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #cccccc;
        }
        .navbar a {
            text-decoration: none;
            color: #0066cc;
            margin-right: 15px;
            font-weight: bold;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .task-card {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }
        .task-card h2 {
            margin-top: 0;
            color: #333333;
        }
        .badge-priority {
            background-color: #eeeeee;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            color: #333333;
        }
        .badge-status {
            background-color: #3498db;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            text-transform: capitalize;
        }
        .comment-box {
            background-color: #ffffff;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
            border-radius: 4px;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .comment-header b {
            color: #2c3e50;
        }
        .comment-header small {
            color: #888888;
        }
        .delete-link {
            color: #e74c3c;
            font-size: 12px;
            text-decoration: none;
            font-weight: bold;
        }
        .delete-link:hover {
            text-decoration: underline;
        }
        .comment-form-container {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 3px;
            box-sizing: border-box;
            resize: vertical;
            margin-bottom: 10px;
            font-family: inherit;
        }
        button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #219150;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="navbar">
            <a href="dashboard.php">&larr; Back to Dashboard</a>
            <a href="project_detail.php?id=<?php echo $task['project_id']; ?>">&larr; Back to Project Board</a>
        </div>
        
        <div class="task-card">
            <h2><?php echo htmlspecialchars($task['title']); ?></h2>
            <p style="color: #555555;"><b>Description:</b> <?php echo htmlspecialchars($task['description']); ?></p>
            <p><b>Priority:</b> <span class="badge-priority"><?php echo $task['priority']; ?></span></p>
            <p><b>Due Date:</b> <i style="color: #d35400; font-weight: bold;"><?php echo $task['due_date']; ?></i></p>
            <p><b>Status:</b> <span class="badge-status"><?php echo htmlspecialchars($task['status']); ?></span></p>
        </div>

        <h3 style="color: #333333;">Task Discussion Thread</h3>

        <div id="comment_thread_<?php echo $task_id; ?>" style="margin-bottom: 20px;">
            <?php if (empty($comments)): ?>
                <p id="no-comment-msg" style="color: gray; background-color: #ffffff; padding: 15px; border-radius: 5px; text-align: center; border: 1px solid #cccccc;">No comments posted yet. Start the conversation!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div id="comment_<?php echo $comment['id']; ?>" class="comment-box">
                        <div class="comment-header">
                            <b><?php echo htmlspecialchars($comment['author_name']); ?></b>
                            <small><?php echo $comment['created_at']; ?></small>
                        </div>
                        <p style="margin: 0 0 10px 0; color: #444444;"><?php echo htmlspecialchars($comment['body']); ?></p>
                        
                        <?php if ($comment['user_id'] == $current_user_id): ?>
                            <a href="javascript:void(0)" onclick="deleteComment(<?php echo $comment['id']; ?>)" class="delete-link">Delete Comment</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="comment-form-container">
            <h4 style="margin-top: 0; color: #333333;">Post a Comment</h4>
            <textarea id="comment_body_<?php echo $task_id; ?>" rows="4" placeholder="Write your professional updates or feedback here..."></textarea>
            <button onclick="postComment(<?php echo $task_id; ?>)">Post Comment</button>
        </div>
    </div>

</body>
</html>