<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin-top: 50px;
        }
        .register-container {
            background-color: #ffffff;
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            text-align: left;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333333;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin-top: 15px;
        }
        a {
            color: #0066cc;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <div class="register-container">
        <h2>Register</h2>
        
        <?php if(isset($_SESSION['error'])): ?>
            <p style="color:red; text-align:center;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="../controllers/AuthController.php" method="POST">
            <input type="hidden" name="action" value="register">
            
            <label>Name:</label><br>
            <input type="text" name="name" required><br>
            
            <label>Email:</label><br>
            <input type="email" name="email" required><br>
            
            <label>Password:</label><br>
            <input type="password" name="password" minlength="8" required><br>
            
            <button type="submit">Register</button>
        </form>
        
        <div class="message">
            <a href="login.php">Already have an account? Login here.</a>
        </div>
    </div>

</body>
</html>