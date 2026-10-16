<?php

session_start();
include "db_connect.php";

/* ==============================
   ADMIN LOGIN CHECK
   ============================== */

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}


/* ==============================
   GET PRESENT STUDENTS
   ============================== */

$sql = "
    SELECT
        s.student_name,
        s.username,
        s.email,
        a.status
    FROM attendance a
    INNER JOIN student s
        ON a.reg_no = s.reg_no
    WHERE LOWER(a.status) = 'present'
    GROUP BY
        s.reg_no,
        s.student_name,
        s.username,
        s.email,
        a.status
    ORDER BY s.student_name ASC
";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Attendance | Admin Portal</title>


    <!-- GOOGLE FONTS -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
          rel="stylesheet">


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            background: #f7f3ec;
            color: #243447;
            font-family: 'Poppins', sans-serif;
        }


        .dashboard-layout {
            min-height: 100vh;
            display: flex;
        }


        /* ==============================
           SIDEBAR
           ============================== */

        .sidebar {
            width: 255px;
            height: 100vh;
            background: #243447;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 0 18px rgba(36,52,71,0.15);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c6a15b #243447;
        }


        .sidebar::-webkit-scrollbar {
            width: 7px;
        }


        .sidebar::-webkit-scrollbar-track {
            background: #243447;
        }


        .sidebar::-webkit-scrollbar-thumb {
            background: #c6a15b;
            border-radius: 10px;
        }


        .sidebar-title {
            padding: 30px 20px 25px;
            text-align: center;
            border-bottom: 1px solid rgba(198,161,91,0.40);
            flex-shrink: 0;
        }


        .sidebar-title h2 {
            margin: 0;
            font-family: 'Cinzel', serif;
            font-size: 21px;
            color: #f3d99b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }


        .sidebar-title p {
            margin: 8px 0 0;
            color: #e5e9ed;
            font-size: 11px;
            letter-spacing: 1px;
        }


        .sidebar-menu {
            padding: 25px 15px;
            flex: 1;
        }


        .menu-title {
            padding: 0 12px 12px;
            color: #d8b66c;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.7px;
            text-transform: uppercase;
        }


        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px 14px;
            margin-bottom: 6px;
            color: #f2f4f6;
            text-decoration: none;
            font-size: 13px;
            border-radius: 4px;
            transition: none;
        }


        .sidebar-menu a:hover {
            background: rgba(198,161,91,.17);
            color: #fff;
            padding-left: 14px;
            transform: none;
        }


        .sidebar-menu a.active {
            background: #c6a15b;
            color: #243447;
            font-weight: 600;
        }


        .menu-icon {
            width: 24px;
            min-width: 24px;
            text-align: center;
            font-size: 16px;
        }


        .sidebar-bottom {
            padding: 18px 15px;
            border-top: 1px solid rgba(198,161,91,.4);
            flex-shrink: 0;
        }


        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px;
            border: 1px solid #d8b66c;
            color: #f3d99b;
            background: transparent;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
        }


        .logout-link:hover {
            background: #c6a15b;
            color: #243447;
        }


        /* ==============================
           MAIN CONTENT
           ============================== */

        .main-content {
            margin-left: 255px;
            width: calc(100% - 255px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }


        .top-header {
            height: 80px;
            background: #fffdf9;
            border-bottom: 1px solid #ddd4c8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            box-shadow: 0 3px 12px rgba(36,52,71,0.05);
        }


        .top-header h1 {
            margin: 0;
            font-family: 'Cinzel', serif;
            font-size: 24px;
            color: #243447;
            font-weight: 600;
            letter-spacing: 0.5px;
        }


        .top-header h1::after {
            content: "";
            display: block;
            width: 58px;
            height: 2px;
            background: #c6a15b;
            margin-top: 8px;
        }


        .system-name {
            color: #66717c;
            font-size: 11px;
            letter-spacing: .3px;
        }


        /* ==============================
           CONTENT
           ============================== */

        .page-content {
            padding: 40px;
        }


        .welcome {
            margin-bottom: 25px;
        }


        .welcome h2 {
            margin: 0 0 8px;
            font-family: 'Cinzel', serif;
            font-size: 25px;
            color: #4b3a2f;
        }


        .welcome p {
            margin: 0;
            color: #66717c;
            font-size: 13px;
        }


        /* ==============================
           ATTENDANCE CARD
           ============================== */

        .attendance-card {
            width: 100%;
            max-width: 1000px;
            background: #fffdf9;
            border: 1px solid #ddd4c8;
            border-radius: 6px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(36,52,71,0.08);
        }


        .attendance-card h3 {
            margin: 0 0 10px;
            font-family: 'Cinzel', serif;
            font-size: 21px;
            color: #243447;
        }


        .attendance-subtitle {
            margin-bottom: 25px;
            color: #66717c;
            font-size: 13px;
        }


        /* ==============================
           TABLE
           ============================== */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }


        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }


        .attendance-table th {
            background: #243447;
            color: #f3d99b;
            padding: 14px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 500;
        }


        .attendance-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee6dc;
            color: #4b5966;
            font-size: 13px;
        }


        .attendance-table tr:hover td {
            background: #faf7f1;
        }


        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            background: #e8f5e9;
            color: #2e7d32;
            font-size: 11px;
            font-weight: 600;
        }


        /* ==============================
           NO ATTENDANCE
           ============================== */

        .no-attendance {
            padding: 25px;
            background: #f8f3eb;
            border-left: 3px solid #c6a15b;
            border-radius: 4px;
            color: #66717c;
            font-size: 13px;
        }


        /* ==============================
           MOBILE
           ============================== */

        @media (max-width: 800px) {

            .sidebar {
                width: 220px;
            }


            .main-content {
                margin-left: 220px;
                width: calc(100% - 220px);
            }


            .top-header {
                padding: 0 20px;
            }


            .system-name {
                display: none;
            }


            .page-content {
                padding: 30px 20px;
            }

        }

    </style>

