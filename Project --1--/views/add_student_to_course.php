<?php

// session_start();
$errors = $_SESSION['errors'] ?? [];
$message = $_SESSION['message'] ?? null;
unset($_SESSION['errors'], $_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student to Course</title>
    <link rel="stylesheet" href="../css/addstoc.css">
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
        <a href='../controller/CourseController.php?action=details&id=<?= $course_id ?>' class="back-arrow" aria-label="Go back to courses details">&#8592;</a>
        <h2>Add Student</h2>

        <?php if (empty($unregisteredStudents)): ?>
            <p>All students are already registered in this course.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Register</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unregisteredStudents as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['student_id']) ?></td>
                            <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td>
                                <a href='../controller/StudentController.php?action=details&id=<?= $s['id'] ?>' class="edit">View</a> |
                                <form method="POST" style="display:inline;" action="../controller/RegistrationController.php?action=register&course_id=<?= $course_id ?>">
                                    <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="edit">Register</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>