<?php
include 'db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $criteria_id = $_POST['criteria_id'];
    $judge_id = $_POST['judge_id'];
    $scores = $_POST['scores'];

    foreach ($scores as $participant_id => $score) {
        // Validate the score
        if (!is_numeric($score)) {
            echo "Invalid score for participant ID: $participant_id";
            continue; // Skip to the next iteration
        }

        // Update the score in the database
        $update_query = "UPDATE scores SET score = $score WHERE participant_id = $participant_id AND criteria_id = $criteria_id AND judge_id = $judge_id";
        if (!mysqli_query($connect, $update_query)) {
            echo "Error updating score for participant ID: $participant_id";
        }
    }

    // Redirect back to the judge dashboard after updating scores
    header("Location: judge_dashboard.php");
}
?>
