function removeWorkspaceMember(memberRowId) {
    if(confirm("Are you sure you want to remove this member?")) {
        var xhttp = new XMLHttpRequest();
        
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if(this.responseText.trim() === "success") {
                    var row = document.getElementById("member-row-" + memberRowId);
                    if(row) {
                        row.parentNode.removeChild(row);
                    }
                } else {
                    alert("Failed to remove member!");
                }
            }
        };
        
        xhttp.open("POST", "../controllers/WorkspaceController.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("action=remove_member&member_row_id=" + memberRowId);
    }
}