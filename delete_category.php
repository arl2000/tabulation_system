<?php
include 'db_connection.php';


if (!isset($_GET['criteria_id'])) {
    echo "Criteria ID is not set.";
    exit;
}

$criteria_id = $_GET['criteria_id'];

// Delete sub-criteria associated with the criteria
$delete_sub_criteria_query = "DELETE FROM sub_criteria WHERE criteria_id = '$criteria_id'";
mysqli_query($connect, $delete_sub_criteria_query);

// Delete the criteria
$delete_criteria_query = "DELETE FROM criteria WHERE criteria_id = '$criteria_id'";
if (mysqli_query($connect, $delete_criteria_query)) {
    echo "Criteria and its sub-criteria deleted successfully.";
} else {
    echo "Error deleting criteria: " . mysqli_error($connect);
}

mysqli_close($connect);
?>
