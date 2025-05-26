<?php

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../dbconfig.php';

/**
 * Controller for handling schedule-related actions.
 */
class ScheduleController
{
    private $courseModel;
    private $pdo;

    /**
     * ScheduleController constructor.
     */
    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->courseModel = new Course($pdo);
    }

    /**
     * Show the form to add or edit a schedule entry.
     */
    public function form()
    {
        $course_id = $_GET['course_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;
        $editing_schedule = null;

        if ($schedule_id) {
            $result = $this->courseModel->getSchedule($course_id, $schedule_id);
            $editing_schedule = isset($result['success']) ? $result['success'] : null;
            if (isset($result['error'])) {
                session_start();
                $_SESSION['errors'] = (array)$result['error'];
            }
        }

        require_once __DIR__ . '/../views/add_edit_schedule.php';
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION['errors'], $_SESSION['message']);
        }
    }

    /**
     * Handle saving (adding or updating) a schedule entry.
     */
    public function save()
    {
        session_start();
        $course_id = $_GET['course_id'] ?? $_POST['course_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $day = $_POST['day'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
            $location = $_POST['location'] ?? null;

            if (isset($_POST['update_schedule_id'])) {
                $schedule_id = $_POST['update_schedule_id'];
                $result = $this->courseModel->updateSchedule(
                    $schedule_id,
                    $day,
                    $start_time,
                    $end_time,
                    $location
                );
                if (isset($result['success'])) {
                    $_SESSION['message'] = "Schedule updated successfully!";
                } else {
                    $_SESSION['errors'] = (array)($result['error'] ?? ['Update failed.']);
                }
            } else {
                $result = $this->courseModel->addSchedule(
                    $course_id,
                    $day,
                    $start_time,
                    $end_time,
                    $location
                );
                if (isset($result['success'])) {
                    $_SESSION['message'] = "Schedule added successfully!";
                } else {
                    $_SESSION['errors'] = (array)($result['error'] ?? ['Add failed.']);
                }
            }

            header("Location: CourseController.php?action=details&id=$course_id");
            exit;
        }
    }

    /**
     * Delete a schedule entry.
     */
    public function delete()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule_id'])) {
            $scheduleId = $_POST['delete_schedule_id'];
            $course_id = $_POST['course_id'] ?? null;

            $result = $this->courseModel->deleteSchedule($scheduleId);

            if (isset($result['success'])) {
                $_SESSION['message'] = "Schedule deleted successfully";
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Delete failed.']);
            }
            header("Location: /RDC/Project --1--/controller/CourseController.php?action=details&id=$course_id");
            exit;
        }

        header("HTTP/1.0 400 Bad Request");
        echo "Invalid request";
        exit;
    }
}

// Handle the request
$action = $_GET['action'] ?? 'form';
$controller = new ScheduleController();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Page not found";
}