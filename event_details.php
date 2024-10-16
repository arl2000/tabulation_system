<?php
include 'db_connection.php';
session_start();

// Check if event_id is set in the URL
if (!isset($_GET['event_id'])) {
    echo "Event ID is not set.";
    exit;
}

$event_id = $_GET['event_id'];

// Fetch event details
$event_query = "SELECT * FROM events WHERE event_id = $event_id";
$event_result = mysqli_query($connect, $event_query);
$event = mysqli_fetch_assoc($event_result);

// Fetch male participants and their scores, ordered by highest average score
$male_participants_query = "
    SELECT 
        p.participant_id, 
        p.number,
        p.name,
        SUM(s.score) AS total_score, 
        AVG(s.score) AS average_score
    FROM participants p
    LEFT JOIN scores s ON p.participant_id = s.participant_id
    WHERE p.event_id = $event_id AND p.gender = 'male'
    GROUP BY p.participant_id
    ORDER BY average_score DESC";
$male_participants_result = mysqli_query($connect, $male_participants_query);

// Fetch female participants and their scores, ordered by highest average score
$female_participants_query = "
    SELECT 
        p.participant_id, 
        p.number,
        p.name,
        SUM(s.score) AS total_score, 
        AVG(s.score) AS average_score
    FROM participants p
    LEFT JOIN scores s ON p.participant_id = s.participant_id
    WHERE p.event_id = $event_id AND p.gender = 'female'
    GROUP BY p.participant_id
    ORDER BY average_score DESC";
$female_participants_result = mysqli_query($connect, $female_participants_query);

// Fetch mixed gender participants and their scores, ordered by highest average score
$mixed_participants_query = "
    SELECT 
        p.participant_id, 
        p.number,
        p.name,
        SUM(s.score) AS total_score, 
        AVG(s.score) AS average_score
    FROM participants p
    LEFT JOIN scores s ON p.participant_id = s.participant_id
    WHERE p.event_id = $event_id AND (p.gender IS NULL OR p.gender = '')
    GROUP BY p.participant_id
    ORDER BY average_score DESC";
$mixed_participants_result = mysqli_query($connect, $mixed_participants_query);

$judges_query = "SELECT * FROM judges WHERE event_id = $event_id";
    $judges_result = mysqli_query($connect, $judges_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event['name']; ?> Ranking</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
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
    max-width: 800px;
    margin: 50px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2, h3 {
    color: #333;
    margin-bottom: 20px;
}


.event-list h4 {
    margin: 15px 0;
}

.event-list h4 a {
    text-decoration: none;
    color: #333;
    transition: color 0.3s;
}

.event-list h4 a:hover {
    color: #007bff;
}

/* Styles for the event details page */
table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
}

th, td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

th {
    background-color: #f2f2f2;
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
    margin-top: 20px;
}

form input[type="submit"]:hover {
    background-color: #218838;
}

.judge {
        margin-top: 90px;
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
            form, .printbtn {
                display: none;
            }

            body {
                padding-top: 5px;
            }

            /* Specify padding for subsequent pages */
            @page {
                margin-top: 2cm;
                margin-top: 100px; /* Adjust as needed */
            }

            /* Prevent table from being split across pages */
            table {
                page-break-inside: avoid;
            }
        }

    </style>
</head>
<body>

<nav class="navbar">
    <ul>
        <li><a href="display_judges_scores.php?event_id=<?php echo $event_id; ?>">Back to Judges Scores</a></li>
        <li><a href="event_details.php?event_id=<?php echo $event_id; ?>">Show Overall Ranking</a></li>
        <li><a href="minor_awards.php?event_id=<?php echo $event_id; ?>">Show Minor Awards</a></li>
        <li><a href="judges_individual_scores.php?event_id=<?php echo $event_id; ?>">Show Individual Scores</a></li>
        <li><a href="admin_dashboard.php?event_id=<?php echo $event_id; ?>">Home</a></li>
    </ul>
</nav>

    <div class="container">
        <h2><?php echo $event['name']; ?> Ranking</h2>

        <!-- Male Participants Table -->
        <?php if (mysqli_num_rows($male_participants_result) > 0): ?>
        <h3>Male Participants</h3>
        <table>
            <tr>
                <th>Participant Number</th>
                <th>Participant Name</th>
                <th>Total Score</th>
                <th>Average Score</th>
                <th>Rank</th>
            </tr>
            <?php 
            $rank = 1;
            while ($participant = mysqli_fetch_assoc($male_participants_result)): ?>
                <tr>
                    <td><?php echo $participant['number']; ?></td>
                    <td><?php echo $participant['name']; ?></td>
                    <td><?php echo number_format($participant['total_score'], 2); ?></td>
                    <td><?php echo number_format($participant['average_score'], 2); ?></td>
                    <td><?php echo $rank . getRankSuffix($rank); ?></td>
                </tr>
            <?php 
            $rank++; 
            endwhile; ?>
        </table>
        <?php endif; ?>

        <!-- Female Participants Table -->
        <?php if (mysqli_num_rows($female_participants_result) > 0): ?>
        <h3>Female Participants</h3>
        <table>
            <tr>
                <th>Participant Number</th>
                <th>Participant Name</th>
                <th>Total Score</th>
                <th>Average Score</th>
                <th>Rank</th>
            </tr>
            <?php 
            $rank = 1;
            while ($participant = mysqli_fetch_assoc($female_participants_result)): ?>
                <tr>
                    <td><?php echo $participant['number']; ?></td>
                    <td><?php echo $participant['name']; ?></td>
                    <td><?php echo number_format($participant['total_score'], 2); ?></td>
                    <td><?php echo number_format($participant['average_score'], 2); ?></td>
                    <td><?php echo $rank . getRankSuffix($rank); ?></td>
                </tr>
            <?php 
            $rank++; 
            endwhile; ?>
        </table>
        <?php endif; ?>

        <!-- Mixed Gender Participants Table -->
        <?php if (mysqli_num_rows($mixed_participants_result) > 0): ?>
        <h3>Mixed Gender Participants</h3>
        <table>
            <tr>
                <th>Participant Number</th>
                <th>Participant Name</th>
                <th>Total Score</th>
                <th>Average Score</th>
                <th>Rank</th>
            </tr>
            <?php 
            $rank = 1;
            while ($participant = mysqli_fetch_assoc($mixed_participants_result)): ?>
                <tr>
                    <td><?php echo $participant['number']; ?></td>
                    <td><?php echo $participant['name']; ?></td>
                    <td><?php echo number_format($participant['total_score'], 2); ?></td>
                    <td><?php echo number_format($participant['average_score'], 2); ?></td>
                    <td><?php echo $rank . getRankSuffix($rank); ?></td>
                </tr>
            <?php 
            $rank++; 
            endwhile; ?>
        </table>
        <?php endif; ?>


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

        <!-- Button to go back to judges scores page -->
        <form method="get" action="display_judges_scores.php">
            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
            <input type="submit" value="Go Back to Judges Scores">
        </form>
        <br>
        <button class="printbtn" onclick="printContent()">Print</button>

    </div>


    <script>
        // JavaScript function to trigger print dialog
        function printContent() {
            var originalBody = document.body.innerHTML;
            window.print();
            document.body.innerHTML = originalBody;
        }
    </script>
</body>
</html>

<?php
// Function to get the appropriate suffix for a given rank
function getRankSuffix($rank) {
    if ($rank % 100 >= 11 && $rank % 100 <= 13) {
        return 'th';
    } else {
        switch ($rank % 10) {
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
            default: return 'th';
        }
    }
}
?>
