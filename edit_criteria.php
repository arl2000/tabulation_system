<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['event_id']) || !isset($_GET['criteria_id'])) {
    echo "Event ID or Criteria ID is not set.";
    exit;
}

$event_id = $_GET['event_id'];
$criteria_id = $_GET['criteria_id'];

// Fetch event and criteria details
$event_query = "SELECT name FROM events WHERE event_id = '$event_id'";
$event_result = mysqli_query($connect, $event_query);
$event = mysqli_fetch_assoc($event_result);
$event_name = $event['name'];

$criteria_query = "SELECT name FROM criteria WHERE criteria_id = '$criteria_id'";
$criteria_result = mysqli_query($connect, $criteria_query);
$criteria = mysqli_fetch_assoc($criteria_result);
$criteria_name = $criteria['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_criteria_name = $_POST['sub_criteria_name'];
    $points = $_POST['points'];

    // Insert sub-criteria
    $query = "INSERT INTO sub_criteria (criteria_id, sub_criteria_name, points) VALUES ('$criteria_id', '$sub_criteria_name', '$points')";
    if (mysqli_query($connect, $query)) {
        echo "<script>";
        echo "alert('New sub-criteria added successfully');";
        echo "window.location.href = 'edit_criteria.php?criteria_id=$criteria_id&event_id=$event_id';";
        echo "</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}

$sub_criteria_query = "SELECT * FROM sub_criteria WHERE criteria_id = '$criteria_id'";
$sub_criteria_result = mysqli_query($connect, $sub_criteria_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Criteria</title>
    <style>
        /* Overall CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .centered-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-top: 50px;
        }

        .criteria-details-container {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }

        .criteria-details-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .criteria-details-list li {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 16px;
        }

        .criteria-details-list li:last-child {
            border-bottom: none;
        }

        .criteria-details-list li:hover {
            background-color: #f0f0f0;
        }

        .criteria-details-list li a {
            text-decoration: none;
            color: #007bff;
        }

        .criteria-details-list li a:hover {
            text-decoration: underline;
        }

        #showModal {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #showModal:hover {
            background-color: #0056b3;
        }

        /* Style for the modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #ddd;
            width: 50%;
            border-radius: 8px;
        }

        .modal-content h2 {
            margin-top: 0;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
        }

        form label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="number"] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form input[type="submit"] {
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form input[type="submit"]:hover {
            background-color: #218838;
        }

    </style>
</head>
<body>

<div class="centered-container">
    <div class="criteria-details-container">
        <h3>Sub-Criteria for: <?php echo htmlspecialchars($criteria_name); ?> in Event: <?php echo htmlspecialchars($event_name); ?></h3>
        <ul class="criteria-details-list">
            <?php while ($sub_criteria = mysqli_fetch_assoc($sub_criteria_result)): ?>
                <li><?php echo htmlspecialchars($sub_criteria['sub_criteria_name']); ?> (Points: <?php echo htmlspecialchars($sub_criteria['points']); ?>)</li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- Button to open the modal -->
    <button id="showModal">Add Sub-Criteria</button>
</div>

<!-- The Modal -->
<div id="criteriaModal" class="modal">
    <div class="modal-content">
        <h2>Add Sub-Criteria for: <?php echo htmlspecialchars($criteria_name); ?></h2>
        <form method="post">
            <label for="sub_criteria_name">Sub-Criteria Name:</label>
            <input type="text" id="sub_criteria_name" name="sub_criteria_name" required><br>
            <label for="points">Points:</label>
            <input type="number" id="points" step="0.01" name="points" required><br>
            <input type="submit" value="Add Sub-Criteria">
        </form>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("criteriaModal");

    // Get the button that opens the modal
    var btn = document.getElementById("showModal");

    // When the user clicks on the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
