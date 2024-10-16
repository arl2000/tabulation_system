<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['event_id'])) {
    echo "Event ID is not set.";
    exit;
}

$event_id = $_GET['event_id'];

// Fetch event name based on event ID
$event_query = "SELECT name FROM events WHERE event_id = '$event_id'";
$event_result = mysqli_query($connect, $event_query);
if ($event_result && mysqli_num_rows($event_result) > 0) {
    $event = mysqli_fetch_assoc($event_result);
    $event_name = $event['name'];
} else {
    echo "Event not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action == 'add_judge') {
            $judge_name = $_POST['judge_name'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
            $query = "INSERT INTO judges (event_id, name, password) VALUES ('$event_id', '$judge_name', '$password')";
            executeQuery($query, "New judge added successfully", "Error adding judge");
        } elseif ($action == 'add_criteria') {
            $criteria_name = $_POST['criteria_name'];
            $query = "INSERT INTO criteria (event_id, name) VALUES ('$event_id', '$criteria_name')";
            if (mysqli_query($connect, $query)) {
                $criteria_id = mysqli_insert_id($connect);
                if (isset($_POST['sub_criteria_name']) && isset($_POST['points'])) {
                    $sub_criteria_names = $_POST['sub_criteria_name'];
                    $points = $_POST['points'];
                    foreach ($sub_criteria_names as $index => $sub_criteria_name) {
                        $sub_criteria_name = mysqli_real_escape_string($connect, $sub_criteria_name);
                        $points_value = mysqli_real_escape_string($connect, $points[$index]);
                        $sub_query = "INSERT INTO sub_criteria (criteria_id, sub_criteria_name, points) VALUES ('$criteria_id', '$sub_criteria_name', '$points_value')";
                        if (!mysqli_query($connect, $sub_query)) {
                            echo "Error adding sub-criteria: " . mysqli_error($connect);
                        }
                    }
                }
                echo "<script>";
                echo "alert('New criteria and sub-criteria added successfully');";
                echo "window.location.href = 'manage_events.php?event_id=$event_id';";
                echo "</script>";
            } else {
                echo "Error adding criteria: " . mysqli_error($connect);
            }
        } elseif ($action == 'add_participant') {
            $participant_number = $_POST['participant_number'];
            $participant_name = $_POST['participant_name'];
            $participant_gender = $_POST['participant_gender'];
            $query = "INSERT INTO participants (event_id, number, name, gender) VALUES ('$event_id', '$participant_number', '$participant_name', '$participant_gender')";
            executeQuery($query, "New participant added successfully", "Error adding participant");
        }
    }
}

function executeQuery($query, $successMessage, $errorMessage) {
    global $connect, $event_id;
    if (mysqli_query($connect, $query)) {
        echo "<script>";
        echo "alert('$successMessage');";
        echo "window.location.href = 'manage_events.php?event_id=$event_id';";
        echo "</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action == 'edit') {
            $judge_id = $_POST['judge_id'];
            $judge_name = $_POST['name'];
            $password = $_POST['password'];

            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $query = "UPDATE judges SET name='$judge_name', password='$hashed_password' WHERE judge_id='$judge_id' AND event_id='$event_id'";
            } else {
                $query = "UPDATE judges SET name='$judge_name' WHERE judge_id='$judge_id' AND event_id='$event_id'";
            }

            if (mysqli_query($connect, $query)) {
                echo "<script>";
                echo "alert('Judge updated successfully');";
                echo "window.location.href = 'manage_events.php?event_id=$event_id';";
                echo "</script>";
            } else {
                echo "Error: " . $query . "<br>" . mysqli_error($connect);
            }
        } elseif ($action == 'delete') {
            $judge_id = $_POST['judge_id'];
            $query = "DELETE FROM judges WHERE judge_id='$judge_id' AND event_id='$event_id'";
            if (mysqli_query($connect, $query)) {
                echo "<script>";
                echo "alert('Judge deleted successfully');";
                echo "window.location.href = 'manage_events.php?event_id=$event_id';";
                echo "</script>";
            } else {
                echo "Error: " . $query . "<br>" . mysqli_error($connect);
            }
        }
    }
}

