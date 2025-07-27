<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

if ($conn->query("DELETE FROM soldiers WHERE id = $id")) {
    header("Location: list_soldiers.php");
    exit();
} else {
    echo "Failed to delete soldier.";
}
?>
<link rel="stylesheet" href="css/style.css">