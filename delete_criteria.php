<?php
include 'db_connection.php';

if (!isset($_GET['sub_criteria_id'])) {
    echo "Sub-Criteria ID is not set.";
    exit;
}

$sub_criteria_id = $_GET['sub_criteria_id'];

// Delete the sub-criteria
$delete_sub_criteria_query = "DELETE FROM sub_criteria WHERE sub_criteria_id = '$sub_criteria_id'";
if (mysqli_query($connect, $delete_sub_criteria_query)) {
    echo "Sub-criteria deleted successfully.";
} else {
    echo "Error deleting sub-criteria: " . mysqli_error($connect);
}

mysqli_close($connect);
?>
