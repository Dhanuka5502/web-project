<?php
session_start();
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: manage_users.php");
    exit;
}

require_once '../backend/db.php';
$id = intval($_GET['id']);

// Prevent admin from deleting themselves
if ($_SESSION['id'] == $id) {
    header("location: manage_users.php?error=cannot_delete_self");
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

header("location: manage_users.php");
exit; 