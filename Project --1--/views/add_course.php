<?php

// session_start();
$errorMessages = $_SESSION['errors'] ?? [];
$message = $_SESSION['message'] ?? null;
unset($_SESSION['errors'], $_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Course</title>
    <link rel="stylesheet" href="../css/addC.css">
</head>
<body>
    <div class="course-details">
        <a href="../controller/CourseController.php?action=index" class="back-arrow">&#8592;</a>
        <h2>Add New Course</h2>

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

        <form method="POST" class="schedule-form" action="../controller/CourseController.php?action=create">
            <label for="course_code">Course Code</label>
            <input type="text" name="course_code" id="course_code"><br>

            <label for="course_name">Course Name</label>
            <input type="text" name="course_name" id="course_name"><br>

            <label for="credits">Credits</label>
            <input type="number" name="credits" id="credits"><br>

            <label for="description">Description</label>
            <textarea name="description" id="description"></textarea><br>

            <button type="submit">Add Course</button>
        </form>
    </div>
</body>
</html>