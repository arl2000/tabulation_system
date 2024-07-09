<?php
session_start();
include 'db_connection.php';


if (!isset($_SESSION['judge_id'])) {
    header("Location: judge_login.php");
    exit;
}

$judge_id = $_SESSION['judge_id'];

// Fetch judge details
$judge_query = "SELECT * FROM judges WHERE judge_id = $judge_id";
$judge_result = mysqli_query($connect, $judge_query);
$judge = mysqli_fetch_assoc($judge_result);
$judge_name = $judge['name'];

$event_id = $judge['event_id'];

// Fetch event details
$event_query = "SELECT * FROM events WHERE event_id = $event_id";
$event_result = mysqli_query($connect, $event_query);
$event = mysqli_fetch_assoc($event_result);
$event_name = $event['name'];
$criteria = $event['criteria'];

// Fetch criteria
$criteria_query = "SELECT * FROM criteria WHERE event_id = $event_id";
$criteria_result = mysqli_query($connect, $criteria_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Judge Dashboard</title>
<!-- Include jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
// Function to toggle table visibility
var currentTable = null;

function toggleTable(criteriaId) {
    if (currentTable !== null && currentTable !== criteriaId) {
        $("#table_" + currentTable).hide();
    }
    $("#table_" + criteriaId).toggle();
    currentTable = (currentTable === criteriaId) ? null : criteriaId;
}

function saveScores(criteriaId, isAutoUpdate = false) {
    var scores = {};
    var exceededFields = []; // Array to store exceeded fields

    $("#table_" + criteriaId + " input").each(function() {
        var participantId = $(this).data('participant');
        var subCriteriaId = $(this).data('subcriteria');
        var fieldValue = $(this).val().trim(); // Trim to remove any extra whitespace
        if (fieldValue === "") return; // Skip empty fields

        fieldValue = parseFloat(fieldValue); // Convert value to float for comparison
        var maxPoints = parseFloat($(this).attr('max')); // Get maximum allowed score

        if (!scores[participantId]) {
            scores[participantId] = {};
        }
        scores[participantId][subCriteriaId] = fieldValue;

        // Check if the entered score exceeds the maximum allowed score
        if (fieldValue > maxPoints) {
            exceededFields.push($(this)); // Store the input field for showing the alert
        }
    });

    if (exceededFields.length > 0) {
        exceededFields.forEach(function(field) {
            alert("The score entered exceeds the maximum allowed score for this sub-criteria!");
        });
        return; // Exit the function without submitting the form
    }

    $.post("submit_score.php", { criteria_id: criteriaId, scores: scores }, function(response) {
        if (!isAutoUpdate) {
            alert("Scores saved successfully!");
        }
    }).fail(function(jqXHR) {
        if (jqXHR.status === 400) {
            var response = JSON.parse(jqXHR.responseText);
            alert("Failed to save scores: " + response.errors.join(", "));
        } else {
            alert("Failed to save scores.");
        }
    });
}

// Ensure the auto-save setup is working as expected
function setupAutoSave() {
    var timeout = null;
    $('input').on('input', function() {
        var criteriaId = $(this).closest('div').attr('id').split('_')[1];
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            saveScores(criteriaId, true);
        }, 5000); // 5 seconds
    });
}

function hideEmptyTables() {
    $('div[id^="table_"]').each(function() {
        var tableId = $(this).attr('id');
        $(this).find('table').each(function() {
            if ($(this).find('tr').length <= 1) {
                $(this).prev('h5').hide(); // Hide the label
                $(this).hide();
            }
        });

        // Hide the entire criteria section if all tables are empty
        if ($(this).find('table:visible').length === 0) {
            $(this).hide();
        }
    });
}

$(document).ready(function() {
    setupAutoSave();
    hideEmptyTables();
});

</script>

<style>
/* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f7f7f7;
}

.container {
    max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2, h3, h4, h5 {
    color: #333;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

h3, h4, h5 {
    margin-bottom: 10px;
}

.criteria-buttons {
    text-align: center;
    margin-bottom: 20px;
}

.criteria-button {
    display: inline-block;
    cursor: pointer;
    padding: 5px 10px;
    background-color: #007bff;
    color: #fff;
    border-radius: 5px;
    margin-right: 10px;
    transition: background-color 0.3s;
}

.criteria-button:hover {
    background-color: #0056b3;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th, table td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: center;
}

table th {
    background-color: #f2f2f2;
}

/* Button Styles */
button {
    padding: 8px 16px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #0056b3;
}

</style>
</head>
<body>

<div class="container">
    
<h3>Welcome Judge: <?php echo $judge_name; ?></h3>

<h2>Event: <?php echo $event_name; ?></h2>

<div class="criteria-buttons">
    <h1>Categories:</h1>
    <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
        <h4 class="criteria-button" onclick="toggleTable('<?php echo $criteria['criteria_id']; ?>')">
           <?php echo $criteria['name']; ?>
        </h4>
    <?php endwhile; ?>
