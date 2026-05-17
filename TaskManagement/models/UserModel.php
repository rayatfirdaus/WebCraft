<?php
function registerUser($conn, $name, $email, $password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (name, email, password_hash) VALUES ('$name', '$email', '$hashed_password')";
    return mysqli_query($conn, $sql);
}

function loginUser($conn, $email, $password) {
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password_hash'])) {
            return $user;
        }
    }
    return false;
}

function checkEmailExists($conn, $email) {
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}
?>
