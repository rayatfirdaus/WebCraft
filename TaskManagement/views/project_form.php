<?php
session_start();
include '../config/db.php';
include '../models/WorkspaceModel.php';
include '../models/ProjectModel.php';

if (!isset($_SESSION['workspace_id'])) {
    header("Location: dashboard.php");
    exit();
}

$workspace_id = $_SESSION['workspace_id'];
$project_id = isset($_GET['id']) ? $_GET['id'] : null;

$p_name = ""; $p_desc = ""; $p_deadline = ""; $p_color = "#3498db"; 

if ($project_id) {
    $project = getProjectDetails($conn, $project_id);
    if ($project) {
        $p_name = $project['name'];
        $p_desc = $project['description'];
        $p_deadline = $project['deadline'];
        $p_color = $project['color_label'];
    }
}

$workspace_members = getWorkspaceMembers($conn, $workspace_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $project_id ? "Edit Project" : "Create Project"; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333333;
            text-align: center;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 15px;
            margin-top: 0;
        }
        label {
            font-weight: bold;
            color: #555555;
        }
        input[type="text"], input[type="date"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            height: 80px;
        }
        .selection-group {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #dddddd;
            border-radius: 3px;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .selection-group label {
            font-weight: normal;
            margin-right: 15px;
            cursor: pointer;
        }
        .member-checkbox {
            display: block;
            margin-bottom: 8px;
        }
        button[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        a.cancel-btn {
            text-decoration: none;
            color: #d35400;
            margin-left: 15px;
            font-weight: bold;
        }
        a.cancel-btn:hover {
            text-decoration: underline;
        }
        .error-msg {
            color: red;
            background-color: #ffe6e6;
            padding: 10px;
            border: 1px solid red;
            border-radius: 3px;
            text-align: center;
        }
    </style>
</head>
<body>
    
    <div class="form-container">
        <h2><?php echo $project_id ? "Edit Project" : "Create New Project"; ?></h2>
        
        <?php if(isset($_SESSION['p_error'])): ?>
            <p class="error-msg"><?php echo $_SESSION['p_error']; unset($_SESSION['p_error']); ?></p>
        <?php endif; ?>

        <form action="../controllers/ProjectController.php" method="POST">
            <input type="hidden" name="action" value="save_project">
            <?php if($project_id): ?>
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <?php endif; ?>

            <label>Project Name:</label>
            <input type="text" name="name" value="<?php echo $p_name; ?>" required>

            <label>Description:</label>
            <textarea name="description"><?php echo $p_desc; ?></textarea>

            <label>Deadline:</label>
            <input type="date" name="deadline" value="<?php echo $p_deadline; ?>" required>

            <label>Color Label Preset Swatches:</label>
            <div class="selection-group">
                <label><input type="radio" name="color_label" value="#ff0000" <?php if($p_color=='#ff0000') echo 'checked'; ?>> <span style="color:#ff0000; font-weight:bold;">Red</span></label>
                <label><input type="radio" name="color_label" value="#00ff00" <?php if($p_color=='#00ff00') echo 'checked'; ?>> <span style="color:#00aa00; font-weight:bold;">Green</span></label>
                <label><input type="radio" name="color_label" value="#0000ff" <?php if($p_color=='#0000ff') echo 'checked'; ?>> <span style="color:#0000ff; font-weight:bold;">Blue</span></label>
                <label><input type="radio" name="color_label" value="#f1c40f" <?php if($p_color=='#f1c40f') echo 'checked'; ?>> <span style="color:#d4ac0d; font-weight:bold;">Yellow</span></label>
                <label><input type="radio" name="color_label" value="#9b59b6" <?php if($p_color=='#9b59b6') echo 'checked'; ?>> <span style="color:#9b59b6; font-weight:bold;">Purple</span></label>
            </div>

            <label>Assign Workspace Members (At least one):</label>
            <div class="selection-group">
                <?php foreach($workspace_members as $member): 
                    $checked = "";
                    if ($project_id) {
                        if (isProjectMember($conn, $project_id, $member['user_id'])) {
                            $checked = "checked";
                        }
                    }
                ?>
                    <label class="member-checkbox">
                        <input type="checkbox" name="members[]" value="<?php echo $member['user_id']; ?>" <?php echo $checked; ?>> 
                        <?php echo $member['name']; ?> (<small style="color: gray;"><?php echo $member['email']; ?></small>)
                    </label>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit">Save Project</button>
                <a href="dashboard.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>

</body>
</html>