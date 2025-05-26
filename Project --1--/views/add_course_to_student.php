<?php

// session_start();
$errors = $_SESSION['errors'] ?? [];
$message = $_SESSION['message'] ?? null;
unset($_SESSION['errors'], $_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course to Student</title>
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
        <a href='../controller/StudentController.php?action=details&id=<?= $student_id ?>' class="back-arrow" aria-label="Go back to student details">&#8592;</a>
        <h2>Add Course</h2>

        <?php if (empty($unregisteredCourses)): ?>
            <p>All courses are already registered for this student.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Register</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unregisteredCourses as $course): ?>
                        <tr>
                            <td><?= htmlspecialchars($course['course_code']) ?></td>
                            <td><?= htmlspecialchars($course['course_name']) ?></td>
                            <td><?= htmlspecialchars($course['credits']) ?></td>
                            <td>
                                <a href='../controller/CourseController.php?action=details&id=<?= $course['id'] ?>' class="edit">View</a> |
                                <form method="POST" style="display:inline;" action="../controller/RegistrationController.php?action=register&student_id=<?= $student_id ?>">
                                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
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