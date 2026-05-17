
<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    
    <?php if(isset($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form action="../controllers/AuthController.php" method="POST">
        <input type="hidden" name="action" value="register">
        
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>
        
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" minlength="8" required><br><br>
        
        <button type="submit">Register</button>
    </form>
    <br>
    <a href="login.php">Already have an account? Login here.</a>
</body>
</html>
