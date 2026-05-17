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
</head>
<body>
    <h2><?php echo $project_id ? "Edit Project" : "Create New Project"; ?></h2>
    
    <?php if(isset($_SESSION['p_error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['p_error']; unset($_SESSION['p_error']); ?></p>
    <?php endif; ?>

    <form action="../controllers/ProjectController.php" method="POST">
        <input type="hidden" name="action" value="save_project">
        <?php if($project_id): ?>
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        <?php endif; ?>

        <label>Project Name:</label><br>
        <input type="text" name="name" value="<?php echo $p_name; ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description"><?php echo $p_desc; ?></textarea><br><br>

        <label>Deadline:</label><br>
        <input type="date" name="deadline" value="<?php echo $p_deadline; ?>" required><br><br>

        <label>Color Label Preset Swatches:</label><br>
        <input type="radio" name="color_label" value="#ff0000" <?php if($p_color=='#ff0000') echo 'checked'; ?>> Red
        <input type="radio" name="color_label" value="#00ff00" <?php if($p_color=='#00ff00') echo 'checked'; ?>> Green
        <input type="radio" name="color_label" value="#0000ff" <?php if($p_color=='#0000ff') echo 'checked'; ?>> Blue
        <input type="radio" name="color_label" value="#f1c40f" <?php if($p_color=='#f1c40f') echo 'checked'; ?>> Yellow
        <input type="radio" name="color_label" value="#9b59b6" <?php if($p_color=='#9b59b6') echo 'checked'; ?>> Purple
        <br><br>

        <label>Assign Workspace Members (At least one):</label><br>
        <?php foreach($workspace_members as $member): 
            $checked = "";
            if ($project_id) {
                if (isProjectMember($conn, $project_id, $member['user_id'])) {
                    $checked = "checked";
                }
            }
        ?>
            <input type="checkbox" name="members[]" value="<?php echo $member['user_id']; ?>" <?php echo $checked; ?>> 
            <?php echo $member['name']; ?> (<small><?php echo $member['email']; ?></small>)<br>
        <?php endforeach; ?><br>

        <button type="submit">Save Project</button>
        <a href="dashboard.php">Cancel</a>
    </form>
</body>
</html>