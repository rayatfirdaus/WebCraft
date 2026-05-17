

// assets/js/activity.js

// 1. Filter Activity Log (GET Request)
function filterActivities(projectId) {
    let userId = document.getElementById("member_filter").value;
    let listDiv = document.getElementById("activity_list");
    
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let activities = JSON.parse(this.responseText);
            listDiv.innerHTML = ""; // old list clear kora
            
            if (activities.length === 0) {
                listDiv.innerHTML = "<p>No activities found.</p>";
                return;
            }

            activities.forEach(function(act) {
                // UI render for each log
                let item = `<div style="border-bottom:1px solid #ccc; padding:10px 0;">
                    <span style="background:#3498db; color:white; padding:5px; border-radius:50%; font-size:12px;">${act.initials}</span>
                    <b>${act.user_name}</b> ${act.action_text} 
                    <span style="color:gray; font-size:12px; float:right;">${act.time_ago}</span>
                </div>`;
                listDiv.innerHTML += item;
            });
        }
    };
    xhttp.open("GET", `../controllers/ActivityController.php?action=fetch_activities&project_id=${projectId}&user_id=${userId}`, true);
    xhttp.send();
}

// 2. Post a New Comment
function postComment(taskId) {
    let bodyInput = document.getElementById("comment_body_" + taskId);
    let bodyText = bodyInput.value;
    
    if(bodyText.trim() === "") return alert("Write something!");

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            if (res.ok) {
                let comment = res.comment;
                let thread = document.getElementById("comment_thread_" + taskId);
                
                // Append without page reload
                let newCommentHTML = `
                <div id="comment_${comment.id}" style="background:#f9f9f9; padding:8px; margin-bottom:5px; border:1px solid #ddd;">
                    <b>${comment.author_name}</b> <small>${comment.created_at}</small>
                    <p style="margin:5px 0;">${comment.body}</p>
                    <a href="javascript:void(0)" onclick="deleteComment(${comment.id})" style="color:red; font-size:12px;">Delete</a>
                </div>`;
                
                thread.innerHTML += newCommentHTML;
                bodyInput.value = ""; // clear input
            } else {
                alert(res.message);
            }
        }
    };
    xhttp.open("POST", "../controllers/ActivityController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(`action=post_comment&task_id=${taskId}&body=${encodeURIComponent(bodyText)}`);
}

// 3. Delete Comment (Fades out of DOM)
function deleteComment(commentId) {
    if(!confirm("Delete this comment?")) return;

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            if (res.ok) {
                // DOM element k opacity komiye (fade out) remove kora
                let commentDiv = document.getElementById("comment_" + commentId);
                commentDiv.style.transition = "opacity 0.5s";
                commentDiv.style.opacity = "0";
                setTimeout(() => commentDiv.remove(), 500);
            } else {
                alert(res.message);
            }
        }
    };
    
    // PDF Specs required DELETE method
    xhttp.open("DELETE", `../controllers/ActivityController.php`, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(`comment_id=${commentId}`);
}