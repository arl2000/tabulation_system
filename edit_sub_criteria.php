<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $detail_id = $_POST['detail_id'];
    $sub_criteria_name = $_POST['sub_criteria_name'];
    $points = $_POST['points'];

    $query = "UPDATE criteria_details SET sub_criteria_name = '$sub_criteria_name', points = '$points' WHERE detail_id = '$detail_id'";
    if (mysqli_query($connect, $query)) {
        echo "Sub-criteria updated successfully.";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}
?>
