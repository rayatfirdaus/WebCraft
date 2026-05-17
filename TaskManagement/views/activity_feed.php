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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .feed-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
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
        .navbar a:hover {
            text-decoration: underline;
        }
        h2 {
            color: #333333;
            margin-top: 0;
        }
        .filter-section {
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #dddddd;
        }
        select {
            padding: 8px;
            border: 1px solid #cccccc;
            border-radius: 3px;
            font-size: 14px;
            margin-left: 10px;
        }
        #activity_list {
            margin-top: 20px;
            padding: 10px;
        }
    </style>
</head>
<body onload="filterActivities(<?php echo $project_id; ?>)">
    
    <div class="feed-container">
        <div class="navbar">
            <a href="dashboard.php">&larr; Back to Dashboard</a>
            <a href="project_detail.php?id=<?php echo $project_id; ?>">&larr; Back to Task Board</a>
        </div>
        
        <h2>Project Activity Feed: <?php echo $project['name']; ?></h2>
        
        <div class="filter-section">
            <label><strong>Filter by Member:</strong></label>
            <select id="member_filter" onchange="filterActivities(<?php echo $project_id; ?>)">
                <option value="">All Members</option>
                <?php foreach($members as $m): ?>
                    <option value="<?php echo $m['user_id']; ?>"><?php echo $m['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div id="activity_list">
            <p style="color: gray; text-align: center;">Loading activities...</p>
        </div>
    </div>
    
</body>
</html>