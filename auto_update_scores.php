<?php
include 'db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $criteria_id = $_POST['criteria_id'];
    $judge_id = $_POST['judge_id'];
    $scores = $_POST['scores'];

    // Fetch the maximum points allowed for the criteria
    $max_points_query = "SELECT max_points FROM criteria WHERE criteria_id = $criteria_id";
    $max_points_result = mysqli_query($connect, $max_points_query);
    $max_points_row = mysqli_fetch_assoc($max_points_result);
    $max_points = $max_points_row['max_points'];

    foreach ($scores as $participant_id => $score) {
        // Validate the score
        if (!is_numeric($score)) {
            echo "Invalid score for participant ID: $participant_id";
            continue; // Skip to the next iteration
        }

        // Check if the score exceeds the maximum points allowed
        if ($score > $max_points) {
            echo "Score exceeds maximum points for participant ID: $participant_id";
            continue; // Skip to the next iteration
        }

        // Check if a score exists for this participant, criteria, and judge
        $check_query = "SELECT * FROM scores WHERE participant_id = $participant_id AND criteria_id = $criteria_id AND judge_id = $judge_id";
        $check_result = mysqli_query($connect, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            // Update the existing score
            $update_query = "UPDATE scores SET score = $score WHERE participant_id = $participant_id AND criteria_id = $criteria_id AND judge_id = $judge_id";
            if (!mysqli_query($connect, $update_query)) {
                echo "Error updating score for participant ID: $participant_id";
            }
        } else {
            // Insert the new score
            $insert_query = "INSERT INTO scores (participant_id, criteria_id, judge_id, score) VALUES ($participant_id, $criteria_id, $judge_id, $score)";
            if (!mysqli_query($connect, $insert_query)) {
                echo "Error inserting score for participant ID: $participant_id";
            }
        }
    }
}
?>
