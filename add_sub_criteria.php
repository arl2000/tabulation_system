<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $criteria_id = $_POST['criteria_id'];
    $sub_criteria_name = $_POST['sub_criteria_name'];
    $points = $_POST['points'];

    $query = "INSERT INTO sub_criteria (criteria_id, sub_criteria_name, points) VALUES ('$criteria_id', '$sub_criteria_name', '$points')";
    if (mysqli_query($connect, $query)) {
        echo "Criteria added successfully.";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}
?>
