<?php
header('Content-Type: application/json');

// Simple admin check
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'admin_panel.php') === false) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate data
if (empty($data['name']) || empty($data['role'])) {
    echo json_encode(['success' => false, 'message' => 'Name and role are required']);
    exit;
}

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Insert cast
$stmt = $conn->prepare("INSERT INTO cast (cast_name, role, dob) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $data['name'], $data['role'], $data['dob']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cast member added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding cast member: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>