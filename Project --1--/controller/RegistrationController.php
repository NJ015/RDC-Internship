<?php

require_once __DIR__ . '/../models/Registration.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../dbconfig.php';

/**
 * Controller for handling registration-related actions.
 */
class RegistrationController
{
    private $registrationModel;
    private $courseModel;
    private $studentModel;
    private $pdo;

    /**
     * RegistrationController constructor.
     */
    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->registrationModel = new Registration($pdo);
        $this->courseModel = new Course($pdo);
        $this->studentModel = new Student($pdo);
    }

    /**
     * Show the registration table view for a student or course.
     */
    public function registerTableView()
    {
        session_start();

        if (isset($_GET['student_id'])) {
            $student_id = $_GET['student_id'];
            $unregisteredCoursesResult = $this->studentModel->getCoursesNotIn($student_id);
            $unregisteredCourses = isset($unregisteredCoursesResult['success']) ? $unregisteredCoursesResult['success'] : [];
            if (isset($unregisteredCoursesResult['error'])) {
                $_SESSION['errors'] = (array)$unregisteredCoursesResult['error'];
            }
            require_once '../views/add_course_to_student.php';
        } else if (isset($_GET['course_id'])) {
            $course_id = $_GET['course_id'];
            $unregisteredStudentsResult = $this->courseModel->getStudentsNotInCourse($course_id);
            $unregisteredStudents = isset($unregisteredStudentsResult['success']) ? $unregisteredStudentsResult['success'] : [];
            if (isset($unregisteredStudentsResult['error'])) {
                $_SESSION['errors'] = (array)$unregisteredStudentsResult['error'];
            }
            require_once '../views/add_student_to_course.php';
        } else {
            $_SESSION['errors'] = ['No student or course ID provided.'];
            require_once '../views/error.php';
        }
        unset($_SESSION['errors'], $_SESSION['message']);
    }

    /**
     * Register a student to a course.
     */
    public function register()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['student_id']) || isset($_POST['course_id']))) {
            $course_id = $_POST['course_id'] ?? $_GET['course_id'] ?? null;
            $student_id = $_POST['student_id'] ?? $_GET['student_id'] ?? null;

            $result = $this->registrationModel->register($student_id, $course_id);

            if (isset($result['success'])) {
                $_SESSION['message'] = "Registration successful!";
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Registration failed.']);
            }

            if (isset($_GET['student_id'])) {
                header('Location: RegistrationController.php?action=registerTableView&student_id=' . $student_id);
            } else {
                header('Location: RegistrationController.php?action=registerTableView&course_id=' . $course_id);
            }
            exit;
        }
    }

    /**
     * Unregister a student from a course.
     */
    public function unregister()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['student_id']) || isset($_POST['course_id']))) {
            $course_id = $_POST['course_id'] ?? $_GET['course_id'] ?? null;
            $student_id = $_POST['student_id'] ?? $_GET['student_id'] ?? null;

            $result = $this->registrationModel->unregister($student_id, $course_id);

            if (isset($result['success'])) {
                $_SESSION['message'] = "Unregistered from course";
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Unregister failed.']);
            }

            if (isset($_GET['student_id'])) {
                header('Location: StudentController.php?action=details&id=' . $student_id);
            } else if (isset($_GET['course_id'])) {
                header('Location: CourseController.php?action=details&id=' . $course_id);
            }
            exit;
        }
    }
}

// Handle the request
$action = $_GET['action'] ?? 'registerTableView';
$controller = new RegistrationController();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Page not found";
}