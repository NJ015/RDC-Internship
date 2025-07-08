<?php

require_once '../db/dbconfig.php';

header('Content-Type: application/json');

// fetch 1
if (isset($_POST['id'])) {
}
// fetch all
else {

    // Step 2: Fetch all issues with project names
    $issues = [];
    $issue_query = "
    SELECT issues.*, projects.name AS project_name
    FROM issues
    LEFT JOIN projects ON issues.project_id = projects.id
";
    $issue_result = $conn->query($issue_query);
    while ($row = $issue_result->fetch_assoc()) {
        $row['assignees'] = [];
        $issues[$row['id']] = $row;
    }

    // Step 3: Fetch all assignees per issue
    $assignee_query = "
    SELECT issue_assignee.issue_id, users.id AS user_id, users.username, users.role AS user_role
    FROM issue_assignee
    JOIN users ON users.id = issue_assignee.user_id
";
    $assignee_result = $conn->query($assignee_query);

    // Step 4: Attach assignees to the right issue
    while ($row = $assignee_result->fetch_assoc()) {
        $issue_id = $row['issue_id'];

        if (isset($issues[$issue_id])) {
            $issues[$issue_id]['assignees'][] = [
                'user_id' => $row['user_id'],
                'username' => $row['username']
            ];
        }
    }

    // Step 5: Reindex (remove associative keys) and send result
    $final_result = array_values($issues);
    echo json_encode($final_result);
}
