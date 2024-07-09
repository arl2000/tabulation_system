<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo "Unauthorized access.";
    exit;
}

if (!isset($_GET['participant_id'])) {
    echo "Participant ID is not set.";
    exit;
}

$participant_id = $_GET['participant_id'];

// Delete the participant
$delete_participant_query = "DELETE FROM participants WHERE participant_id = '$participant_id'";
if (mysqli_query($connect, $delete_participant_query)) {
    echo "Participant deleted successfully.";
} else {
    echo "Error deleting participant: " . mysqli_error($connect);
}

mysqli_close($connect);
?>
