<?php
session_start();

// Default admin credentials
$DEFAULT_ADMIN_USERNAME = "admin";
$DEFAULT_ADMIN_PASSWORD = "123456";

// Check if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_panel.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $DEFAULT_ADMIN_USERNAME && $password === $DEFAULT_ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_panel.php");
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AfterStar</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #e50914;
            --dark: #141414;
            --light: #f5f5f5;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background-color: #1a1a1a;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }
        
        .logo span {
            color: var(--primary);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #222;
            color: white;
            font-family: inherit;
        }
        
        .form-button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        
        .form-button:hover {
            background-color: #b2070f;
        }
        
        .error-message {
            color: var(--primary);
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        
        .default-creds {
            margin-top: 20px;
            padding: 15px;
            background-color: #222;
            border-radius: 4px;
            font-size: 13px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">After<span>Star</span> Admin</div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="form-button">Login</button>
          
            
        </form>
        
        
    </div>
</body>
</html>