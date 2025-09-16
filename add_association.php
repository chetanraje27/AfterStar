<?php
header('Content-Type: application/json');

// Simple admin check
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'admin_panel.php') === false) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate data
if (empty($data['movieId']) || empty($data['castId'])) {
    echo json_encode(['success' => false, 'message' => 'Both movie and cast selection are required']);
    exit;
}

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Check if association already exists
$checkStmt = $conn->prepare("SELECT * FROM movie_cast WHERE movie_id = ? AND cast_id = ?");
$checkStmt->bind_param("ii", $data['movieId'], $data['castId']);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This association already exists']);
    exit;
}

// Insert association
$stmt = $conn->prepare("INSERT INTO movie_cast (movie_id, cast_id) VALUES (?, ?)");
$stmt->bind_param("ii", $data['movieId'], $data['castId']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Movie-cast association added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating association: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>