</head>


<body>


<div class="dashboard-layout">


    <!-- ==============================
         SIDEBAR
         ============================== -->

    <aside class="sidebar">


        <div class="sidebar-title">

            <h2>
                Admin Portal
            </h2>

            <p>
                Internship Management
            </p>

        </div>


        <nav class="sidebar-menu">


            <div class="menu-title">
                Main Menu
            </div>


            <a href="admin_dashboard.php">

                <span class="menu-icon">
                    ⌂
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="manage_students.php">

                <span class="menu-icon">
                    👥
                </span>

                <span>
                    Manage Students
                </span>

            </a>


            <a href="manage_internships.php">

                <span class="menu-icon">
                    🏢
                </span>

                <span>
                    Manage Internships
                </span>

            </a>


            <a href="manage_applications.php">

                <span class="menu-icon">
                    📋
                </span>

                <span>
                    Manage Applications
                </span>

            </a>


            <a href="admin_reports.php">

                <span class="menu-icon">
                    📄
                </span>

                <span>
                    View Reports
                </span>

            </a>


            <a href="admin_attendance.php" class="active">

                <span class="menu-icon">
                    📅
                </span>

                <span>
                    Attendance
                </span>

            </a>


            <a href="manage_meeting.php">

                <span class="menu-icon">
                    💻
                </span>

                <span>
                    Manage Meeting
                </span>

            </a>


            <a href="issue_certificate.php">

                <span class="menu-icon">
                    🎓
                </span>

                <span>
                    Issue Certificate
                </span>

            </a>


        </nav>


        <div class="sidebar-bottom">

            <a href="logout.php"
               class="logout-link">

                <span>
                    ↪
                </span>

                Logout

            </a>

        </div>


    </aside>


    <!-- ==============================
         MAIN CONTENT
         ============================== -->

    <main class="main-content">


        <header class="top-header">

            <h1>
                Attendance
            </h1>

            <div class="system-name">
                Digital Internship Management System
            </div>

        </header>


        <section class="page-content">


            <div class="welcome">

                <h2>
                    Student Attendance
                </h2>

                <p>
                    View students who have attended the internship online meeting.
                </p>

            </div>


            <div class="attendance-card">


                <h3>
                    Present Students
                </h3>


                <div class="attendance-subtitle">
                    Students who have marked their attendance are displayed below.
                </div>


                <?php if ($result && mysqli_num_rows($result) > 0): ?>


                    <div class="table-wrapper">


                        <table class="attendance-table">


                            <thead>

                                <tr>

                                    <th>
                                        Student Name
                                    </th>

                                    <th>
                                        Username
                                    </th>

                                    <th>
                                        Email
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php while ($student = mysqli_fetch_assoc($result)): ?>


                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['student_name']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['username']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['email']
                                        );
                                        ?>
                                    </td>


                                    <td>

                                        <span class="status-badge">
                                            Present
                                        </span>

                                    </td>

                                </tr>


                            <?php endwhile; ?>


                            </tbody>


                        </table>


                    </div>


                <?php else: ?>


                    <div class="no-attendance">

                        No students have marked attendance yet.

                    </div>


                <?php endif; ?>


            </div>


        </section>


    </main>


</div>


</body>

</html>