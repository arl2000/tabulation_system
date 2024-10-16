<?php
session_start();
include 'db_connection.php';

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

// Fetch all judges
$judges_query = "SELECT * FROM judges";
$judges_result = mysqli_query($connect, $judges_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Scores</title>
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

    .container {
        max-width: 90%;
        margin: 30px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1, h2, h3, h4 {
        color: #333;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
    }

    h2, h3 {
        margin-bottom: 15px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        background-color: #fafafa;
    }

    table th, table td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }

    table th {
        background-color: #f2f2f2;
        color: #555;
    }

    table td input {
        width: 60px;
        padding: 5px;
        text-align: center;
    }

    .criteria-section {
        margin-bottom: 40px;
    }

    .judge-label {
        margin-bottom: 10px;
        color: #007bff;
        font-weight: bold;
    }
    .active{
            background-color: #45a049;
        }
</style>
</head>
<body>

<nav class="navbar">
    <ul>
        <li><a href="display_judges_scores.php?event_id=<?php echo $event_id; ?>">Back to Judges Scores</a></li>
        <li><a href="event_details.php?event_id=<?php echo $event_id; ?>">Show Overall Ranking</a></li>
        <li><a href="minor_awards.php?event_id=<?php echo $event_id; ?>">Show Minor Awards</a></li>
        <li class="active"><a href="judges_individual_scores.php?event_id=<?php echo $event_id; ?>">Show Individual Scores</a></li>
        <li><a href="admin_dashboard.php?event_id=<?php echo $event_id; ?>">Home</a></li>
    </ul>
</nav>

<div class="container">
    <h1>Event: <?php echo $event_name; ?></h1>

    <!-- Loop through all judges -->
    <?php while ($judge = mysqli_fetch_assoc($judges_result)): ?>
    <div class="judge-section">
        <h2>Judge: <?php echo $judge['name']; ?></h2>
        
        <?php
        $judge_id = $judge['judge_id'];
        
        // Fetch criteria for the event
        $criteria_query = "SELECT * FROM criteria WHERE event_id = $event_id";
        $criteria_result = mysqli_query($connect, $criteria_query);
        ?>
        
        <!-- Loop through all criteria -->
        <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
        <div class="criteria-section">
            <h3>Criteria: <?php echo $criteria['name']; ?></h3>

            <!-- Male Participants Table -->
            <?php
            // Fetch male participants for the event
            $male_participants_query = "SELECT * FROM participants WHERE event_id = $event_id AND gender = 'male'";
            $male_participants_result = mysqli_query($connect, $male_participants_query);

            if (mysqli_num_rows($male_participants_result) > 0):
            ?>
            <h4>Male Participants</h4>
            <table>
                <tr>
                    <th>Participant Number</th>
                    <th>Gender</th>
                    <?php
                    // Fetch sub-criteria for this criterion
                    $sub_criteria_query = "SELECT * FROM sub_criteria WHERE criteria_id = {$criteria['criteria_id']}";
                    $sub_criteria_result = mysqli_query($connect, $sub_criteria_query);
                    $sub_criteria_ids = [];
                    while ($sub_criteria = mysqli_fetch_assoc($sub_criteria_result)): 
                        $sub_criteria_ids[] = $sub_criteria['sub_criteria_id'];
                    ?>
                        <th><?php echo $sub_criteria['sub_criteria_name']; ?> (Max: <?php echo $sub_criteria['points']; ?>)</th>
                    <?php endwhile; ?>
                </tr>
                <?php
                while ($participant = mysqli_fetch_assoc($male_participants_result)):
                    $participant_scores = [];
                    foreach ($sub_criteria_ids as $sub_criteria_id) {
                        // Fetch participant's sub-criteria scores for this criterion and judge
                        $score_query = "SELECT score FROM scores WHERE criteria_id = {$criteria['criteria_id']} AND participant_id = {$participant['participant_id']} AND sub_criteria_id = $sub_criteria_id AND judge_id = $judge_id";
                        $score_result = mysqli_query($connect, $score_query);
                        $score = mysqli_fetch_assoc($score_result);
                        $participant_scores[$sub_criteria_id] = $score ? $score['score'] : '-';
                    }
                ?>
                <tr>
                    <td><?php echo $participant['number']; ?></td>
                    <td><?php echo $participant['gender']; ?></td>
                    <?php foreach ($participant_scores as $score): ?>
                        <td><?php echo $score; ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>

            <!-- Female Participants Table -->
            <?php
            // Fetch female participants for the event
            $female_participants_query = "SELECT * FROM participants WHERE event_id = $event_id AND gender = 'female'";
            $female_participants_result = mysqli_query($connect, $female_participants_query);

            if (mysqli_num_rows($female_participants_result) > 0):
            ?>
            <h4>Female Participants</h4>
            <table>
                <tr>
                    <th>Participant Number</th>
                    <th>Gender</th>
                    <?php
                    mysqli_data_seek($sub_criteria_result, 0); // Reset result pointer for sub-criteria
                    while ($sub_criteria = mysqli_fetch_assoc($sub_criteria_result)):
                    ?>
                        <th><?php echo $sub_criteria['sub_criteria_name']; ?> (Max: <?php echo $sub_criteria['points']; ?>)</th>
                    <?php endwhile; ?>
                </tr>
                <?php
                while ($participant = mysqli_fetch_assoc($female_participants_result)):
                    $participant_scores = [];
                    foreach ($sub_criteria_ids as $sub_criteria_id) {
                        // Fetch participant's sub-criteria scores for this criterion and judge
                        $score_query = "SELECT score FROM scores WHERE criteria_id = {$criteria['criteria_id']} AND participant_id = {$participant['participant_id']} AND sub_criteria_id = $sub_criteria_id AND judge_id = $judge_id";
                        $score_result = mysqli_query($connect, $score_query);
                        $score = mysqli_fetch_assoc($score_result);
                        $participant_scores[$sub_criteria_id] = $score ? $score['score'] : '-';
                    }
                ?>
                <tr>
                    <td><?php echo $participant['number']; ?></td>
                    <td><?php echo $participant['gender']; ?></td>
                    <?php foreach ($participant_scores as $score): ?>
                        <td><?php echo $score; ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>

            <!-- Add similar blocks for Mixed Participants if needed -->
        </div>
        <?php endwhile; // End criteria loop ?>
    </div>
    <?php endwhile; // End judges loop ?>
    <button onclick="printPage()">Print</button>
</div>

<script>
        // Function to print the page
        function printPage() {
            window.print(); // Call the window.print() method
        }
    </script>

</body>
</html>
