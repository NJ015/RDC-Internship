<?php

require_once 'Course.php';

class Student
{
    public $id;
    public $student_id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $dob;
    public $gender;
    public $major;
    public $created_at;
    public $updated_at;

    private $pdo;

    /**
     * Student constructor.
     * @param PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a Student object from a DB row.
     * @param PDO $pdo
     * @param array $row
     * @return Student
     */
    public static function fromRow($pdo, $row)
    {
        $student = new self($pdo);
        $student->id = $row['id'];
        $student->student_id = $row['student_id'];
        $student->first_name = $row['first_name'];
        $student->last_name = $row['last_name'];
        $student->email = $row['email'];
        $student->phone = $row['phone'];
        $student->dob = $row['dob'];
        $student->gender = $row['gender'];
        $student->major = $row['major'];
        $student->created_at = $row['created_at'];
        $student->updated_at = $row['updated_at'];
        return $student;
    }

    /**
     * Create a new student.
     * @param string $first
     * @param string $last
     * @param string $email
     * @param string|null $phone
     * @param string|null $dob
     * @param string|null $gender
     * @param string|null $major
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function create($first, $last, $email, $phone = null, $dob = null, $gender = null, $major = null)
    {
        $errors = self::validate($this->pdo, [
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email,
            'phone' => $phone,
            'dob' => $dob,
        ], true);
        if (!empty($errors)) {
            return ['error' => $errors];
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO students (first_name, last_name, email, phone, dob, gender, major)
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first, $last, $email, $phone, $dob, $gender, $major]);

            $autoId = $this->pdo->lastInsertId();
            $student_id = date('Y') . str_pad($autoId, 4, '0', STR_PAD_LEFT);

            $stmt = $this->pdo->prepare("UPDATE students SET student_id = ? WHERE id = ?");
            $stmt->execute([$student_id, $autoId]);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Validate student data.
     * @param PDO $pdo
     * @param array $data
     * @param bool $isNew
     * @return array List of error messages (empty if valid)
     */
    public static function validate($pdo, $data, $isNew = false)
    {
        $errors = [];

        if (empty($data['first_name'])) {
            $errors[] = "First name is required.";
        }
        if (empty($data['last_name'])) {
            $errors[] = "Last name is required.";
        }
        if (empty($data['email'])) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        try {
            if ($isNew) {
                $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
                $stmt->execute([$data['email']]);
            } else {
                $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $data['id'] ?? '']);
            }

            if ($stmt->fetch()) {
                $errors[] = "Email already exists.";
            }
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
        }

        if (!empty($data['phone']) && !preg_match('/^\d{6,15}$/', $data['phone'])) {
            $errors[] = "Invalid phone number format.";
        }

        return $errors;
    }

    /**
     * Update a student.
     * @param int $id
     * @param string $first
     * @param string $last
     * @param string $email
     * @param string|null $phone
     * @param string|null $dob
     * @param string|null $gender
     * @param string|null $major
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function update($id, $first, $last, $email, $phone = null, $dob = null, $gender = null, $major = null)
    {
        $errors = self::validate($this->pdo, [
            'id' => $id,
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email,
            'phone' => $phone,
            'dob' => $dob,
        ]);
        if (!empty($errors)) {
            return ['error' => $errors];
        }
        try {
            $stmt = $this->pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, phone = ?, dob = ?, gender = ?, major = ? WHERE id = ?");
            $stmt->execute([$first, $last, $email, $phone, $dob, $gender, $major, $id]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Delete a student by ID.
     * @param int $id
     * @return array ['success' => true] on success, ['error' => ...] on failure
     */
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Find a student by DB id.
     * @param int $id
     * @return array ['success' => Student] on success, ['error' => ...] on failure
     */
    public function find($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                return ['success' => self::fromRow($this->pdo, $row)];
            } else {
                return ['error' => 'Student not found.'];
            }
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Find a student by their unique student ID.
     * @param string $student_id
     * @return array ['success' => Student] on success, ['error' => ...] on failure
     */
    public function findByStudentID($student_id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $row = $stmt->fetch();
            if ($row) {
                return ['success' => self::fromRow($this->pdo, $row)];
            } else {
                return ['error' => 'Student not found.'];
            }
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all students.
     * @return array ['success' => Student[]] on success, ['error' => ...] on failure
     */
    public function all()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM students ORDER BY created_at DESC");
            $students = [];
            while ($row = $stmt->fetch()) {
                $students[] = self::fromRow($this->pdo, $row);
            }
            return ['success' => $students];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all courses for this student.
     * @return array ['success' => Course[]] on success, ['error' => ...] on failure
     */
    public function getCourses()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.* FROM courses c
                JOIN registrations r ON c.id = r.course_id
                WHERE r.student_id = ?
            ");
            $stmt->execute([$this->id]);

            $courses = [];
            while ($row = $stmt->fetch()) {
                $courses[] = Course::fromRow($this->pdo, $row);
            }
            return ['success' => $courses];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get the schedule for a student.
     * @param int $student_id
     * @return array ['success' => object[]] on success, ['error' => ...] on failure
     */
    public function getSchedule($student_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.course_name, s.day_of_week, s.start_time, s.end_time, s.location
                FROM registrations r
                JOIN courses c ON r.course_id = c.id
                JOIN schedules s ON s.course_id = c.id
                WHERE r.student_id = ?
                ORDER BY FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), s.start_time
            ");
            $stmt->execute([$student_id]);
            return ['success' => $stmt->fetchAll(PDO::FETCH_OBJ)];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all courses the student is NOT registered in.
     * @param int $student_id
     * @return array ['success' => array[]] on success, ['error' => ...] on failure
     */
    public function getCoursesNotIn($student_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.*
                FROM courses c
                WHERE c.id NOT IN (
                    SELECT course_id
                    FROM registrations
                    WHERE student_id = ?
                )
            ");
            $stmt->execute([$student_id]);
            return ['success' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}