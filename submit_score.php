<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['judge_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$judge_id = $_SESSION['judge_id'];
$criteria_id = $_POST['criteria_id'];
$scores = $_POST['scores'];

$errors = [];

foreach ($scores as $participant_id => $sub_criteria_scores) {
    foreach ($sub_criteria_scores as $sub_criteria_id => $score) {
        // Ensure score is a number
        $score = floatval($score);

        // Fetch the maximum allowed score for the sub-criteria
        $max_score_query = "SELECT sub_criteria_name, points FROM sub_criteria WHERE sub_criteria_id = ?";
        $max_score_stmt = $connect->prepare($max_score_query);
        $max_score_stmt->bind_param("i", $sub_criteria_id);
        $max_score_stmt->execute();
        $max_score_result = $max_score_stmt->get_result();
        $max_score_row = $max_score_result->fetch_assoc();
        $sub_criteria_name = $max_score_row['sub_criteria_name'];
        $max_points = floatval($max_score_row['points']);

        // Fetch the participant's name
        $participant_query = "SELECT name FROM participants WHERE participant_id = ?";
        $participant_stmt = $connect->prepare($participant_query);
        $participant_stmt->bind_param("i", $participant_id);
        $participant_stmt->execute();
        $participant_result = $participant_stmt->get_result();
        $participant_row = $participant_result->fetch_assoc();
        $participant_name = $participant_row['name'];

        if ($score > $max_points) {
            // If the score exceeds the maximum allowed, add an error message
            $errors[] = "Score for participant $participant_name in sub-criteria $sub_criteria_name exceeds the maximum allowed score of $max_points.";
        } else {
            // Check if the score already exists
            $check_query = "SELECT * FROM scores WHERE criteria_id = ? AND participant_id = ? AND sub_criteria_id = ? AND judge_id = ?";
            $stmt = $connect->prepare($check_query);
            $stmt->bind_param("iiii", $criteria_id, $participant_id, $sub_criteria_id, $judge_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update the existing score
                $update_query = "UPDATE scores SET score = ? WHERE criteria_id = ? AND participant_id = ? AND sub_criteria_id = ? AND judge_id = ?";
                $update_stmt = $connect->prepare($update_query);
                $update_stmt->bind_param("diiii", $score, $criteria_id, $participant_id, $sub_criteria_id, $judge_id);
                $update_stmt->execute();
            } else {
                // Insert a new score
                $insert_query = "INSERT INTO scores (criteria_id, participant_id, sub_criteria_id, judge_id, score) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $connect->prepare($insert_query);
                $insert_stmt->bind_param("iiiid", $criteria_id, $participant_id, $sub_criteria_id, $judge_id, $score);
                $insert_stmt->execute();
            }
        }
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['message' => 'Error', 'errors' => $errors]);
} else {
    echo json_encode(['message' => 'Scores saved successfully']);
}
?>
