<?php

// session_start();
$errors = $_SESSION['errors'] ?? [];
$message = $_SESSION['message'] ?? null;
unset($_SESSION['errors'], $_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Course Registration Report</title>
    <link rel="stylesheet" href="../css/report.css">
</head>

<body>
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

    <div class="course-details">
        <a href="../controller/CourseController.php?action=index" class="back-arrow">&#8592;</a>
        <h2>Course Registration Report</h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Number of Students</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['course_code']) ?></td>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                        <td><?= htmlspecialchars($row['student_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

<script>
    document.querySelectorAll('.report-table tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', () => {
            const id = row.querySelector('td:first-child').textContent.trim();
            window.location.href = `../controller/CourseController.php?action=details&id=${id}`;
        });
    });
</script>

</html>