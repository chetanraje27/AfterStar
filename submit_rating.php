<?php
header('Content-Type: application/json');

// Get JSON input instead of $_POST
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if required fields are set
if (!isset($data['movieId']) || !isset($data['rating'])) {
    echo json_encode(['success' => false, 'message' => 'Movie selection and rating are required']);
    exit;
}

$movieId = intval($data['movieId']);
$rating = intval($data['rating']);
$review = isset($data['review']) ? trim($data['review']) : '';

// Rest of your existing validation and database code...

// Validate inputs
if ($movieId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please select a valid movie']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get movie title
$stmt = $conn->prepare("SELECT movie_title FROM movie WHERE movie_id = ?");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Movie not found']);
    $stmt->close();
    $conn->close();
    exit;
}

$movie = $result->fetch_assoc();
$movieTitle = $movie['movie_title'];
$stmt->close();

// Insert rating
$stmt = $conn->prepare("INSERT INTO ratings (movie_id, movie_title, rating, review) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isis", $movieId, $movieTitle, $rating, $review);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Rating submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error submitting rating: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>