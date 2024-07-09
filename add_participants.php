<?php
include 'db_connection.php';

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

// Fetch all participants for the event regardless of gender
$participants_query = "SELECT participant_id, name, number, gender FROM participants WHERE event_id = '$event_id'";
$participants_result = mysqli_query($connect, $participants_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants for <?php echo htmlspecialchars($event_name); ?></title>
    <!-- <style>
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

        .participants-list-container {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 80%;
            max-width: fit-content;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 16px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        td:last-child {
            text-align: center;
        }

        td:hover {
            background-color: #f0f0f0;
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

        .deletebtn {
            padding: 5px 10px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .deletebtn:hover {
            background-color: #c82333;
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
        form input[type="number"],
        form select {
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
    </style> -->
</head>
<body>

<div class="centered-container">
    <div class="participants-list-container">
        <h3>Participants for: <?php echo htmlspecialchars($event_name); ?></h3>
        
        <table>
            <tr>
                <th>Participant Number</th>
                <th>Participant Name</th>
                <th>Gender</th>
                <th>Action</th>
            </tr>
            <?php while ($participant = mysqli_fetch_assoc($participants_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($participant['number']); ?></td>
                    <td><?php echo htmlspecialchars($participant['name']); ?></td>
                    <td><?php echo htmlspecialchars($participant['gender']); ?></td>
                    <td>
                        <button class="deletebtn" data-participant-id="<?php echo $participant['participant_id']; ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <button style="display: none;" id="showModal">Add Participant</button>
</div>
<!-- 
<div id="participantsModal" class="modal">
    <div class="modal-content">
        <h2>Add Participant for: <?php echo htmlspecialchars($event_name); ?></h2>
        <form method="post" action="add_participant.php">
            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
            <label for="number">Participant Number:</label>
            <input type="number" id="number" name="number" required><br>
            <label for="name">Participant Name:</label>
            <input type="text" id="name" name="name" required><br>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select><br>
            <input type="submit" value="Add Participant">
        </form>
    </div>
</div> -->

<script>
    // Get the modal
    var modal = document.getElementById("participantsModal");

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

    // Delete participant functionality
    document.querySelectorAll('.deletebtn').forEach(button => {
        button.addEventListener('click', function() {
            const participantId = this.getAttribute('data-participant-id');
            if (confirm('Are you sure you want to delete this participant?')) {
                fetch(`delete_participant.php?participant_id=${participantId}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    window.location.reload();
                });
            }
        });
    });
</script>

</body>
</html>
