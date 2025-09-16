<?php
header('Content-Type: application/json');

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die(json_encode([]));
}

$result = $conn->query("SELECT movie_id, movie_title FROM movie ORDER BY movie_title");
$movies = [];

while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

echo json_encode($movies);
$conn->close();
?>