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
    <title><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?> - Details</title>
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
        <a href="../controller/StudentController.php?action=index" class="back-arrow">&#8592;</a>

        <div class="tabs">
            <a href="../controller/StudentController.php?action=details&id=<?= $student->id ?>&tab=info" class="tab-btn<?= $activeTab === 'info' ? ' active' : '' ?>">Student Info</a>
            <a href="../controller/StudentController.php?action=details&id=<?= $student->id ?>&tab=courses" class="tab-btn<?= $activeTab === 'courses' ? ' active' : '' ?>">Courses</a>
            <a href="../controller/StudentController.php?action=details&id=<?= $student->id ?>&tab=schedule" class="tab-btn<?= $activeTab === 'schedule' ? ' active' : '' ?>">Schedule</a>
        </div>

        <div class="tab-content" id="info" style="<?= $activeTab === 'info' ? '' : 'display:none;' ?>">
            <h2><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></h2>

            <?php if (isset($_GET['edit'])): ?>
                <hr>
                <h3>Edit Student</h3>
                <form method="POST" class="schedule-form" action="../controller/StudentController.php?action=edit">
                    <input type="hidden" name="edit_id" value="<?= $student->id ?>">

                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($student->first_name) ?>" required>

                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($student->last_name) ?>" required>

                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($student->email) ?>" required>

                    <label>Phone:</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($student->phone) ?>">

                    <label>Date of Birth:</label>
                    <input type="date" name="dob" value="<?= htmlspecialchars($student->dob) ?>">

                    <label>Gender:</label>
                    <select name="gender">
                        <option value="Male" <?= $student->gender === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $student->gender === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>

                    <label>Major:</label>
                    <input type="text" name="major" value="<?= htmlspecialchars($student->major) ?>">

                    <button type="submit" class="edit">Save Changes</button>
                </form>

            <?php else: ?>
                <hr>
                <div class="student-details">
                    <p><strong>Student ID:</strong> <?= htmlspecialchars($student->student_id) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($student->email) ?></p>
                    <p><strong>First Name:</strong> <?= htmlspecialchars($student->first_name) ?></p>
                    <p><strong>Last Name:</strong> <?= htmlspecialchars($student->last_name) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($student->phone) ?></p>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student->dob) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($student->gender) ?></p>
                    <p><strong>Major:</strong> <?= htmlspecialchars($student->major) ?></p>
                    <p><strong>Joined:</strong> <?php $date = new DateTime($student->created_at);
                                                echo $date->format('Y-m-d'); ?></p>
                </div>

                <div class="actions">
                    <form method="POST" style="display:inline;" action="../controller/StudentController.php?action=editForm">
                        <input type="hidden" name="edit" value="<?= $student->id ?>">
                        <button type="submit" class="edit">Edit</button>
                    </form> |

                    <form method="POST" class="delete-form" style="display: inline;" action="../controller/StudentController.php?action=delete">
                        <input type="hidden" name="delete_id" value="<?= $student->id ?>">
                        <button type="button" class="delete-btn" data-message='Are you sure you want to delete this student?'>Delete</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-content" id="courses" style="<?= $activeTab === 'courses' ? '' : 'display:none;' ?>">
            <h3>Registered Courses</h3>
            <a href="../controller/RegistrationController.php?action=registerTableView&student_id=<?= $student->id ?>" class="edit">Register to a New Course</a>
            <?php if ($courses): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c->course_code) ?></td>
                                <td><?= htmlspecialchars($c->course_name) ?></td>
                                <td><?= htmlspecialchars($c->credits) ?></td>
                                <td>
                                    <a href="../controller/CourseController.php?action=details&id=<?= $c->id ?>" class="edit">View</a> |
                                    <form method="POST" style="display:inline;" class="delete-form" action="../controller/RegistrationController.php?action=unregister&student_id=<?= $student->id ?>">
                                        <input type="hidden" name="course_id" value="<?= $c->id ?>">
                                        <button type="submit" class="delete-btn" data-message='Unregister from this course?'>Unregister</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No courses registered.</p>
            <?php endif; ?>
        </div>

        <div class="tab-content" id="schedule" style="<?= $activeTab === 'schedule' ? '' : 'display:none;' ?>">
            <h3>Weekly Schedule</h3>
            <?php if ($schedule): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedule as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s->course_name) ?></td>
                                <td><?= htmlspecialchars($s->day_of_week) ?></td>
                                <td><?= htmlspecialchars(substr($s->start_time, 0, 5)) ?> - <?= htmlspecialchars(substr($s->end_time, 0, 5)) ?></td>
                                <td><?= htmlspecialchars($s->location ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No schedule available.</p>
            <?php endif; ?>
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
    </div>

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

</body>

</html>