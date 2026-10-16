<?php
include "db_connect.php";

$sql = "SELECT 
            a.attendance_id,
            a.reg_no,
            s.student_name,
            a.internship_id,
            i.company_name,
            a.attendance_date,
            a.status
        FROM attendance a
        LEFT JOIN student s
            ON a.reg_no = s.reg_no
        LEFT JOIN internships i
            ON a.internship_id = i.internship_id
        ORDER BY a.attendance_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Attendance</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .attendance-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .attendance-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            overflow-x: auto;
        }

        .attendance-title {
            text-align: center;
            color: #4b3a2f;
            margin-bottom: 30px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        .attendance-table th {
            background: #4b3a2f;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .attendance-table td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
        }

        .attendance-table tr:hover {
            background: #f8f3eb;
        }

        .present {
            background: #d6b56d;
            color: #3b2b20;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
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

    </style>

</head>

<body>

<div class="attendance-container">

    <div class="attendance-card">

        <h1 class="attendance-title">
            Student Attendance
        </h1>

        <?php if ($result && mysqli_num_rows($result) > 0) { ?>

            <table class="attendance-table">

                <thead>

                    <tr>

                        <th>Attendance ID</th>

                        <th>Register Number</th>

                        <th>Student Name</th>

                        <th>Internship ID</th>

                        <th>Company</th>

                        <th>Date</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['attendance_id']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['reg_no']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['student_name'] ?? 'N/A'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['internship_id']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['company_name'] ?? 'N/A'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['attendance_date']
                            );
                            ?>
                        </td>

                        <td>
                            <span class="present">
                                <?php
                                echo htmlspecialchars(
                                    $row['status']
                                );
                                ?>
                            </span>
                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div class="no-data">

                <h3>No Attendance Records Found</h3>

                <p>
                    Students have not marked attendance yet.
                </p>

            </div>

        <?php } ?>


        <a
            href="admin_dashboard.php"
            class="back-btn"
        >
            ← Back to Admin Dashboard
        </a>

    </div>

</div>

</body>

</html>