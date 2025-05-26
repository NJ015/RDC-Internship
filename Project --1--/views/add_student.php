<?php

// session_start();
$errorMessages = $_SESSION['errors'] ?? [];
$message = $_SESSION['message'] ?? null;
unset($_SESSION['errors'], $_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="../css/addC.css">
</head>
<body>
    <div class="course-details">
        <a href="../controller/StudentController.php?action=index" class="back-arrow">&#8592;</a>
        <h2>Add New Student</h2>

        <?php if (!empty($errorMessages)): ?>
            <div class="error-messages">
                <?php foreach ($errorMessages as $msg): ?>
                    <p style="color:red"><?= htmlspecialchars($msg) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="success-message">
                <p style="color:green;"><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="schedule-form" action="../controller/StudentController.php?action=create">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" required>

            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone">

            <label for="major">Major</label>
            <input type="text" name="major" id="major">

            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob">

            <label for="gender">Gender</label>
            <select name="gender" id="gender">
                <option value="">-- Select --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <button type="submit">Add Student</button>
        </form>
    </div>
</body>
</html>