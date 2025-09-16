<?php
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    echo json_encode([
        'loggedIn' => true,
        'username' => $_SESSION['username']
    ]);
} else {
    echo json_encode([
        'loggedIn' => false
    ]);
}
?>