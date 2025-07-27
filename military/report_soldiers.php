<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Haddii la raadiyo
$where = "";
$search = $_GET['filter'] ?? '';

if ($search) {
    $safe = $conn->real_escape_string($search);
    $where = "WHERE unit LIKE '%$safe%' OR rank LIKE '%$safe%' OR soldier_code LIKE '%$safe%'";
}

$soldiers = $conn->query("SELECT * FROM soldiers $where ORDER BY enlist_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Soldiers Report</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #2f3d2f;
            color: #fff;
        }
        h2 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📑 Soldier Report</h2>

        <form method="get">
            <input type="text" name="filter" placeholder="Search by unit, rank or code" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Filter</button>
            <button onclick="window.print()">🖨️ Print</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Rank</th>
                    <th>Unit</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Enlist Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $soldiers->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['soldier_code'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['rank'] ?></td>
                        <td><?= $row['unit'] ?></td>
                        <td><?= $row['gender'] ?></td>
                        <td><?= $row['age'] ?></td>
                        <td><?= $row['enlist_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
                <button type="button" onclick="window.location.href='dashboard.php';">← Back</button>
            </tbody>
        </table>
    </div>
</body>
</html>
