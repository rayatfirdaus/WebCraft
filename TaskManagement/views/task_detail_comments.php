

<?php
// views/task_detail_comments.php
session_start();
include '../config/db.php';
include '../models/TaskModel.php';     // Task er details anar jonno (Member 3 er file)
include '../models/ActivityModel.php'; // Comments er queries er jonno (Member 4 er file)

// Auth check - login na thakle login page e pathay dibe
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// URL parameter theke task_id secure kora
if (!isset($_GET['task_id']) || empty($_GET['task_id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = intval($_GET['task_id']); // Sanitize integer query parameter
$current_user_id = $_SESSION['user_id'];

// 1. Task er detailed record fetch kora
$task = getTaskById($conn, $task_id);
if (!$task) {
    die("Task not found or access denied.");
}

// 2. Oi task er ager shob comments load kora
$comments = getCommentsByTask($conn, $task_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Details & Comments</title>
    <script src="../assets/js/activity.js"></script>
</head>
<body style="font-family: Arial, sans-serif; margin: 20px; background: #fdfdfd;">

    <a href="dashboard.php" style="text-decoration: none; color: #3498db;">&larr; Back to Dashboard</a> | 
    <a href="project_detail.php?id=<?php echo $task['project_id']; ?>" style="text-decoration: none; color: #3498db;">Back to Project Board</a>
    
    <hr>

    <div style="background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <h2 style="margin-top: 0;"><?php echo htmlspecialchars($task['title']); ?></h2>
        <p style="color: #555;"><b>Description:</b> <?php echo htmlspecialchars($task['description']); ?></p>
        <p><b>Priority:</b> <span style="text-transform: uppercase; font-weight: bold;"><?php echo $task['priority']; ?></span></p>
        <p><b>Due Date:</b> <i><?php echo $task['due_date']; ?></i></p>
        <p><b>Status:</b> <u><?php echo ucfirst($task['status']); ?></u></p>
    </div>

    <hr>

    <h3>Task Discussion Thread</h3>

    <div id="comment_thread_<?php echo $task_id; ?>" style="max-width: 600px; margin-bottom: 20px;">
        <?php if (empty($comments)): ?>
            <p id="no-comment-msg" style="color: gray;">No comments posted yet. Start the conversation!</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div id="comment_<?php echo $comment['id']; ?>" style="background: #f9f9f9; padding: 10px; margin-bottom: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <b><?php echo htmlspecialchars($comment['author_name']); ?></b>
                        <small style="color: gray;"><?php echo $comment['created_at']; ?></small>
                    </div>
                    <p style="margin: 8px 0; color: #333;"><?php echo htmlspecialchars($comment['body']); ?></p>
                    
                    <?php if ($comment['user_id'] == $current_user_id): ?>
                        <a href="javascript:void(0)" onclick="deleteComment(<?php echo $comment['id']; ?>)" style="color: red; font-size: 12px; text-decoration: none;">Delete Comment</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="max-width: 600px; background: #f4f4f4; padding: 15px; border: 1px solid #ccc; border-radius: 4px;">
        <h4>Post a Comment</h4>
        <textarea id="comment_body_<?php echo $task_id; ?>" rows="4" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; resize: vertical;" placeholder="Write your professional updates or feedback here..."></textarea><br><br>
        
        <button onclick="postComment(<?php echo $task_id; ?>)" style="background: #27ae60; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Post Comment</button>
    </div>

</body>
</html>