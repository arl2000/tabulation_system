<?php
include 'db_connection.php';

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
                echo "window.location.href = 'judges.php?event_id=$event_id';";
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
                echo "window.location.href = 'judges.php?event_id=$event_id';";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Judges</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
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

<!-- Place this script at the end of the HTML, just before the closing </body> tag -->
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
