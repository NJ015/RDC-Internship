<?php
// session_start();
$errors = $_SESSION['errors'] ?? [];
$message = $_SESSION['message'] ?? ($_GET['message'] ?? null);
unset($_SESSION['errors'], $_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $editing_schedule ? "Edit Schedule" : "Add Schedule" ?></title>
    <!-- <link rel="stylesheet" href="../css/main.css"> -->
    <link rel="stylesheet" href="../css/add_edit_schedule.css">
</head>

<body>
    <div class="course-details">
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p style="color:red"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="success-message">
                <p style="color:green;"><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>

        <a href="../controller/CourseController.php?action=details&id=<?= $course_id ?>" 
           class="back-arrow" 
           aria-label="Go back">&#8592;</a>

        <h4><?= $editing_schedule ? "Edit Schedule" : "Add Schedule" ?></h4>
        
        <form method="POST" 
              action="../controller/ScheduleController.php?action=save&course_id=<?= $course_id ?>" 
              class="schedule-form">
              
            <?php if ($editing_schedule): ?>
                <input type="hidden" name="update_schedule_id" value="<?= $editing_schedule->id ?>">
            <?php endif; ?>

            <label for="day">Day</label>
            <select name="day" id="day" required>
                <?php
                $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                foreach ($days as $d) {
                    $selected = ($editing_schedule && $editing_schedule->day_of_week === $d) ? 'selected' : '';
                    echo "<option value=\"$d\" $selected>$d</option>";
                }
                ?>
            </select>

            <label for="start_time">Start Time</label>
            <input type="time" name="start_time" id="start_time" required 
                   value="<?= $editing_schedule ? substr($editing_schedule->start_time, 0, 5) : '' ?>">

            <label for="end_time">End Time</label>
            <input type="time" name="end_time" id="end_time" required 
                   value="<?= $editing_schedule ? substr($editing_schedule->end_time, 0, 5) : '' ?>">

            <label for="location">Location</label>
            <input type="text" name="location" id="location" 
                   value="<?= htmlspecialchars($editing_schedule->location ?? '') ?>">

            <button type="submit"><?= $editing_schedule ? "Update" : "Add Schedule" ?></button>
        </form>
    </div>
</body>
</html>