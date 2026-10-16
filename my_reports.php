<?php
session_start();
include "db_connect.php";

/* Student login check */
if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];

/* Get student's submitted reports */
$sql = "SELECT
            report_id,
            report_title,
            report_file,
            submission_date,
            status
        FROM internship_reports
        WHERE reg_no = ?
        ORDER BY report_id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $reg_no
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Reports</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .report-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .report-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            overflow-x: auto;
        }

        .report-title {
            text-align: center;
            color: #4b3a2f;
            margin-bottom: 30px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .report-table th {
            background: #4b3a2f;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .report-table td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
        }

        .report-table tr:hover {
            background: #f8f3eb;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 20px;
            font-weight: bold;
        }

        .pending {
            background: #f3d98b;
            color: #5a4438;
        }

        .approved {
            background: #c8dfc4;
            color: #285c2a;
        }

        .rejected {
            background: #e8b8b8;
            color: #7a0000;
        }

        .view-btn {
            display: inline-block;
            padding: 8px 15px;
            background: #6b5142;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .view-btn:hover {
            background: #4b3a2f;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            background: #4b3a2f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .back-btn:hover {
            background: #6b5142;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
        }

    </style>

</head>

<body>

<div class="report-container">

    <div class="report-card">

        <h1 class="report-title">
            My Reports
        </h1>

        <?php if ($result && mysqli_num_rows($result) > 0) { ?>

            <table class="report-table">

                <thead>

                    <tr>

                        <th>Report Title</th>

                        <th>Submission Date</th>

                        <th>Status</th>

                        <th>Report</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['report_title']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['submission_date']
                            );
                            ?>
                        </td>

                        <td>

                            <?php
                            $status = $row['status'];

                            if ($status == "Approved") {
                            ?>

                                <span class="status-badge approved">
                                    Approved
                                </span>

                            <?php
                            } elseif ($status == "Rejected") {
                            ?>

                                <span class="status-badge rejected">
                                    Rejected
                                </span>

                            <?php
                            } else {
                            ?>

                                <span class="status-badge pending">
                                    Pending
                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <a
                                href="uploads/<?php echo rawurlencode($row['report_file']); ?>"
                                target="_blank"
                                class="view-btn"
                            >
                                View
                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div class="no-data">

                <h3>No Reports Submitted</h3>

                <p>
                    You have not submitted any internship reports yet.
                </p>

            </div>

        <?php } ?>


        <a
            href="dashboard.php"
            class="back-btn"
        >
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>

</html>