<?php
require 'db.php';
session_start();

// Function to generate unique soldier code
function generateSoldierCode($conn) {
    $latest = $conn->query("SELECT soldier_code FROM soldiers ORDER BY id DESC LIMIT 1");
    if ($latest && $latest->num_rows > 0) {
        $row = $latest->fetch_assoc();
        $lastCode = intval(str_replace('SLD-', '', $row['soldier_code']));
        $newCode = 'SLD-' . str_pad($lastCode + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newCode = 'SLD-0001';
    }
    return $newCode;
}

$soldierCode = generateSoldierCode($conn);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $rank = trim($_POST['rank']);
    $unit = trim($_POST['unit']);
    $enlist_date = trim($_POST['enlist_date']);
    $contact = trim($_POST['contact']);
    $photoPath = null;

    // Check required fields
    if (empty($name) || empty($age) || empty($gender) || empty($rank) || empty($unit) || empty($enlist_date) || empty($contact)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = './uploads/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $photoPath = $dest_path;
                } else {
                    $error = 'Error uploading the photo.';
                }
            } else {
                $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif.';
            }
        }

        // Insert to DB if no errors
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO soldiers (soldier_code, name, age, gender, rank, unit, enlist_date, contact, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssissssss", $soldierCode, $name, $age, $gender, $rank, $unit, $enlist_date, $contact, $photoPath);
            if ($stmt->execute()) {
                $success = "Soldier added successfully! Soldier Code: <strong>$soldierCode</strong>";
                $soldierCode = generateSoldierCode($conn);
            } else {
                $error = 'Failed to save soldier. Please try again.';
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add New Soldier</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="container">
    <h2>Add New Soldier</h2>

    <?php if ($error): ?>
        <div class="error" style="color:red;"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="success" style="color:green;"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" action="">
        <div class="form-group">
            <label for="soldier_code">Soldier Code:</label>
            <input type="text" id="soldier_code" name="soldier_code" value="<?= $soldierCode ?>" readonly>
        </div>

        <div class="form-group">
            <label for="name">Name: *</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="age">Age: *</label>
            <input type="number" id="age" name="age" required>
        </div>

        <div class="form-group">
            <label for="gender">Gender: *</label>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>Select gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="rank">Rank: *</label>
            <input type="text" id="rank" name="rank" required>
        </div>

        <div class="form-group">
            <label for="unit">Unit: *</label>
            <input type="text" id="unit" name="unit" required>
        </div>

        <div class="form-group">
            <label for="enlist_date">Enlist Date: *</label>
            <input type="date" id="enlist_date" name="enlist_date" required>
        </div>

        <div class="form-group">
            <label for="contact">Contact: *</label>
            <input type="text" id="contact" name="contact" required>
        </div>

        <div class="form-group">
            <label for="photo">Photo (optional):</label>
            <input type="file" id="photo" name="photo" accept="image/*">
        </div>

        <div class="buttons">
            <button type="submit">➕ Add Soldier</button>
            <button type="button" onclick="window.location.href='dashboard.php';">← Back</button>
        </div>
    </form>
</div>
</body>
</html>
