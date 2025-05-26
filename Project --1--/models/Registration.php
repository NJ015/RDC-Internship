<?php

class Registration
{
    private $pdo;

    /**
     * Registration constructor.
     * @param PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Register a student for a course.
     * @param int $studentId
     * @param int $courseId
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function register($studentId, $courseId)
    {
        if ($this->isRegistered($studentId, $courseId)) {
            return ['error' => ['Student already registered.']];
        } elseif ($this->hasScheduleConflict($studentId, $courseId)) {
            return ['error' => ['Schedule conflict with another course.']];
        } else {
            $errors = self::validate($this->pdo, $studentId, $courseId);
            if (!empty($errors)) {
                return ['error' => $errors];
            }
            try {
                $query = "INSERT INTO registrations (student_id, course_id) VALUES (:student_id, :course_id)";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([
                    ':student_id' => $studentId,
                    ':course_id' => $courseId
                ]);
                return ['success' => true];
            } catch (PDOException $e) {
                return ['error' => $e->getMessage()];
            }
        }
    }

    /**
     * Unregister a student from a course.
     * @param int $studentId
     * @param int $courseId
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function unregister($studentId, $courseId)
    {
        try {
            $query = "DELETE FROM registrations WHERE student_id = :student_id AND course_id = :course_id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':student_id' => $studentId,
                ':course_id' => $courseId
            ]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Validate registration data.
     * @param PDO $pdo
     * @param int $student_id
     * @param int $course_id
     * @return array List of error messages (empty if valid)
     */
    public static function validate($pdo, $student_id, $course_id)
    {
        $errors = [];

        // Check student exists
        $stmt = $pdo->prepare("SELECT id FROM students WHERE id = ?");
        $stmt->execute([$student_id]);
        if (!$stmt->fetch()) {
            $errors[] = "Student does not exist.";
        }

        // Check course exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        if (!$stmt->fetch()) {
            $errors[] = "Course does not exist.";
        }

        // Check already registered
        $stmt = $pdo->prepare("SELECT id FROM registrations WHERE student_id = ? AND course_id = ?");
        $stmt->execute([$student_id, $course_id]);
        if ($stmt->fetch()) {
            $errors[] = "Student is already registered for this course.";
        }

        return $errors;
    }

    /**
     * Check if a student is already registered to a course.
     * @param int $studentId
     * @param int $courseId
     * @return bool
     */
    public function isRegistered($studentId, $courseId)
    {
        $query = "SELECT 1 FROM registrations WHERE student_id = :student_id AND course_id = :course_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':student_id' => $studentId,
            ':course_id' => $courseId
        ]);
        return $stmt->fetchColumn() !== false;
    }

    /**
     * Check if registering for a course would cause a schedule conflict.
     * @param int $studentId
     * @param int $newCourseId
     * @return bool True if conflict, false otherwise
     */
    public function hasScheduleConflict($studentId, $newCourseId)
    {
        // Get the schedule of the new course
        $stmt = $this->pdo->prepare("
            SELECT day_of_week, start_time, end_time
            FROM schedules
            WHERE course_id = :course_id
        ");
        $stmt->execute([':course_id' => $newCourseId]);
        $newCourseSchedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$newCourseSchedule) return false; // No schedule = no conflict

        // Get all courses the student is registered in
        $stmt = $this->pdo->prepare("
            SELECT c.id
            FROM courses c
            INNER JOIN registrations r ON r.course_id = c.id
            WHERE r.student_id = :student_id
        ");
        $stmt->execute([':student_id' => $studentId]);
        $registeredCourses = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($registeredCourses)) return false;

        // Now check for conflicts
        foreach ($registeredCourses as $registeredCourseId) {
            $stmt = $this->pdo->prepare("
                SELECT day_of_week, start_time, end_time
                FROM schedules
                WHERE course_id = :course_id
            ");
            $stmt->execute([':course_id' => $registeredCourseId]);
            $registeredSchedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registeredSchedule as $reg) {
                foreach ($newCourseSchedule as $new) {
                    if (
                        $reg['day_of_week'] === $new['day_of_week'] &&
                        $reg['start_time'] < $new['end_time'] &&
                        $reg['end_time'] > $new['start_time']
                    ) {
                        return true; // Conflict found
                    }
                }
            }
        }

        return false; // No conflict
    }
}