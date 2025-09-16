<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}
die(json_encode(['status' => 'success']));
?>