<?php
header('Content-Type: application/json');
session_start();

// Store logout message in session before destroying it
$_SESSION['logout_message'] = 'Logged out successfully';
session_destroy();

// Return JSON response with redirect information
echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully',
    'redirect' => 'index.html',
    'delay' => 2000 // 2 seconds delay before redirect
]);
?>