<?php

require_once 'Student.php';

class Course
{
    public $id;
    public $course_code;
    public $course_name;
    public $description;
    public $credits;
    public $created_at;
    public $updated_at;

    private $pdo;

    /**
     * Course constructor.
     * @param PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a Course instance from a database row.
     * @param PDO $pdo
     * @param array $row
     * @return Course
     */
    public static function fromRow($pdo, $row)
    {
        $course = new self($pdo);
        $course->id = $row['id'];
        $course->course_code = $row['course_code'];
        $course->course_name = $row['course_name'];
        $course->description = $row['description'];
        $course->credits = $row['credits'];
        $course->created_at = $row['created_at'];
        $course->updated_at = $row['updated_at'];
        return $course;
    }

    /**
     * Create a new course.
     * @param string $code
     * @param string $name
     * @param string $desc
     * @param int $credits
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function create($code, $name, $desc, $credits = 3)
    {
        $errors = self::validate($this->pdo, [
            'course_code' => $code,
            'course_name' => $name,
            'description' => $desc,
            'credits' => $credits
        ]);
        if (!empty($errors)) {
            return ['error' => $errors];
        }
        try {
            $stmt = $this->pdo->prepare("INSERT INTO courses (course_code, course_name, description, credits) VALUES (?, ?, ?, ?)");
            $stmt->execute([$code, $name, $desc, $credits]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Validate course data.
     * @param PDO $pdo
     * @param array $data
     * @param bool $isEdit
     * @param int|null $currentId
     * @return array List of error messages (empty if valid)
     */
    public static function validate($pdo, $data, $isEdit = false, $currentId = null)
    {
        $errors = [];

        if (empty($data['course_code'])) {
            $errors[] = "Course code is required.";
        }

        if (empty($data['course_name'])) {
            $errors[] = "Course name is required.";
        }

        if (!empty($data['credits']) && (!is_numeric($data['credits']) || $data['credits'] < 1)) {
            $errors[] = "Credits must be a positive number.";
        } elseif ($data['credits'] > 6) {
            $errors[] = "Credits cannot exceed 6.";
        }

        try {
            // Uniqueness check
            $query = "SELECT id FROM courses WHERE (course_code = :code OR course_name = :name)";
            if ($isEdit) {
                $query .= " AND id != :id";
            }

            $stmt = $pdo->prepare($query);
            $params = [
                ':code' => $data['course_code'],
                ':name' => $data['course_name']
            ];
            if ($isEdit) $params[':id'] = $currentId;
            $stmt->execute($params);
            if ($stmt->fetch()) {
                $errors[] = "Course code or name already exists.";
            }
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     * Update an existing course.
     * @param int $id
     * @param string $code
     * @param string $name
     * @param string $desc
     * @param int $credits
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function update($id, $code, $name, $desc, $credits)
    {
        $errors = self::validate($this->pdo, [
            'course_code' => $code,
            'course_name' => $name,
            'description' => $desc,
            'credits' => $credits
        ], true, $id);

        if (!empty($errors)) {
            return ['error' => $errors];
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE courses SET course_code = ?, course_name = ?, description = ?, credits = ? WHERE id = ?");
            $stmt->execute([$code, $name, $desc, $credits, $id]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Delete a course by ID.
     * @param int $id
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Find a course by ID.
     * @param int $id
     * @return array ['success' => Course] on success, ['error' => ...] on failure
     */
    public function find($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                return ['success' => self::fromRow($this->pdo, $row)];
            } else {
                return ['error' => 'Course not found.'];
            }
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all courses.
     * @return array ['success' => Course[]] on success, ['error' => ...] on failure
     */
    public function all()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
            $courses = [];
            while ($row = $stmt->fetch()) {
                $courses[] = self::fromRow($this->pdo, $row);
            }
            return ['success' => $courses];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all students registered in this course.
     * @return array ['success' => Student[]] on success, ['error' => ...] on failure
     */
    public function getStudents()
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT s.* FROM students s
            JOIN registrations r ON s.id = r.student_id
            WHERE r.course_id = ?
        ");
            $stmt->execute([$this->id]);

            $students = [];
            while ($row = $stmt->fetch()) {
                $students[] = Student::fromRow($this->pdo, $row);
            }
            return ['success' => $students];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all schedule entries for this course or a specific schedule entry.
     * @param int $course_id
     * @param int|null $schedule_id
     * @return array ['success' => object|object[]] on success, ['error' => ...] on failure
     */
    public function getSchedule($course_id, $schedule_id = NULL)
    {
        try {
            if ($schedule_id) {
                $stmt = $this->pdo->prepare("SELECT * FROM schedules WHERE id = ?");
                $stmt->execute([$schedule_id]);
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                if ($result) {
                    return ['success' => $result];
                } else {
                    return ['error' => 'Schedule not found.'];
                }
            }
            $stmt = $this->pdo->prepare("SELECT * FROM schedules WHERE course_id = ? ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), start_time");
            $stmt->execute([$course_id]);
            return ['success' => $stmt->fetchAll(PDO::FETCH_OBJ)];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Add a schedule entry for this course.
     * @param int $course_id
     * @param string $day
     * @param string $start_time
     * @param string $end_time
     * @param string|null $location
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function addSchedule($course_id, $day, $start_time, $end_time, $location = null)
    {
        $conflict = $this->hasScheduleConflict($course_id, $day, $start_time, $end_time);
        if ($conflict !== false) {
            return ['error' => $conflict];
        } else if ($start_time >= $end_time) {
            return ['error' => "Start time must be before end time."];
        } else {
            try {
                $stmt = $this->pdo->prepare("INSERT INTO schedules (course_id, day_of_week, start_time, end_time, location)
                                 VALUES (:course_id, :day, :start_time, :end_time, :location)");
                $stmt->execute([
                    'course_id' => $course_id,
                    'day' => $day,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'location' => $location
                ]);
                return ['success' => true];
            } catch (PDOException $e) {
                return ['error' => $e->getMessage()];
            }
        }
    }

    /**
     * Check if a schedule conflict exists for a course.
     * @param int $course_id
     * @param string $day
     * @param string $start_time
     * @param string $end_time
     * @param bool $isNew
     * @param int|null $schedule_id
     * @return string|false Error message if conflict, false if no conflict
     */
    public function hasScheduleConflict($course_id, $day, $start_time, $end_time, $isNew = true, $schedule_id = null)
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*) FROM schedules
        WHERE course_id = :course_id
          AND day_of_week = :day
          AND (
            NOT (
                end_time <= :start_time_new OR
                start_time >= :end_time_new
            )
          )" . ($isNew ? "" : " AND id != :schedule_id") . "
        ");

        $params = [
            ':course_id' => $course_id,
            ':day' => $day,
            ':start_time_new' => $start_time,
            ':end_time_new' => $end_time
        ];

        if (!$isNew) {
            $params[':schedule_id'] = $schedule_id;
        }

        try {
            $stmt->execute($params);
            $result = $stmt->fetchColumn();
            if ($result > 0) {
                return "This course already has a class scheduled during that time on $day.";
            }
            return false;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Delete a schedule entry by ID.
     * @param int $scheduleId
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function deleteSchedule($scheduleId)
    {
        try {
            $query = "DELETE FROM schedules WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $scheduleId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Update a schedule entry.
     * @param int $scheduleId
     * @param string $day
     * @param string $start_time
     * @param string $end_time
     * @param string|null $location
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function updateSchedule($scheduleId, $day, $start_time, $end_time, $location)
    {
        try {
            // get course_id of this schedule
            $stmt = $this->pdo->prepare("SELECT course_id FROM schedules WHERE id = ?");
            $stmt->execute([$scheduleId]);
            $course_id = $stmt->fetchColumn();
            if ($course_id === false) {
                return ['error' => 'Schedule not found.'];
            }

            $conflict = $this->hasScheduleConflict($course_id, $day, $start_time, $end_time, false, $scheduleId);
            if ($conflict !== false) {
                return ['error' => $conflict];
            } else if ($start_time >= $end_time) {
                return ['error' => "Start time must be before end time."];
            }

            $stmt = $this->pdo->prepare("UPDATE schedules SET day_of_week = ?, start_time = ?, end_time = ?, location = ? WHERE id = ?");
            $stmt->execute([$day, $start_time, $end_time, $location, $scheduleId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get student counts for all courses.
     * @return array ['success' => array[]] on success, ['error' => ...] on failure
     */
    public function getCourseStudentCounts()
    {
        try {
            $stmt = $this->pdo->query("
            SELECT 
                c.id,
                c.course_code,
                c.course_name,
                COUNT(registrations.student_id) AS student_count
            FROM courses as c
            LEFT JOIN registrations ON c.id = registrations.course_id
            GROUP BY c.id, c.course_code
            ORDER BY student_count DESC
            ");
            return ['success' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get students not registered in a specific course.
     * @param int $course_id
     * @return array ['success' => array[]] on success, ['error' => ...] on failure
     */
    public function getStudentsNotInCourse($course_id)
    {
        try {
            $query = "
            SELECT s.id, s.student_id, s.first_name, s.last_name, s.email
            FROM students s
            WHERE s.id NOT IN (
                SELECT r.student_id
                FROM registrations r
                WHERE r.course_id = :course_id
            )";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':course_id' => $course_id]);

            return ['success' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}