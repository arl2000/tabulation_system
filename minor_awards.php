<?php
include 'db_connection.php';
session_start();

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

// Fetch criteria for the event
$criteria_query = "SELECT * FROM criteria WHERE event_id = $event_id";
$criteria_result = mysqli_query($connect, $criteria_query);

function getRankLabel($rank) {
    $suffix = 'th';
    if (!in_array(($rank % 100), [11, 12, 13])) {
        switch ($rank % 10) {
            case 1: $suffix = 'st'; break;
            case 2: $suffix = 'nd'; break;
            case 3: $suffix = 'rd'; break;
        }
    }
    return $rank . $suffix;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minor Awards for: <?php echo htmlspecialchars($event_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
            color: #333;
        }

        .criterion-name {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            color: #555;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 40px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .number{
            text-align: center;
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

        .judge {
        margin-top: 150px;
        display: flex;
        flex-direction: row;
        justify-content: center;
        flex-wrap: wrap;
        }

        .signature {
            display: inline-block;
            margin: 20px;
            padding-top: 5px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .signature-label {
            margin-top: 5px;
            font-size: 14px;
            color: #555;
        }

        @media print {
            button, .printbtn {
                display: none;
            }

            body {
                padding-top: 5px;
            }
            title{
                margin-right: 100px;
            }

            @page {
                margin: 2cm;
            }

            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <h2><?php echo htmlspecialchars($event_name); ?> Minor Awards</h2>

    <?php
    while ($criteria = mysqli_fetch_assoc($criteria_result)) {
        $criteria_id = $criteria['criteria_id'];
        $criteria_name = $criteria['name'];

        $male_participants_query = "SELECT participants.number, participants.name, AVG(scores.score) as average_score
        FROM participants
        JOIN scores ON participants.participant_id = scores.participant_id
        WHERE participants.event_id = $event_id AND participants.gender = 'male' AND scores.criteria_id = $criteria_id
        GROUP BY participants.participant_id
        ORDER BY average_score DESC";
        $male_participants_result = mysqli_query($connect, $male_participants_query);

        if (mysqli_num_rows($male_participants_result) > 0) {
            echo "<table>
                <tr>
                    <th colspan='4'>Category: " . htmlspecialchars($criteria_name) . " (Male)</th>
                </tr>
                <tr>
                    <th>Participant Number</th>
                    <th>Participant Name</th>
                    <th>Average Score</th>
                    <th>Rank</th>
                </tr>";
            $rank = 1;
            while ($row = mysqli_fetch_assoc($male_participants_result)) {
                $rank_label = getRankLabel($rank);
                echo "<tr>
                    <td class='number'>" . htmlspecialchars($row['number']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . number_format($row['average_score'], 2) . "</td>
                    <td>" . $rank_label . "</td>
                </tr>";
                $rank++;
            }
            echo "</table>";
        }

        $female_participants_query = "SELECT participants.number, participants.name, AVG(scores.score) as average_score
        FROM participants
        JOIN scores ON participants.participant_id = scores.participant_id
        WHERE participants.event_id = $event_id AND participants.gender = 'female' AND scores.criteria_id = $criteria_id
        GROUP BY participants.participant_id
        ORDER BY average_score DESC";
        $female_participants_result = mysqli_query($connect, $female_participants_query);

        if (mysqli_num_rows($female_participants_result) > 0) {
            echo "<table>
                <tr>
                    <th colspan='4'>Category: " . htmlspecialchars($criteria_name) . " (Female)</th>
                </tr>
                <tr>
                    <th>Participant Number</th>
                    <th>Participant Name</th>
                    <th>Average Score</th>
                    <th>Rank</th>
                </tr>";
            $rank = 1;
            while ($row = mysqli_fetch_assoc($female_participants_result)) {
                $rank_label = getRankLabel($rank);
                echo "<tr>
                    <td class='number'>" . htmlspecialchars($row['number']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . number_format($row['average_score'], 2) . "</td>
                    <td>" . $rank_label . "</td>
                </tr>";
                $rank++;
            }
            echo "</table>";
        }

        $mixed_participants_query = "SELECT participants.number, participants.name, AVG(scores.score) as average_score
        FROM participants
        JOIN scores ON participants.participant_id = scores.participant_id
        WHERE participants.event_id = $event_id AND (participants.gender IS NULL OR participants.gender = '') AND scores.criteria_id = $criteria_id
        GROUP BY participants.participant_id
        ORDER BY average_score DESC";
        $mixed_participants_result = mysqli_query($connect, $mixed_participants_query);

        if (mysqli_num_rows($mixed_participants_result) > 0) {
            echo "<div class='criterion-name'>Category: " . htmlspecialchars($criteria_name) . " (Mixed Gender)</div>";
            echo "<table>
                <tr>
                    <th>Participant Number</th>
                    <th>Participant Name</th>
                    <th>Average Score</th>
                    <th>Rank</th>
                </tr>";
            $rank = 1;
            while ($row = mysqli_fetch_assoc($mixed_participants_result)) {
                $rank_label = getRankLabel($rank);
                echo "<tr>
                    <td class='number'>" . htmlspecialchars($row['number']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . number_format($row['average_score'], 2) . "</td>
                    <td>" . $rank_label . "</td>
                </tr>";
                $rank++;
            }
            echo "</table>";
        }
    }

    $judges_query = "SELECT * FROM judges WHERE event_id = $event_id";
    $judges_result = mysqli_query($connect, $judges_query);
    ?>

    <?php if (mysqli_num_rows($judges_result) > 0): ?>
        <div class="judge">
            <?php while ($judge = mysqli_fetch_assoc($judges_result)): ?>
                <div class="signature">
                    <?php echo htmlspecialchars($judge['name']); ?>
                    <div class="signature-label">Judge Signature</div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>


    <button onclick="window.location.href='display_judges_scores.php?event_id=<?php echo $event_id; ?>'">Back to Judges Scores</button>

    <button class="printbtn" onclick="printContent()">Print</button>

    <script>
        function printContent() {
            var originalBody = document.body.innerHTML;
            window.print();
            document.body.innerHTML = originalBody;
        }
    </script>
</body>
</html>
