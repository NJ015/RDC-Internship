<?php

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../dbconfig.php';

/**
 * Controller for handling course-related actions.
 */
class CourseController
{
    private $courseModel;
    private $pdo;

    /**
     * CourseController constructor.
     */
    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->courseModel = new Course($pdo);
    }

    /**
     * Display all courses.
     */
    public function index()
    {
        session_start();
        $result = $this->courseModel->all();
        $courses = [];
        if (isset($result['success'])) {
            $courses = $result['success'];
        } elseif (isset($result['error'])) {
            $_SESSION['errors'] = (array)$result['error'];
        }
        require_once __DIR__ . '/../views/courses.php';
        unset($_SESSION['errors'], $_SESSION['message']);
    }

    /**
     * Show the form to create a new course.
     */
    public function createForm()
    {
        session_start();
        require_once '../views/add_course.php';
        unset($_SESSION['errors'], $_SESSION['message']);
    }

    /**
     * Handle creation of a new course.
     */
    public function create()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->courseModel->create(
                $_POST['course_code'],
                $_POST['course_name'],
                $_POST['description'],
                $_POST['credits']
            );

            if (isset($result['error'])) {
                $_SESSION['errors'] = (array)$result['error'];
                header("Location: CourseController.php?action=createForm");
                exit;
            }

            $_SESSION['message'] = "Course added successfully!";
            header("Location: CourseController.php?action=index");
            exit;
        }
    }

    /**
     * Show the form to edit a course.
     */
    public function editForm()
    {
        session_start();

        $course_id = $_POST['edit'] ?? null;
        $courseResult = $this->courseModel->find($course_id);

        if (!$course_id || !isset($courseResult['success'])) {
            $_SESSION['errors'] = ['Course not found'];
            header("Location: CourseController.php?action=index");
            exit;
        }

        $course = $courseResult['success'];
        $_GET['edit'] = true;

        $studentsResult = $course->getStudents();
        $students = isset($studentsResult['success']) ? $studentsResult['success'] : [];
        $scheduleResult = $this->courseModel->getSchedule($course_id);
        $schedule = isset($scheduleResult['success']) ? $scheduleResult['success'] : [];

        require_once '../views/course_details.php';
        unset($_SESSION['errors'], $_SESSION['message']);
    }

    /**
     * Handle editing a course.
     */
    public function edit()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
            $course_id = $_POST['edit_id'];

            $result = $this->courseModel->update(
                $course_id,
                $_POST['course_code'],
                $_POST['course_name'],
                $_POST['description'],
                $_POST['credits']
            );

            $tab = $_SESSION['active_tab'] ?? 'info';

            if (isset($result['success'])) {
                $_SESSION['message'] = "Course updated successfully!";
                header("Location: CourseController.php?action=details&id=$course_id&tab=$tab");
                exit;
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Unknown error']);
                header("Location: CourseController.php?action=editForm");
                exit;
            }
        }
    }

    /**
     * Delete a course.
     */
    public function delete()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $course_id = $_POST['delete_id'];
            $result = $this->courseModel->delete($course_id);

            if (isset($result['success'])) {
                $_SESSION['message'] = "Course deleted successfully!";
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Delete failed.']);
            }
            header("Location: CourseController.php?action=index");
            exit;
        }
    }

    /**
     * Show details for a course.
     */
    public function details()
    {
        session_start();
        $course_id = $_GET['id'] ?? null;
        $courseResult = $this->courseModel->find($course_id);

        if (!$course_id || !isset($courseResult['success'])) {
            $_SESSION['errors'] = ['Course not found'];
            header("Location: CourseController.php?action=index");
            exit;
        }

        $course = $courseResult['success'];
        $studentsResult = $course->getStudents();
        $students = isset($studentsResult['success']) ? $studentsResult['success'] : [];
        $scheduleResult = $this->courseModel->getSchedule($course_id);
        $schedule = isset($scheduleResult['success']) ? $scheduleResult['success'] : [];

        require_once __DIR__ . '/../views/course_details.php';
        unset($_SESSION['errors'], $_SESSION['message']);
    }

    /**
     * Show a report of courses and student counts.
     */
    public function report()
    {
        session_start();
        $result = $this->courseModel->getCourseStudentCounts();
        $reportData = isset($result['success']) ? $result['success'] : [];
        if (isset($result['error'])) {
            $_SESSION['errors'] = (array)$result['error'];
        }
        require_once __DIR__ . '/../views/course_report.php';
        unset($_SESSION['errors'], $_SESSION['message']);
    }
}

// Handle the request
$action = $_GET['action'] ?? 'index';
$controller = new CourseController();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Page not found";
}