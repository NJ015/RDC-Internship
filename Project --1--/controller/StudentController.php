<?php

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Registration.php';
require_once __DIR__ . '/../dbconfig.php';

/**
 * Controller for handling student-related actions.
 */
class StudentController
{
    private $studentModel;
    private $registrationModel;
    private $pdo;

    /**
     * StudentController constructor.
     */
    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->studentModel = new Student($pdo);
        $this->registrationModel = new Registration($pdo);
    }

    /**
     * Display all students.
     */
    public function index()
    {
        session_start();
        $result = $this->studentModel->all();
        $students = [];
        if (isset($result['success'])) {
            $students = $result['success'];
        } elseif (isset($result['error'])) {
            $_SESSION['errors'] = (array)$result['error'];
        }
        require_once __DIR__ . '/../views/students.php';
        unset($_SESSION['errors'], $_SESSION['message']);
    }

    /**
     * Show the form to create a new student.
     */
    public function createForm()
    {
        session_start();
        require_once '../views/add_student.php';
        unset($_SESSION['errors'], $_SESSION['message']);
    }

    /**
     * Handle creation of a new student.
     */
    public function create()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->studentModel->create(
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['email'],
                $_POST['phone'] ?? null,
                $_POST['dob'] ?? null,
                $_POST['gender'] ?? null,
                $_POST['major'] ?? null
            );

            if (isset($result['error'])) {
                $_SESSION['errors'] = (array)$result['error'];
                header("Location: StudentController.php?action=createForm");
                exit;
            }

            $_SESSION['message'] = "Student added successfully!";
            header("Location: StudentController.php?action=index");
            exit;
        }
    }

    /**
     * Show the form to edit a student.
     */
    public function editForm()
    {
        session_start();
        $student_id = $_POST['edit'] ?? null;
        $result = $this->studentModel->find($student_id);

        if (!$student_id || !isset($result['success'])) {
            $_SESSION['errors'] = ['Student not found'];
            header("Location: StudentController.php?action=index");
            exit;
        }

        // Redirect to details page with edit mode enabled
        header('Location: StudentController.php?action=details&id=' . $student_id . '&edit=true');
        exit;
    }

    /**
     * Handle editing a student.
     */
    public function edit()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
            $student_id = $_POST['edit_id'];

            $result = $this->studentModel->update(
                $student_id,
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['dob'],
                $_POST['gender'],
                $_POST['major']
            );

            $tab = $_SESSION['active_tab'] ?? 'info';

            if (isset($result['success'])) {
                $_SESSION['message'] = "Student updated successfully!";
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Unknown error']);
            }
            header('Location: StudentController.php?action=details&id=' . $student_id . '&tab=' . $tab);
            exit;
        }
    }

    /**
     * Delete a student.
     */
    public function delete()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $student_id = $_POST['delete_id'];
            $result = $this->studentModel->delete($student_id);

            if (isset($result['success'])) {
                $_SESSION['message'] = "Student deleted successfully!";
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Delete failed.']);
            }
            header("Location: StudentController.php?action=index");
            exit;
        }
    }

    /**
     * Show details for a student.
     * @param int $student_id
     */
    public function details($student_id = null)
    {
        session_start();
        $student_id = $_GET['id'] ?? $student_id;
        $result = $this->studentModel->find($student_id);

        if (!$student_id || !isset($result['success'])) {
            $_SESSION['errors'] = ['Student not found'];
            header("Location: StudentController.php?action=index");
            exit;
        }

        $student = $result['success'];

        $coursesResult = $student->getCourses();
        $courses = isset($coursesResult['success']) ? $coursesResult['success'] : [];
        $scheduleResult = $this->studentModel->getSchedule($student_id);
        $schedule = isset($scheduleResult['success']) ? $scheduleResult['success'] : [];
        $availableCoursesResult = $this->studentModel->getCoursesNotIn($student_id);
        $availableCourses = isset($availableCoursesResult['success']) ? $availableCoursesResult['success'] : [];

        require_once __DIR__ . '/../views/student_details.php';
        unset($_SESSION['errors'], $_SESSION['message']);
    }

    /**
     * Register a student to a course.
     */
    public function registerToCourse()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $student_id = $_POST['student_id'];
            $course_id = $_POST['course_id'];

            $result = $this->registrationModel->register($student_id, $course_id);

            $tab = $_SESSION['active_tab'] ?? 'courses';

            if (isset($result['success'])) {
                $_SESSION['message'] = "Registration successful";
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Registration failed.']);
            }
            header("Location: StudentController.php?action=details&id=" . $student_id . "&tab=" . $tab);
            exit;
        }
    }

    /**
     * Unregister a student from a course.
     */
    public function unregisterFromCourse()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $student_id = $_POST['student_id'];
            $course_id = $_POST['course_id'];

            $result = $this->registrationModel->unregister($student_id, $course_id);

            $tab = $_SESSION['active_tab'] ?? 'courses';

            if (isset($result['success'])) {
                $_SESSION['message'] = "Unregistered from course";
            } else {
                $_SESSION['errors'] = (array)($result['error'] ?? ['Unregister failed.']);
            }
            header("Location: StudentController.php?action=details&id=" . $student_id . "&tab=" . $tab);
            exit;
        }
    }
}

// Handle the request
$action = $_GET['action'] ?? 'index';
$controller = new StudentController();

if (isset($_GET['id']) && $action === 'details') {
    $controller->details($_GET['id']);
} elseif (method_exists($controller, $action)) {
    $controller->$action();
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Page not found";
}