<?php

// session_start();
$errors = $_SESSION['errors'] ?? [];
$message = $_SESSION['message'] ?? null;
unset($_SESSION['errors'], $_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Students</title>
    <link rel="stylesheet" href="../css/mainS.css">
</head>

<body>
    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p style='color:red;'><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="success-message">
            <p style="color:green;"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <a href="../controller/StudentController.php?action=createForm" class="report-button">Add New Student</a>

    <div class="form-and-cards-container">
        <?php foreach ($students as $student): ?>
            <div role="button" tabindex="0" class="student-card" 
                 onclick="window.location.href='../controller/StudentController.php?action=details&id=<?= $student->id ?>'">
                <h3><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></h3>
                <p><strong>ID:</strong> <?= htmlspecialchars($student->student_id) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($student->email) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>