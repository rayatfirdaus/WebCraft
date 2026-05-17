<?php
session_start();
include '../config/db.php';
include '../models/UserModel.php';

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'register') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = "All fields are required!";
            header("Location: ../views/register.php");
            exit();
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid Email Format!";
            header("Location: ../views/register.php");
            exit();
        } elseif (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters long!";
            header("Location: ../views/register.php");
            exit();
        } elseif (checkEmailExists($conn, $email)) {
            $_SESSION['error'] = "Email already exists!";
            header("Location: ../views/register.php");
            exit();
        }

        if (registerUser($conn, $name, $email, $password)) {
            $_SESSION['success'] = "Registration Successful! Please Log in.";
            header("Location: ../views/login.php");
        } else {
            $_SESSION['error'] = "Registration failed!";
            header("Location: ../views/register.php");
        }
    }

    if ($action == 'login') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Email and Password required!";
            header("Location: ../views/login.php");
            exit();
        }

        $user = loginUser($conn, $email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['workspace_id'] = null; 
            
            header("Location: ../views/dashboard.php");
        } else {
            $_SESSION['error'] = "Invalid Email or Password!";
            header("Location: ../views/login.php");
        }
    }
}
?>