// Fetch judges for the event
$judges_query = "SELECT judge_id, name FROM judges WHERE event_id = '$event_id'";
$judges_result = mysqli_query($connect, $judges_query);

// Fetch all events
$events_query = "SELECT * FROM events";
$events_result = mysqli_query($connect, $events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event</title>
    <style>
        /* Enhanced CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #ddd;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .modal-content label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .modal-content input[type="text"],
        .modal-content input[type="number"],
        .modal-content input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .modal-content button {
            padding: 10px 20px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-content button[type="submit"] {
            background-color: #28a745;
        }

        .modal-content button[type="submit"]:hover,
        .modal-content button[type="button"]:hover {
            background-color: #0056b3;
        }

        .centered-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-top: 50px;
            width: 100%;
        }

        .criteria-list-container,
        .participants-list-container,
        .judges-list-container {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 90%;
            max-width: 1000px;
        }

        .criteria-list-container h3,
        .participants-list-container h3,
        .judges-list-container h3 {
            margin-top: 0;
            color: #007bff;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .modal-content {
                width: 95%;
            }

            .criteria-list-container,
            .participants-list-container,
            .judges-list-container {
                width: 95%;
            }
        }

        /* style for judge modal */
        .centered-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-top: 50px;
            width: 100%;
        }

        .judges-list-container {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 100%;
        }

        .judges-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .judges-list li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .judges-list li:last-child {
            border-bottom: none;
        }

        button.edit-btn, button.delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button.edit-btn {
            background-color: #28a745;
            color: #fff;
        }

        button.edit-btn:hover {
            background-color: #218838;
        }

        button.delete-btn {
            background-color: #dc3545;
            color: #fff;
        }

        button.delete-btn:hover {
            background-color: #c82333;
        }

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

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        form label {
            display: block;
            margin-bottom: 5px;
        }

        form input[type="text"],
        form input[type="password"],
        form input[type="submit"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="centered-container">
<button onclick="redirectToJudgeScores()">View Judges' Scores</button>

    <!-- Add Judge Section -->
    <div class="judges-list-container">
        <div class="centered-container">
            <div class="judges-list-container">
                    <h3>Judges for: <?php echo htmlspecialchars($event_name); ?></h3>
                    <ul class="judges-list">
                        <?php while ($judge = mysqli_fetch_assoc($judges_result)): ?>
                            <li>
                                <?php echo htmlspecialchars($judge['name']); ?>
                                <div>
                                    <button class="edit-btn" onclick="editJudge('<?php echo $judge['judge_id']; ?>', '<?php echo htmlspecialchars($judge['name']); ?>')">Edit</button>
                                    <button class="delete-btn" onclick="deleteJudge('<?php echo $judge['judge_id']; ?>')">Delete</button>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
        </div>
        <button onclick="document.getElementById('addJudgeModal').style.display='block'">Add Judge</button>
    </div>

    <!-- Add Criteria Section -->
    <div class="criteria-list-container">
        <!-- <h3>Criteria for: <?php echo htmlspecialchars($event_name); ?></h3> -->
        <!-- Display Criteria list -->
        <?php include 'add_criteria.php'; ?><br>
        <button onclick="document.getElementById('addCriteriaModal').style.display='block'">Add Category</button>
    </div>

    <!-- Add Participant Section -->
    <div class="participants-list-container">
        <!-- <h3>Participants for: <?php echo htmlspecialchars($event_name); ?></h3> -->
        <!-- Display Participants list -->
        <?php include 'add_participants.php'; ?><br>
        <button onclick="document.getElementById('addParticipantModal').style.display='block'">Add Participant</button>
    </div>
</div>

<!-- Modals for adding Judge, Criteria, and Participant -->
<!-- Add Judge Modal -->
<div id="addJudgeModal" class="modal">
    <form class="modal-content" method="post">
        <input type="hidden" name="action" value="add_judge">
        <h2>Add Judge</h2>
        <label for="judge_name">Judge Name:</label>
        <input type="text" id="judge_name" name="judge_name" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Add Judge</button>
        <button type="button" onclick="document.getElementById('addJudgeModal').style.display='none'">Cancel</button>
    </form>
</div>
<div id="judgeModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle">Edit Judge for Event: <?php echo htmlspecialchars($event_name); ?></h2>
        <form id="judgeForm" method="post">
            <input type="hidden" id="judge_id" name="judge_id">
            <input type="hidden" id="action" name="action" value="edit">
            <label for="name">Judge Name:</label>
            <input type="text" id="name" name="name" required><br>
            <label for="password" id="passwordLabel">Password:</label>
            <input type="password" id="password" name="password"><br>
            <input type="submit" value="Save Changes">
        </form>
    </div>
</div>


<!-- Add Criteria Modal -->
<div id="addCriteriaModal" class="modal">
    <form class="modal-content" method="post">
        <input type="hidden" name="action" value="add_criteria">
        <h2>Add Category</h2>
        <label for="criteria_name">Category Name:</label>
        <input type="text" id="criteria_name" name="criteria_name" required><br>

        <div id="subCriteriaForm">
            <!-- <div class="sub-criteria">
                <label for="sub_criteria_name_1">Criteria Name:</label>
                <input type="text" id="sub_criteria_name_1" name="sub_criteria_name[]" required>
                <label for="points_1">Points:</label>
                <input type="number" id="points_1" name="points[]" required>
            </div> -->
        </div>
        <button type="submit">Add Category</button>
        <button type="button" onclick="document.getElementById('addCriteriaModal').style.display='none'">Cancel</button>
    </form>
</div>

<!-- Add Participant Modal -->
<div id="addParticipantModal" class="modal">
    <form class="modal-content" method="post">
        <input type="hidden" name="action" value="add_participant">
        <h2>Add Participant</h2>
        <label for="participant_number">Participant Number:</label>
        <input type="number" id="participant_number" name="participant_number" required><br>
        <label for="participant_name">Participant Name:</label>
        <input type="text" id="participant_name" name="participant_name" required><br>
        <label for="participant_gender">Gender:</label>
        <input type="text" id="participant_gender" name="participant_gender"><br>
        <button type="submit">Add Participant</button>
        <button type="button" onclick="document.getElementById('addParticipantModal').style.display='none'">Cancel</button>
    </form>
</div>
<script>
    function redirectToJudgeScores() {
        window.location.href = 'display_judges_scores.php?event_id=<?php echo $event_id; ?>';
    }
</script>
<script>
var modal = document.getElementById("judgeModal");
var span = document.getElementsByClassName("close")[0];
var form = document.getElementById("judgeForm");

span.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function editJudge(judge_id, name) {
    modal.style.display = "block";
    document.getElementById("judge_id").value = judge_id;
    document.getElementById("name").value = name;
    document.getElementById("action").value = "edit";
    document.getElementById("password").style.display = "block";
    document.getElementById("passwordLabel").style.display = "block";
    document.getElementById("modalTitle").innerText = "Edit Judge for Event: <?php echo htmlspecialchars($event_name); ?>";
}

function deleteJudge(judge_id) {
    if (confirm("Are you sure you want to delete this judge?")) {
        document.getElementById("judge_id").value = judge_id;
        document.getElementById("action").value = "delete";
        form.submit();
    }
}

form.onsubmit = function(event) {
    event.preventDefault();
    var action = document.getElementById("action").value;

    if (action === "edit" && document.getElementById("password").value === "") {
        document.getElementById("password").disabled = true;
    } else {
        document.getElementById("password").disabled = false;
    }

    form.submit();
}

</script>

</body>
</html>