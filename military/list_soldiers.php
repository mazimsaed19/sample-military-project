<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Search logic
$search = "";
$where = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where = "WHERE name LIKE '%$search%' OR unit LIKE '%$search%' OR rank LIKE '%$search%' OR soldier_code LIKE '%$search%'";
}

$result = $conn->query("SELECT * FROM soldiers $where ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Soldiers List</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .photo-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🪖 Soldiers List</h2>

    <form method="get" action="">
        <input type="text" name="search" placeholder="Search by name, unit, rank or code" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <button type="button" onclick="window.location.href='list_soldiers.php';">Reset</button>
    </form>

    <table>
        <tr>
            <th>Code</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Rank</th>
            <th>Unit</th>
            <th>Enlist Date</th>
            <th>Contact</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['soldier_code']) ?></td>
                <td>
                    <?php if (!empty($row['photo'])): ?>
                        <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Photo" class="photo-img">
                    <?php else: ?>
                        <span style="color:#ccc;">No Photo</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['age'] ?></td>
                <td><?= $row['gender'] ?></td>
                <td><?= $row['rank'] ?></td>
                <td><?= $row['unit'] ?></td>
                <td><?= $row['enlist_date'] ?></td>
                <td><?= $row['contact'] ?></td>
                <td>
                    <a href="edit_soldier.php?id=<?= $row['id'] ?>">Edit</a> |
                    <a href="delete_soldier.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this soldier?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <button type="button" onclick="window.location.href='dashboard.php';">← Back</button>
</div>

</body>
</html>
