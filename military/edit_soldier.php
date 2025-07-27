<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM soldiers WHERE id = $id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("UPDATE soldiers SET name=?, age=?, gender=?, rank=?, unit=?, enlist_date=?, contact=? WHERE id=?");
    $stmt->bind_param("sisssssi", $_POST['name'], $_POST['age'], $_POST['gender'], $_POST['rank'], $_POST['unit'], $_POST['enlist_date'], $_POST['contact'], $id);
    $stmt->execute();
    header("Location: list_soldiers.php");
    exit();
}
?>

<link rel="stylesheet" href="css/style.css">

<div class="container">
  <h2>Edit Soldier</h2>

  <form method="post">
    <div class="form-group">
      <label>Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
    </div>

    <div class="form-group">
      <label>Age:</label>
      <input type="number" name="age" value="<?= $row['age'] ?>" required>
    </div>

    <div class="form-group">
      <label>Gender:</label>
      <select name="gender">
        <option value="Male" <?= $row['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $row['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
      </select>
    </div>

    <div class="form-group">
      <label>Rank:</label>
      <input type="text" name="rank" value="<?= htmlspecialchars($row['rank']) ?>">
    </div>

    <div class="form-group">
      <label>Unit:</label>
      <input type="text" name="unit" value="<?= htmlspecialchars($row['unit']) ?>">
    </div>

    <div class="form-group">
      <label>Enlist Date:</label>
      <input type="date" name="enlist_date" value="<?= $row['enlist_date'] ?>">
    </div>

    <div class="form-group">
      <label>Contact:</label>
      <input type="text" name="contact" value="<?= htmlspecialchars($row['contact']) ?>">
    </div>

    <div class="buttons">
      <button type="submit">Update Soldier</button>
      <button type="button" onclick="window.location.href='list_soldiers.php';">Cancel</button>
    </div>
  </form>
</div>
