<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];

$internship_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($internship_id <= 0) {
    die("Invalid Internship.");
}

$sql = "SELECT * FROM internships WHERE internship_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $internship_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$internship = mysqli_fetch_assoc($result);

if (!$internship) {
    die("Internship not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $check_sql = "SELECT * FROM internship_applications
                  WHERE reg_no = ? AND internship_id = ?";

    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $reg_no, $internship_id);
    mysqli_stmt_execute($check_stmt);

    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {

        $message = "You have already applied for this internship.";

    } else {

        $insert_sql = "INSERT INTO internship_applications
                       (reg_no, internship_id, status)
                       VALUES (?, ?, 'Pending')";

        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param(
            $insert_stmt,
            "si",
            $reg_no,
            $internship_id
        );

        if (mysqli_stmt_execute($insert_stmt)) {
            $message = "Application submitted successfully!";
        } else {
            $message = "Application failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Apply Internship</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .apply-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
        }

        .apply-card {
            background: #fff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .apply-title {
            text-align: center;
            color: #4b3a2f;
            margin-bottom: 30px;
        }

        .info-box {
            background: #f8f3eb;
            padding: 18px;
            margin-bottom: 15px;
            border-left: 4px solid #6b5142;
            border-radius: 8px;
        }

        .info-box strong {
            color: #5a4438;
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            margin-top: 20px;
            background: #4b3a2f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #6b5142;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            color: #4b3a2f;
            font-weight: bold;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            color: #4b3a2f;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="apply-container">

    <div class="apply-card">

        <h1 class="apply-title">Apply for Internship</h1>

        <?php if (isset($message)) { ?>

            <p class="message">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php } ?>

        <div class="info-box">
            <strong>Company Name:</strong>
            <?php echo htmlspecialchars($internship['company_name']); ?>
        </div>

        <div class="info-box">
            <strong>Internship Domain:</strong>
            <?php echo htmlspecialchars($internship['internship_domain']); ?>
        </div>

        <div class="info-box">
            <strong>Description:</strong>
            <?php echo htmlspecialchars($internship['description']); ?>
        </div>

        <div class="info-box">
            <strong>Start Date:</strong>
            <?php echo htmlspecialchars($internship['start_date']); ?>
        </div>

        <div class="info-box">
            <strong>End Date:</strong>
            <?php echo htmlspecialchars($internship['end_date']); ?>
        </div>

        <div class="info-box">
            <strong>Guide:</strong>
            <?php echo htmlspecialchars($internship['guide_name']); ?>
        </div>

        <form method="POST">

            <button type="submit" class="submit-btn">
                Submit Application
            </button>

        </form>

        <a href="internship.php" class="back-btn">
            ← Back to Internship Details
        </a>

    </div>

</div>

</body>
</html>