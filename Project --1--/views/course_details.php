<?php

// session_start();
$message = $_SESSION['message'] ?? null;
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['message'], $_SESSION['errors']);

if (isset($_GET['tab'])) {
    $_SESSION['active_tab'] = $_GET['tab'];
}
$activeTab = $_SESSION['active_tab'] ?? 'info';

?>

<html lang="en">

<head>
    <title><?= htmlspecialchars($course->course_name) ?> Details</title>
    <link rel="stylesheet" href="../css/mainCD.css">
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

        <div class="tabs">
            <a href="../controller/CourseController.php?action=details&id=<?= $course->id ?>&tab=info" class="tab-btn<?= $activeTab === 'info' ? ' active' : '' ?>">Course Info</a>
            <a href="../controller/CourseController.php?action=details&id=<?= $course->id ?>&tab=students" class="tab-btn<?= $activeTab === 'students' ? ' active' : '' ?>">Students</a>
            <a href="../controller/CourseController.php?action=details&id=<?= $course->id ?>&tab=schedule" class="tab-btn<?= $activeTab === 'schedule' ? ' active' : '' ?>">Schedule</a>
        </div>

        <div class="tab-content" id="info" style="<?= $activeTab === 'info' ? '' : 'display:none;' ?>">
            <h2><?= $course->course_code ?>: <?= htmlspecialchars($course->course_name) ?></h2>
            <p class="desc"><strong>Description:</strong> <?= nl2br(htmlspecialchars($course->description)) ?></p>
            <p><strong>Credits:</strong> <?= $course->credits ?></p>

            <div class="actions">
                <form method="POST" style="display:inline;" action="../controller/CourseController.php?action=editForm">
                    <input type="hidden" name="edit" value="<?= $course->id ?>">
                    <button type="submit" class="edit">Edit</button>
                </form> |
                <form method="POST" style="display:inline;" class="delete-form" action="../controller/CourseController.php?action=delete">
                    <input type="hidden" name="delete_id" value="<?= $course->id ?>">
                    <button type="submit" class="delete-btn" data-message='Are you sure you want to delete this course?'>Delete</button>
                </form>
            </div>

            <?php if (isset($_GET['edit'])): ?>
                <hr>
                <h3>Edit Course</h3>
                <form method="POST" class="schedule-form" action="../controller/CourseController.php?action=edit">
                    <input type="hidden" name="edit_id" value="<?= $course->id ?>">

                    <label>Course Code:</label>
                    <input type="text" name="course_code" value="<?= htmlspecialchars($course->course_code) ?>" required>

                    <label>Course Name:</label>
                    <input type="text" name="course_name" value="<?= htmlspecialchars($course->course_name) ?>" required>

                    <label>Credits:</label>
                    <input type="number" name="credits" min="1" max="6" value="<?= htmlspecialchars($course->credits) ?>" required>

                    <label>Description:</label>
                    <textarea name="description"><?= htmlspecialchars($course->description) ?></textarea>

                    <button type="submit" class="edit">Save Changes</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="tab-content" id="students" style="<?= $activeTab === 'students' ? '' : 'display:none;' ?>">
            <h3>Registered Students</h3>
            <a href="../controller/RegistrationController.php?action=registerTableView&course_id=<?= $course->id ?>" class="button">+ Add Student</a>
            <?php if ($students): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s->student_id) ?></td>
                                <td><?= htmlspecialchars($s->first_name . ' ' . $s->last_name) ?></td>
                                <td><?= htmlspecialchars($s->email) ?></td>
                                <td>
                                    <a href="../controller/StudentController.php?action=details&id=<?= $s->id ?>" class="edit">View</a> |

                                    <form method="POST" style="display:inline;" class="delete-form" action="../controller/RegistrationController.php?action=unregister&course_id=<?= $course->id ?>">
                                        <input type="hidden" name="student_id" value="<?= $s->id ?>">
                                        <button class="delete-btn" type="submit" data-message='Unregister this student from the course?'>Unregister</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No students registered.</p>
            <?php endif; ?>
        </div>

        <div class="tab-content" id="schedule" style="<?= $activeTab === 'schedule' ? '' : 'display:none;' ?>">
            <h3>Course Schedule</h3>
            <a href="../controller/ScheduleController.php?action=form&course_id=<?= $course->id ?>" class="button">+ Add Schedule</a>
            <?php if ($schedule): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedule as $slot): ?>
                            <tr>
                                <td><?= htmlspecialchars($slot->day_of_week) ?></td>
                                <td><?= htmlspecialchars(substr($slot->start_time, 0, 5)) ?> - <?= htmlspecialchars(substr($slot->end_time, 0, 5)) ?></td>
                                <td><?= htmlspecialchars($slot->location ?? 'N/A') ?></td>
                                <td>
                                    <a href="../controller/ScheduleController.php?action=form&course_id=<?= $course->id ?>&schedule_id=<?= $slot->id ?>" class="edit">Edit</a> |

                                    <form method="POST" style="display:inline;" class="delete-form" action="../controller/ScheduleController.php?action=delete">
                                        <input type="hidden" name="delete_schedule_id" value="<?= $slot->id ?>">
                                        <input type="hidden" name="course_id" value="<?= $course->id ?>">
                                        <button type="submit" class="delete-btn" data-message='Are you sure you want to delete this schedule?'>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No schedule assigned.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="confirm-modal" class="modal">
        <div class="modal-content">
            <p id="confirm-message">Are you sure?</p>
            <div class="modal-buttons">
                <button id="confirm-yes">Yes</button>
                <button id="confirm-no">Cancel</button>
            </div>
        </div>
    </div>

</body>

<script>
    const modal = document.getElementById("confirm-modal");
    const confirmMessage = document.getElementById("confirm-message");
    const confirmYes = document.getElementById("confirm-yes");
    const confirmNo = document.getElementById("confirm-no");
    let formToSubmit = null;

    document.querySelectorAll(".delete-btn").forEach(btn => {
        btn.addEventListener("click", (event) => {
            event.preventDefault(); // prevent immediate form submission
            formToSubmit = btn.closest("form");
            confirmMessage.textContent = btn.dataset.message || "Are you sure?";
            modal.style.display = "block";
        });
    });

    confirmYes.onclick = () => {
        modal.style.display = "none";
        if (formToSubmit) formToSubmit.submit();
    };

    confirmNo.onclick = () => {
        modal.style.display = "none";
        formToSubmit = null;
    };

    window.onclick = (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
            formToSubmit = null;
        }
    };
</script>

</html>