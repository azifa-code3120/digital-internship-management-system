<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}

/* Get latest active meeting */
$sql = "
    SELECT meeting_id, internship_id, meeting_link
    FROM online_meetings
    WHERE status = 'Active'
    ORDER BY meeting_id DESC
    LIMIT 1
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("No active meeting available.");
}

$meeting = mysqli_fetch_assoc($result);

$meeting_id = $meeting['meeting_id'];
$internship_id = $meeting['internship_id'];
$meeting_link = $meeting['meeting_link'];


/*
 * Store the internship for which
 * the student has joined the meeting
 */
$_SESSION['meeting_joined'] = true;
$_SESSION['meeting_joined_internship_id'] = $internship_id;
$_SESSION['meeting_joined_meeting_id'] = $meeting_id;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Online Meeting</title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f3ec;
            color: #243447;
            text-align: center;
        }

        .box {
            width: 500px;
            max-width: 90%;
            margin: 100px auto;
            background: #fffdf9;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(36,52,71,0.15);
        }

        h2 {
            margin-bottom: 15px;
            color: #243447;
        }

        p {
            color: #66717c;
            font-size: 14px;
            line-height: 1.6;
        }

        .meeting-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #c6a15b;
            color: #243447;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
        }

        .meeting-btn:hover {
            background: #b28e4f;
        }

        .return-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 11px 23px;
            background: #243447;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .return-btn:hover {
            background: #1b2938;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>Internship Online Meeting</h2>

    <p>
        Your meeting has been opened in a new tab.
        After completing the meeting, return to the student portal.
    </p>

    <a href="<?php echo htmlspecialchars($meeting_link); ?>"
       target="_blank"
       class="meeting-btn">
        Join Google Meet
    </a>

    <br>

    <a href="meeting.php" class="return-btn">
        ← Return to Student Portal
    </a>

</div>

</body>

</html>