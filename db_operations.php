<?php
// Start session at the very beginning
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "after_stars_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

function registerUser($username, $password, $gender) {
    global $conn;
    
    // Check if username exists
    $check = $conn->prepare("SELECT user_id FROM user WHERE user_name = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    $check->close();
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO user (user_name, password, gender) VALUES (?, ?, ?)");
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $username, $hashedPassword, $gender);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Registration successful! Please login.', 'redirect' => 'login.html'];
    } else {
        return ['success' => false, 'message' => 'Registration failed: '];
    }
}

function loginUser($username, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT user_id, user_name, password FROM user WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['user_name'];
        return ['success' => true, 'message' => 'Login successful', 'redirect' => 'afterstar-website.html'];
    } else {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    
    if ($action === 'register') {
        $response = registerUser(
            $_POST['username'],
            $_POST['password'],
            $_POST['gender']
        );
        echo json_encode($response);
    } 
    elseif ($action === 'login') {
        $response = loginUser(
            $_POST['username'],
            $_POST['password']
        );
        echo json_encode($response);
    }
    exit(); // Important to prevent further execution
}

// Close the connection at the end of the script
$conn->close();
?>