</div>

<?php mysqli_data_seek($criteria_result, 0); // Reset result pointer ?>
<?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
    <div id="table_<?php echo $criteria['criteria_id']; ?>" style="display: none;">
        <h4><?php echo $criteria['name']; ?></h4>
        
        <!-- Male Participants Table -->
        <h5>Male Participants</h5>
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
            // Fetch male participants for the event
            $male_participants_query = "SELECT * FROM participants WHERE event_id = $event_id AND gender = 'male'";
            $male_participants_result = mysqli_query($connect, $male_participants_query);
            while ($participant = mysqli_fetch_assoc($male_participants_result)):
                // Fetch participant's sub-criteria scores for this criterion
                $criteria_id = $criteria['criteria_id'];
                $participant_scores = [];
                foreach ($sub_criteria_ids as $sub_criteria_id) {
                    $score_query = "SELECT score FROM scores WHERE criteria_id = $criteria_id AND participant_id = {$participant['participant_id']} AND sub_criteria_id = $sub_criteria_id AND judge_id = $judge_id";
                    $score_result = mysqli_query($connect, $score_query);
                    $score = mysqli_fetch_assoc($score_result);
                    $participant_scores[$sub_criteria_id] = $score ? $score['score'] : '';
                }
            ?>
                <tr>
                    <td><?php echo $participant['number']; ?></td>
                    <td><?php echo $participant['gender']; ?></td>
                    <?php foreach ($participant_scores as $sub_criteria_id => $score): ?>
                        <td>
                            <input type="number" data-participant="<?php echo $participant['participant_id']; ?>" data-subcriteria="<?php echo $sub_criteria_id; ?>" value="<?php echo $score; ?>" max="<?php echo $sub_criteria['points']; ?>">
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        </table>
        
        <!-- Female Participants Table -->
        <h5>Female Participants</h5>
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
            // Fetch female participants for the event
            $female_participants_query = "SELECT * FROM participants WHERE event_id = $event_id AND gender = 'female'";
            $female_participants_result = mysqli_query($connect, $female_participants_query);
            while ($participant = mysqli_fetch_assoc($female_participants_result)):
                // Fetch participant's sub-criteria scores for this criterion
                $criteria_id = $criteria['criteria_id'];
                $participant_scores = [];
                foreach ($sub_criteria_ids as $sub_criteria_id) {
                    $score_query = "SELECT score FROM scores WHERE criteria_id = $criteria_id AND participant_id = {$participant['participant_id']} AND sub_criteria_id = $sub_criteria_id AND judge_id = $judge_id";
                    $score_result = mysqli_query($connect, $score_query);
                    $score = mysqli_fetch_assoc($score_result);
                    $participant_scores[$sub_criteria_id] = $score ? $score['score'] : '';
                }
            ?>
                <tr>
                    <td><?php echo $participant['number']; ?></td>
                    <td><?php echo $participant['gender']; ?></td>
                    <?php foreach ($participant_scores as $sub_criteria_id => $score): ?>
                        <td>
                            <input type="number" data-participant="<?php echo $participant['participant_id']; ?>" data-subcriteria="<?php echo $sub_criteria_id; ?>" value="<?php echo $score; ?>" max="<?php echo $sub_criteria['points']; ?>">
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Mixed Participants Table -->
        <h5>Mixed Participants</h5>
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
            // Fetch participants with no specified gender for the event
            $mixed_participants_query = "SELECT * FROM participants WHERE event_id = $event_id AND (gender IS NULL OR gender = '')";
            $mixed_participants_result = mysqli_query($connect, $mixed_participants_query);
            while ($participant = mysqli_fetch_assoc($mixed_participants_result)):
                // Fetch participant's sub-criteria scores for this criterion
                $criteria_id = $criteria['criteria_id'];
                $participant_scores = [];
                foreach ($sub_criteria_ids as $sub_criteria_id) {
                    $score_query = "SELECT score FROM scores WHERE criteria_id = $criteria_id AND participant_id = {$participant['participant_id']} AND sub_criteria_id = $sub_criteria_id AND judge_id = $judge_id";
                    $score_result = mysqli_query($connect, $score_query);
                    $score = mysqli_fetch_assoc($score_result);
                    $participant_scores[$sub_criteria_id] = $score ? $score['score'] : '';
                }
            ?>
                <tr>
                    <td><?php echo $participant['number']; ?></td>
                    <td><?php echo $participant['gender']; ?></td>
                    <?php foreach ($participant_scores as $sub_criteria_id => $score): ?>
                        <td>
                            <input type="number" data-participant="<?php echo $participant['participant_id']; ?>" data-subcriteria="<?php echo $sub_criteria_id; ?>" value="<?php echo $score; ?>" max="<?php echo $sub_criteria['points']; ?>">
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        </table>

        <button onclick="saveScores('<?php echo $criteria['criteria_id']; ?>')">Save Scores</button>
    </div>
<?php endwhile; ?>

</div>

</body>
</html>
