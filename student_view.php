<?php
include "db_connect.php";

if (!isset($_GET['reg_no'])) {
    die("Student not found.");
}

$reg_no = $_GET['reg_no'];

$sql = "SELECT * FROM student WHERE reg_no = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $reg_no);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Student Details</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .student-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
        }

        .student-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .student-title {
            text-align: center;
            color: #4b3a2f;
            margin-bottom: 30px;
        }

        .detail-box {
            background: #f8f3eb;
            padding: 18px;
            margin-bottom: 15px;
            border-left: 4px solid #6b5142;
            border-radius: 8px;
        }

        .detail-box strong {
            display: block;
            color: #5a4438;
            margin-bottom: 6px;
        }

        .detail-box span {
            color: #333;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 22px;
            background: #4b3a2f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

    </style>

</head>

<body>

<div class="student-container">

    <div class="student-card">

        <h1 class="student-title">
            Student Details
        </h1>

        <div class="detail-box">
            <strong>Register Number</strong>
            <span>
                <?php echo htmlspecialchars($student['reg_no']); ?>
            </span>
        </div>

        <div class="detail-box">
            <strong>Student Name</strong>
            <span>
                <?php echo htmlspecialchars($student['student_name']); ?>
            </span>
        </div>

        <div class="detail-box">
            <strong>Department</strong>
            <span>
                <?php echo htmlspecialchars($student['department']); ?>
            </span>
        </div>

        <div class="detail-box">
            <strong>Year of Study</strong>
            <span>
                <?php echo htmlspecialchars($student['year_of_study']); ?>
            </span>
        </div>

        <div class="detail-box">
            <strong>Email</strong>
            <span>
                <?php echo htmlspecialchars($student['email']); ?>
            </span>
        </div>

        <a href="manage_students.php" class="back-btn">
            ← Back to Manage Students
        </a>

    </div>

</div>

</body>

</html>