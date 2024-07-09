<?php
include 'db_connection.php';
session_start();

// Check if event_id is set
if (!isset($_GET['event_id'])) {
    echo "Event ID is not set.";
    exit;
}

$event_id = $_GET['event_id'];

// Fetch event details
$event_query = "SELECT * FROM events WHERE event_id = $event_id";
$event_result = mysqli_query($connect, $event_query);
$event = mysqli_fetch_assoc($event_result);
$event_name = $event['name'];
$current_criteria = $event['criteria'];

// Fetch criteria for the event
$criteria_query = "SELECT * FROM criteria WHERE event_id = $event_id";
$criteria_result = mysqli_query($connect, $criteria_query);

// Fetch judges for the event
$judges_query = "SELECT * FROM judges WHERE event_id = $event_id";
$judges_result = mysqli_query($connect, $judges_query);

// Fetch male participants for the event
$male_participants_query = "SELECT * FROM participants WHERE event_id = $event_id AND gender = 'male'";
$male_participants_result = mysqli_query($connect, $male_participants_query);

// Fetch female participants for the event
$female_participants_query = "SELECT * FROM participants WHERE event_id = $event_id AND gender = 'female'";
$female_participants_result = mysqli_query($connect, $female_participants_query);

// Handle the criteria submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criteria'])) {
    $criteria = mysqli_real_escape_string($connect, $_POST['criteria']);
    $update_query = "UPDATE events SET criteria = '$criteria' WHERE event_id = $event_id";
    if (mysqli_query($connect, $update_query)) {
        // Refresh the event details
        $event_result = mysqli_query($connect, $event_query);
        $event = mysqli_fetch_assoc($event_result);
        $current_criteria = $event['criteria'];
    } else {
        echo "Error updating criteria: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judges Scores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        ul {
            list-style-type: none;
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        ul li {
            display: inline;
            margin: 0 10px;
        }

        ul li a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            transition: color 0.3s;
        }

        ul li a:hover {
            color: #d4e6f1;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
            color: #333;
        }

        h3 {
            margin-top: 20px;
            color: #333;
            text-align: center;
        }

        .judge-name {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            color: #555;
            margin-top: 100px;
        }

        table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        form {
            display: block;
            text-align: center;
            align-items: center;
            justify-content: center;
        }

        textarea {
            width: 80%;
            height: fit-content;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fff;
            resize: none;
            overflow: hidden;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #333;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
            display: none;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        input[type="submit"]:active {
            background-color: #004494;
        }

        .judge-section {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <a href="admin_dashboard.php?event_id=<?php echo $event_id; ?>">Home</a>

    <h2>Judges Scores for Event: <?php echo htmlspecialchars($event_name); ?></h2>

    <?php while ($judge = mysqli_fetch_assoc($judges_result)): ?>
        <div class="judge-name">Judge: <?php echo htmlspecialchars($judge['name']); ?></div>

        <?php if (mysqli_num_rows($male_participants_result) > 0): ?>
            <h3>Male Participants</h3>
            <table>
                <tr>
                    <th>Participant Number</th>
                    <?php mysqli_data_seek($criteria_result, 0); // Reset criteria result pointer ?>
                    <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
                        <th><?php echo htmlspecialchars($criteria['name']); ?></th>
                    <?php endwhile; ?>
                </tr>
                <?php mysqli_data_seek($male_participants_result, 0); // Reset male participants result pointer ?>
                <?php while ($male_participant = mysqli_fetch_assoc($male_participants_result)): ?>
                    <?php $hasScores = false; ?>
                    <?php mysqli_data_seek($criteria_result, 0); // Reset criteria result pointer ?>
                    <tr>
                        <td><?php echo htmlspecialchars($male_participant['number']); ?></td>
                        <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
                            <?php
                            $criteria_id = $criteria['criteria_id'];
                            $participant_id = $male_participant['participant_id'];
                            $judge_id = $judge['judge_id'];
                            $score_query = "SELECT scores.score, participants.gender
                            FROM scores
                            JOIN participants ON scores.participant_id = participants.participant_id
                            WHERE scores.criteria_id = $criteria_id
                              AND scores.participant_id = $participant_id
                              AND scores.judge_id = $judge_id
                            ";
                            $score_result = mysqli_query($connect, $score_query);
                            $score = mysqli_fetch_assoc($score_result);
                            ?>
                            <td><?php echo $score ? number_format($score['score'], 2) : "N/A"; ?></td>
                            <?php if ($score !== null) $hasScores = true; ?>
                        <?php endwhile; ?>
                    </tr>
                    <?php if (!$hasScores) continue; ?>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>

        <?php if (mysqli_num_rows($female_participants_result) > 0): ?>
            <h3>Female Participants</h3>
            <table>
                <tr>
                    <th>Participant Number</th>
                    <?php mysqli_data_seek($criteria_result, 0); // Reset criteria result pointer ?>
                    <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
                        <th><?php echo htmlspecialchars($criteria['name']); ?></th>
                    <?php endwhile; ?>
                </tr>
                <?php mysqli_data_seek($female_participants_result, 0); // Reset female participants result pointer ?>
                <?php while ($female_participant = mysqli_fetch_assoc($female_participants_result)): ?>
                    <?php $hasScores = false; ?>
                    <?php mysqli_data_seek($criteria_result, 0); // Reset criteria result pointer ?>
                    <tr>
                        <td><?php echo htmlspecialchars($female_participant['number']); ?></td>
                        <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
                            <?php
                            $criteria_id = $criteria['criteria_id'];
                            $participant_id = $female_participant['participant_id'];
                            $judge_id = $judge['judge_id'];
                            $score_query = "SELECT score FROM scores WHERE criteria_id = $criteria_id AND participant_id = $participant_id AND judge_id = $judge_id";
                            $score_result = mysqli_query($connect, $score_query);
                            $score = mysqli_fetch_assoc($score_result);
                            ?>
                            <td><?php echo $score ? number_format($score['score'], 2) : "N/A"; ?></td>
                            <?php if ($score !== null) $hasScores = true; ?>
                        <?php endwhile; ?>
                    </tr>
                    <?php if (!$hasScores) continue; ?>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>

        <?php
        // Fetch mixed-gender participants
        $mixed_participants_query = "SELECT * FROM participants WHERE event_id = $event_id AND (gender IS NULL OR gender = '')";
        $mixed_participants_result = mysqli_query($connect, $mixed_participants_query);
        ?>
        <?php if (mysqli_num_rows($mixed_participants_result) > 0): ?>
            <h3>Participants</h3>
            <table>
                <tr>
                    <th>Participant Number</th>
                    <?php mysqli_data_seek($criteria_result, 0); // Reset criteria result pointer ?>
                    <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
                        <th><?php echo htmlspecialchars($criteria['name']); ?></th>
                    <?php endwhile; ?>
                </tr>
                <?php while ($mixed_participant = mysqli_fetch_assoc($mixed_participants_result)): ?>
                    <?php $hasScores = false; ?>
                    <?php mysqli_data_seek($criteria_result, 0); // Reset criteria result pointer ?>
                    <tr>
                        <td><?php echo htmlspecialchars($mixed_participant['number']); ?></td>
                        <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
                            <?php
                            $criteria_id = $criteria['criteria_id'];
                            $participant_id = $mixed_participant['participant_id'];
                            $judge_id = $judge['judge_id'];
                            $score_query = "SELECT score FROM scores WHERE criteria_id = $criteria_id AND participant_id = $participant_id AND judge_id = $judge_id";
                            $score_result = mysqli_query($connect, $score_query);
                            $score = mysqli_fetch_assoc($score_result);
                            ?>
                            <td><?php echo $score ? number_format($score['score'], 2) : "N/A"; ?></td>
                            <?php if ($score !== null) $hasScores = true; ?>
                        <?php endwhile; ?>
                    </tr>
                    <?php if (!$hasScores) continue; ?>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>

    <?php endwhile; ?>

    <button onclick="window.location.href='event_details.php?event_id=<?php echo $event_id; ?>'">Show Overall Ranking</button>

    <button onclick="window.location.href='minor_awards.php?event_id=<?php echo $event_id; ?>'">Show Minor Awards</button>

    <button onclick="printPage()">Print</button>

    <script>
        function showSubmitButton() {
            document.getElementById('submit-button').style.display = 'block';
        }
    </script>

<script>
        // Function to print the page
        function printPage() {
            window.print(); // Call the window.print() method
        }
    </script>
</body>
</html>
