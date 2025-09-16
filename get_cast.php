<?php
header('Content-Type: application/json');

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die(json_encode([]));
}

$result = $conn->query("SELECT cast_id, cast_name FROM cast ORDER BY cast_name");
$cast = [];

while ($row = $result->fetch_assoc()) {
    $cast[] = $row;
}

echo json_encode($cast);
$conn->close();
?>