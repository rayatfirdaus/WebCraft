<?php
session_start();
include '../config/db.php';
include '../models/WorkspaceModel.php';
include '../models/ProjectModel.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$my_workspaces = getUserWorkspaces($conn, $user_id);

if ((!isset($_SESSION['workspace_id']) || $_SESSION['workspace_id'] === null) && !empty($my_workspaces)) {
    $_SESSION['workspace_id'] = $my_workspaces[0]['id'];
}

$current_workspace_id = isset($_SESSION['workspace_id']) ? $_SESSION['workspace_id'] : null;

if ($_SESSION['workspace_id'] === null && !empty($my_workspaces)) {
    $_SESSION['workspace_id'] = $my_workspaces[0]['id'];
}

$current_workspace_id = $_SESSION['workspace_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="../assets/js/workspace.js"></script> </head>
<body>
    <div style="background:#f4f4f4; padding:10px; display:flex; justify-content:space-between;">
        <span>Welcome, <b><?php echo $_SESSION['name']; ?></b></span>
        
        <form action="../controllers/WorkspaceController.php" method="GET" style="display:inline;">
            <label>Switch Workspace:</label>
            <select name="switch_id" onchange="this.form.submit()">
                <?php foreach($my_workspaces as $ws): ?>
                    <option value="<?php echo $ws['id']; ?>" <?php if($ws['id'] == $current_workspace_id) echo 'selected'; ?>>
                        <?php echo $ws['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <a href="../controllers/AuthController.php?action=logout">Logout</a>
    </div>

    <?php if(isset($_SESSION['w_success'])): ?>
        <p style="color:green;"><?php echo $_SESSION['w_success']; unset($_SESSION['w_success']); ?></p>
    <?php endif; ?>
    <?php if(isset($_SESSION['w_error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['w_error']; unset($_SESSION['w_error']); ?></p>
    <?php endif; ?>

    <?php if($current_workspace_id === null): ?>
        <h3>You are not part of any workspace. Create or Join one!</h3>
        <table border="1" cellpadding="10">
            <tr>
                <td>
                    <h4>Create Workspace</h4>
                    <form action="../controllers/WorkspaceController.php" method="POST">
                        <input type="hidden" name="action" value="create">
                        <input type="text" name="name" placeholder="Workspace Name" required><br><br>
                        <textarea name="description" placeholder="Description"></textarea><br><br>
                        <button type="submit">Create</button>
                    </form>
                </td>
                <td>
                    <h4>Join Workspace</h4>
                    <form action="../controllers/WorkspaceController.php" method="POST">
                        <input type="hidden" name="action" value="join">
                        <input type="text" name="invite_code" placeholder="6-Char Code" required><br><br>
                        <button type="submit">Join</button>
                    </form>
                </td>
            </tr>
        </table>
    <?php else: ?>
        
        <?php
        $ws_query = mysqli_query($conn, "SELECT * FROM workspaces WHERE id = '$current_workspace_id'");
        $current_ws = mysqli_fetch_assoc($ws_query);
        ?>
        <h2>Workspace: <?php echo $current_ws['name']; ?></h2>
        <p>Invite Code: <b><?php echo $current_ws['invite_code']; ?></b></p>

        <hr>
        <h3>Workspace Members</h3>
        <table border="1" cellpadding="5">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Join Date</th>
                <th>Action</th>
            </tr>
            <?php 
            $members = getWorkspaceMembers($conn, $current_workspace_id);
            foreach($members as $member): 
            ?>
                <tr id="member-row-<?php echo $member['member_row_id']; ?>">
                    <td><?php echo $member['name']; ?></td>
                    <td><?php echo $member['email']; ?></td>
                    <td><?php echo $member['joined_at']; ?></td>
                    <td>
                        <?php if($current_ws['owner_id'] == $user_id && $member['user_id'] != $user_id): ?>
                            <button onclick="removeWorkspaceMember(<?php echo $member['member_row_id']; ?>)">Remove</button>
                        <?php else: ?>
                            <span>No Action</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <hr>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Project Boards</h3>
            <a href="project_form.php" style="background:green; color:white; padding:8px; text-decoration:none; border-radius:4px;">+ New Project</a>
        </div>

        <?php
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'active';
        $is_archived_param = ($tab == 'archived') ? 1 : 0;
        $projects = getProjectsByWorkspace($conn, $current_workspace_id, $is_archived_param);
        ?>

        <div>
            <a href="dashboard.php?tab=active" style="<?php if($tab=='active') echo 'font-weight:bold;'; ?>">Active Projects</a> | 
            <a href="dashboard.php?tab=archived" style="<?php if($tab=='archived') echo 'font-weight:bold;'; ?>">Archived Projects</a>
        </div>
        <br>

        <?php if(isset($_SESSION['p_success'])): ?>
            <p style="color:green;"><?php echo $_SESSION['p_success']; unset($_SESSION['p_success']); ?></p>
        <?php endif; ?>

        <?php if(empty($projects)): ?>
            <p>No projects found in this section.</p>
        <?php else: ?>
            <div style="display:flex; flex-wrap:wrap; gap:15px;">
                <?php foreach($projects as $proj): 
                    $total = $proj['total_tasks'];
                    $done = $proj['done_tasks'];
                    $progress_percent = ($total > 0) ? round(($done / $total) * 100) : 0;
                    
                    $is_overdue = (strtotime($proj['deadline']) < strtotime(date('Y-m-d'))) && ($progress_percent < 100);
                ?>
                    <div style="border: 1px solid #ccc; border-left: 8px solid <?php echo $proj['color_label']; ?>; padding:15px; width:280px; border-radius:4px; background:#fff;">
                        <h4><a href="project_detail.php?id=<?php echo $proj['id']; ?>"><?php echo $proj['name']; ?></a></h4>
                        <p><?php echo substr($proj['description'], 0, 60) . '...'; ?></p>
                        
                        <p style="<?php if($is_overdue) echo 'color:red; font-weight:bold;'; ?>">
                            Deadline: <?php echo $proj['deadline']; ?> <?php if($is_overdue) echo '(OVERDUE)'; ?>
                        </p>

                        <div style="background:#ddd; width:100%; height:15px; border-radius:10px; margin-bottom:10px;">
                            <div style="background:green; width:<?php echo $progress_percent; ?>%; height:100%; border-radius:10px; text-align:center; color:white; font-size:11px; line-height:15px;">
                                <?php echo $progress_percent; ?>%
                            </div>
                        </div>
                        <small><?php echo $total > 0 ? "$done/$total Tasks Completed" : "No tasks yet"; ?></small>
                        <br><br>

                        <a href="project_form.php?id=<?php echo $proj['id']; ?>">Edit Settings</a>
                        <?php if($proj['is_archived'] == 0): ?>
                            <form action="../controllers/ProjectController.php" method="POST" style="display:inline; margin-left:10px;">
                                <input type="hidden" name="action" value="archive">
                                <input type="hidden" name="project_id" value="<?php echo $proj['id']; ?>">
                                <button type="submit" onclick="return confirm('Archive this project?')">Archive</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</body>
</html>