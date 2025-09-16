<?php
header('Content-Type: application/json');

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die(json_encode([]));
}

$result = $conn->query("SELECT rating_id, movie_id, rating_value FROM ratings ORDER BY rating_value DESC");
$movies = [];

while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

echo json_encode($movies);
$conn->close();
?>