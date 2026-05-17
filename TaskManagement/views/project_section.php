<?php
include_once '../models/ProjectModel.php';

if (isset($current_workspace_id)) {
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'active';
    $is_archived_param = ($tab == 'archived') ? 1 : 0;
    $projects = getProjectsByWorkspace($conn, $current_workspace_id, $is_archived_param);
    ?>
    <hr>
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Project Boards (Managed by Student 2)</h3>
        <a href="project_form.php" style="background:green; color:white; padding:8px; text-decoration:none;">+ New Project</a>
    </div>
    
    <div>
        <a href="dashboard.php?tab=active">Active Projects</a> | 
        <a href="dashboard.php?tab=archived">Archived Projects</a>
    </div>
    <br>

    <div style="display:flex; flex-wrap:wrap; gap:15px;">
        <?php foreach($projects as $proj): 
            $total = $proj['total_tasks'];
            $done = $proj['done_tasks'];
            $progress_percent = ($total > 0) ? round(($done / $total) * 100) : 0;
        ?>
            <div style="border:1px solid #ccc; border-left:8px solid <?php echo $proj['color_label']; ?>; padding:15px; width:280px;">
                <h4><a href="project_detail.php?id=<?php echo $proj['id']; ?>"><?php echo $proj['name']; ?></a></h4>
                <div style="background:#ddd; width:100%; height:15px; border-radius:10px;">
                    <div style="background:green; width:<?php echo $progress_percent; ?>%; height:100%; text-align:center; color:white; font-size:11px;">
                        <?php echo $progress_percent; ?>%
                    </div>
                </div>
                <small><?php echo $total > 0 ? "$done/$total Tasks Done" : "No tasks yet"; ?></small>
            </div>
        <?php endforeach; ?>
    </div>
<?php } ?>