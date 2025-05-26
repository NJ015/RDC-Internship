<?php

// session_start();
$errors = $_SESSION['errors'] ?? [];
$message = $_SESSION['message'] ?? null;
unset($_SESSION['errors'], $_SESSION['message']);
?>

<html lang="en">
<head>
    <title>Courses</title>
    <link rel="stylesheet" href="../css/mainC.css">
</head>
<body>
    <a href="../controller/CourseController.php?action=report" class="report-button">Courses Report</a>
    <a href="../controller/CourseController.php?action=createForm" class="report-button">Add Courses</a>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p style="color:red;"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="success-message">
            <p style="color:green;"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <div class="form-and-cards-container">
        <?php foreach ($courses as $course): ?>
            <div role="button" tabindex="0" class="course-card"
                 onclick="window.location.href='../controller/CourseController.php?action=details&id=<?= $course->id ?>'">
                <h3 class="title"><?= htmlspecialchars($course->course_code) ?> : <?= htmlspecialchars($course->course_name) ?></h3>
                <p><strong>Credits:</strong> <?= htmlspecialchars($course->credits) ?></p>
                <p class="description"><?= nl2br(htmlspecialchars($course->description)) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>