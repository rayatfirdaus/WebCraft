

<?php
session_start();
include '../config/db.php';
include '../models/ProjectModel.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$project_id = $_GET['project_id'];
$project = getProjectDetails($conn, $project_id);
$members = getProjectMembersWithTaskCount($conn, $project_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Feed - <?php echo $project['name']; ?></title>
    <script src="../assets/js/activity.js"></script>
</head>
<body onload="filterActivities(<?php echo $project_id; ?>)">
    <a href="dashboard.php">Back to Dashboard</a> | 
    <a href="project_detail.php?id=<?php echo $project_id; ?>">Back to Task Board</a>
    
    <h2>Project Activity Feed: <?php echo $project['name']; ?></h2>
    
    <label>Filter by Member:</label>
    <select id="member_filter" onchange="filterActivities(<?php echo $project_id; ?>)">
        <option value="">All Members</option>
        <?php foreach($members as $m): ?>
            <option value="<?php echo $m['user_id']; ?>"><?php echo $m['name']; ?></option>
        <?php endforeach; ?>
    </select>
    
    <hr>
    
    <div id="activity_list" style="max-width: 600px;">
        <p>Loading activities...</p>
    </div>
</body>
</html>