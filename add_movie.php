<?php
header('Content-Type: application/json');

// Simple admin check
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'admin_panel.php') === false) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate data
if (empty($data['title']) || empty($data['castId']) || empty($data['duration'])) {
    echo json_encode(['success' => false, 'message' => 'Movie title, cast selection and duration are required']);
    exit;
}

if (!is_numeric($data['duration']) || $data['duration'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Duration must be a positive number']);
    exit;
}

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Insert movie with cast_id and duration
$stmt = $conn->prepare("INSERT INTO movie (movie_title, posters, release_date, genera, cast_id, duration) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssii", $data['title'], $data['poster'], $data['releaseDate'], $data['genre'], $data['castId'], $data['duration']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Movie added with cast association and duration']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding movie: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>