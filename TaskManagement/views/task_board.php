<?php
include_once '../config/db.php';
include_once '../models/TaskModel.php';
include_once '../models/ProjectModel.php'; 

if (!isset($project_id)) {
    if (isset($_GET['project_id']) && !empty($_GET['project_id'])) {
        $project_id = intval($_GET['project_id']);
    } else {
        die("<b style='color:red;'>Error: Project Context Missing! Invalid Access.</b>");
    }
}

$project_members = getProjectMembersWithTaskCount($conn, $project_id);
$todo_tasks = getTasksByStatus($conn, $project_id, 'todo');
$in_progress_tasks = getTasksByStatus($conn, $project_id, 'in-progress');
$done_tasks = getTasksByStatus($conn, $project_id, 'done');

function renderTaskCard($task) {
    $colors = ['low' => 'gray', 'medium' => '#f39c12', 'high' => 'red'];
    $badge_color = $colors[$task['priority']];
    $initials = strtoupper(substr($task['assignee_name'], 0, 2));

    echo "<div class='task-card' id='task-{$task['id']}' data-due-date='{$task['due_date']}' data-status='{$task['status']}' style='border:1px solid #ddd; padding:10px; margin-bottom:10px; border-radius:5px; background:#fff;'>";
    echo "<b>{$task['title']}</b><br>";
    echo "<small>Due: {$task['due_date']}</small><br>";
    echo "<span style='background:{$badge_color}; color:white; padding:2px 5px; font-size:10px; border-radius:3px;'>{$task['priority']}</span> ";
    echo "<span style='background:#eee; padding:2px 5px; font-size:10px; border-radius:50%;'>{$initials}</span>";
    
    echo "<br><br><a href='task_detail_comments.php?task_id={$task['id']}' style='font-size:12px; color:#3498db; text-decoration:none;'>View Details & Comments</a>";
    
    echo "<div style='margin-top:10px; text-align:right;'>";
    if ($task['status'] != 'todo') {
        echo "<button onclick='moveTask({$task['id']}, \"{$task['status']}\", \"left\")'>&larr;</button> ";
    }
    if ($task['status'] != 'done') {
        echo "<button onclick='moveTask({$task['id']}, \"{$task['status']}\", \"right\")'>&rarr;</button>";
    }
    echo "</div>";
    echo "</div>";
}
?>

<hr>
<h3>Kanban Task Board</h3>
<button onclick="document.getElementById('taskModal').style.display='block'" style="margin-bottom:15px; padding:5px 10px;">+ New Task</button>

<div style="display:flex; gap:20px;">
    <div style="flex:1; background:#f9f9f9; padding:10px; border-radius:5px;" id="col-todo">
        <h4 style="border-bottom:2px solid gray; padding-bottom:5px;">To Do</h4>
        <?php foreach($todo_tasks as $t) renderTaskCard($t); ?>
    </div>

    <div style="flex:1; background:#e8f4f8; padding:10px; border-radius:5px;" id="col-in-progress">
        <h4 style="border-bottom:2px solid #3498db; padding-bottom:5px;">In Progress</h4>
        <?php foreach($in_progress_tasks as $t) renderTaskCard($t); ?>
    </div>

    <div style="flex:1; background:#e9f7ef; padding:10px; border-radius:5px;" id="col-done">
        <h4 style="border-bottom:2px solid green; padding-bottom:5px;">Done</h4>
        <?php foreach($done_tasks as $t) renderTaskCard($t); ?>
    </div>
</div>

<div id="taskModal" style="display:none; position:fixed; top:20%; left:30%; background:white; padding:20px; border:1px solid #000; z-index:100; width:400px; box-shadow: 0px 4px 6px rgba(0,0,0,0.1);">
    <h3>Create New Task</h3>
    <form id="createTaskForm" onsubmit="event.preventDefault(); submitTaskForm();">
        <input type="hidden" id="task_project_id" value="<?php echo $project_id; ?>">
        
        <label>Title:</label><br>
        <input type="text" id="task_title" required style="width:100%;"><br><br>
        
        <label>Description:</label><br>
        <textarea id="task_desc" style="width:100%;"></textarea><br><br>
        
        <label>Assign To:</label><br>
        <select id="task_assignee" required style="width:100%;">
            <?php foreach($project_members as $m): ?>
                <option value="<?php echo $m['user_id']; ?>"><?php echo $m['name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Priority:</label><br>
        <input type="radio" name="priority" value="low" checked> Low
        <input type="radio" name="priority" value="medium"> Medium
        <input type="radio" name="priority" value="high"> High<br><br>

        <label>Due Date:</label><br>
        <input type="date" id="task_due_date" required style="width:100%;"><br><br>

        <button type="submit">Save Task</button>
        <button type="button" onclick="document.getElementById('taskModal').style.display='none'">Cancel</button>
    </form>
</div>

<script src="../assets/js/task.js"></script>
