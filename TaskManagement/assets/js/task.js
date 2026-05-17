
function submitTaskForm() {
    let project_id = document.getElementById("task_project_id").value;
    let title = document.getElementById("task_title").value;
    let desc = document.getElementById("task_desc").value;
    let assignee = document.getElementById("task_assignee").value;
    let due_date = document.getElementById("task_due_date").value;
    
    let priorityRadios = document.getElementsByName("priority");
    let priority = "low";
    for (let i = 0; i < priorityRadios.length; i++) {
        if (priorityRadios[i].checked) { priority = priorityRadios[i].value; break; }
    }

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let response = JSON.parse(this.responseText);
            if (response.ok) {
                document.getElementById('taskModal').style.display = 'none';
                location.reload(); 
            } else {
                alert("Error: " + response.message);
            }
        }
    };
    xhttp.open("POST", "../controllers/TaskController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("action=create_task&project_id="+project_id+"&title="+title+"&description="+desc+"&assigned_to="+assignee+"&priority="+priority+"&due_date="+due_date);
}


function moveTask(taskId, currentStatus, direction) {
    let nextStatus = "";
    if (currentStatus === 'todo' && direction === 'right') nextStatus = 'in-progress';
    else if (currentStatus === 'in-progress' && direction === 'right') nextStatus = 'done';
    else if (currentStatus === 'done' && direction === 'left') nextStatus = 'in-progress';
    else if (currentStatus === 'in-progress' && direction === 'left') nextStatus = 'todo';
    
    if (nextStatus === "") return; 

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let response = JSON.parse(this.responseText);
            if (response.ok) {
                
                let card = document.getElementById('task-' + taskId);
                let targetCol = document.getElementById('col-' + response.new_status);
                
                card.setAttribute('data-status', response.new_status);
                targetCol.appendChild(card); 
                
            
            }
        }
    };
    xhttp.open("POST", "../controllers/TaskController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("action=move_task&task_id="+taskId+"&new_status="+nextStatus);
}

window.onload = function() {
    let today = new Date();
    today.setHours(0,0,0,0); 

    let taskCards = document.getElementsByClassName("task-card");
    for (let i = 0; i < taskCards.length; i++) {
        let card = taskCards[i];
        let status = card.getAttribute("data-status");
        let dueDateStr = card.getAttribute("data-due-date");
        
        if (status !== 'done' && dueDateStr) {
            let dueDate = new Date(dueDateStr);
            if (dueDate < today) {
                card.style.border = "2px solid red";
                card.style.backgroundColor = "#ffe6e6"; 
            }
        }
    }
};
