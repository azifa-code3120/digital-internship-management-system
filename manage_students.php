<?php
session_start();
include "db_connect.php";

/* =========================================
   ADMIN ACCESS CHECK
   ========================================= */

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}


/* =========================================
   FETCH STUDENTS
   ========================================= */

$sql = "SELECT reg_no,
               student_name,
               department,
               year_of_study,
               email
        FROM student
        ORDER BY student_name ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Manage Students | Digital Internship Management System
    </title>


    <!-- =========================================
         GOOGLE FONTS
         ========================================= -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
          rel="stylesheet">


    <style>

        /* =========================================
           BASIC RESET
           ========================================= */

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background: #f7f3ec;

            color: #243447;

            font-family: 'Poppins', sans-serif;

        }


        /* =========================================
           MAIN LAYOUT
           ========================================= */

        .dashboard-layout {

            min-height: 100vh;

            display: flex;

        }


        /* =========================================
           SIDEBAR
           ========================================= */

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

            z-index: 1000;

        }


        /* Scrollbar */

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


        /* =========================================
           SIDEBAR TITLE
           ========================================= */

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


        /* =========================================
           SIDEBAR MENU
           ========================================= */

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


        /* =========================================
           SIDEBAR MUST NOT MOVE
           ========================================= */

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


        /* =========================================
           SIDEBAR BOTTOM
           ========================================= */

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

            transition: none;

        }


        .logout-link:hover {

            background: #c6a15b;

            color: #243447;

        }


        /* =========================================
           MAIN CONTENT
           ========================================= */

        .main-content {

            margin-left: 255px;

            width: calc(100% - 255px);

            min-height: 100vh;

            display: flex;

            flex-direction: column;

        }


        /* =========================================
           TOP HEADER
           ========================================= */

        .top-header {

            height: 80px;

            background: #fffdf9;

            border-bottom: 1px solid #ddd4c8;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 40px;

            box-shadow: 0 3px 12px rgba(36,52,71,0.05);

            flex-shrink: 0;

        }


        /* =========================================
           PAGE TITLE BOX
           ========================================= */

        .page-title-box {

            border-left: 4px solid #c6a15b;

            padding: 9px 16px;

            background: #f7f3ec;

            border-radius: 3px;

        }


        .page-title-box h1 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 21px;

            color: #243447;

            font-weight: 600;

            letter-spacing: 0.5px;

        }


        /* =========================================
           SYSTEM NAME
           ========================================= */

        .system-name {

            color: #66717c;

            font-size: 11px;

            letter-spacing: .5px;

        }


        /* =========================================
           PAGE CONTENT
           ========================================= */

        .page-content {

            padding: 35px 40px 45px;

        }


        /* =========================================
           CONTENT HEADING
           ========================================= */

        .content-heading {

            margin-bottom: 24px;

        }


        .content-heading h2 {

            margin: 0 0 7px;

            font-family: 'Cinzel', serif;

            font-size: 24px;

            color: #243447;

            font-weight: 600;

        }


        .content-heading h2::after {

            content: "";

            display: block;

            width: 55px;

            height: 2px;

            background: #c6a15b;

            margin-top: 9px;

        }


        .content-heading p {

            margin: 12px 0 0;

            color: #737b83;

            font-size: 12px;

        }


        /* =========================================
           TABLE CARD
           ========================================= */

        .table-card {

            background: #fffdf9;

            border: 1px solid #ddd4c8;

            border-radius: 7px;

            box-shadow: 0 7px 22px rgba(36,52,71,0.08);

            overflow: hidden;

        }


        /* =========================================
           TABLE TOP
           ========================================= */

        .table-top {

            padding: 20px 24px;

            border-bottom: 1px solid #e4ddd4;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .table-top h3 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 16px;

            color: #243447;

            font-weight: 600;

        }


        .student-count {

            padding: 6px 12px;

            background: #f7f3ec;

            border: 1px solid #d8cbb9;

            color: #6b5142;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 500;

        }


        /* =========================================
           TABLE WRAPPER
           ========================================= */

        .table-wrapper {

            overflow-x: auto;

        }


        /* =========================================
           TABLE
           ========================================= */

        table {

            width: 100%;

            border-collapse: collapse;

        }


        thead th {

            background: #243447;

            color: #f3d99b;

            padding: 15px 20px;

            text-align: left;

            font-size: 11px;

            font-weight: 600;

            letter-spacing: .6px;

        }


        tbody td {

            padding: 18px 20px;

            border-bottom: 1px solid #e5ded5;

            color: #46515c;

            font-size: 13px;

            vertical-align: middle;

        }


        tbody tr:last-child td {

            border-bottom: none;

        }


        tbody tr:hover {

            background: #faf6ef;

        }


        /* =========================================
           SERIAL NUMBER
           ========================================= */

        .serial {

            width: 80px;

            color: #8a8f94;

            font-size: 11px;

        }


        /* =========================================
           STUDENT NAME
           ========================================= */

        .student-name {

            color: #243447;

            font-weight: 500;

        }


        /* =========================================
           DELETE ICON
           SHOW ONLY WHEN STUDENT NAME IS HOVERED
           ========================================= */

        .student-name-cell {

            position: relative;

        }

        .student-delete {

            opacity: 0;
            color: #9b3d3d;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            margin-left: 10px;
            transition: opacity 0.2s ease;

        }

        .student-name-cell:hover .student-delete {

            opacity: 1;

        }

        .student-delete:hover {

            color: #721f1f;

        }


        /* =========================================
           VIEW BUTTON
           ========================================= */

        .view-btn {

            display: inline-block;

            padding: 8px 18px;

            background: #243447;

            color: #f3d99b;

            text-decoration: none;

            border-radius: 4px;

            font-size: 11px;

            font-weight: 500;

            border: 1px solid #243447;

            transition: none;

        }


        .view-btn:hover {

            background: #c6a15b;

            color: #243447;

            border-color: #c6a15b;

        }


        /* =========================================
           EMPTY STATE
           ========================================= */

        .empty-state {

            text-align: center;

            padding: 55px 20px;

            color: #737b83;

        }


        .empty-state-icon {

            font-size: 30px;

            margin-bottom: 10px;

        }


        .empty-state h3 {

            margin: 0 0 6px;

            font-family: 'Cinzel', serif;

            color: #243447;

            font-size: 17px;

        }


        .empty-state p {

            margin: 0;

            font-size: 12px;

        }


        /* =========================================
           BACK BUTTON
           ========================================= */

        .back-area {

            margin-top: 25px;

        }


        .back-btn {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding: 10px 17px;

            background: #243447;

            color: #f3d99b;

            text-decoration: none;

            border-radius: 4px;

            font-size: 11px;

            font-weight: 500;

            border: 1px solid #243447;

            transition: none;

        }


        .back-btn:hover {

            background: #c6a15b;

            color: #243447;

            border-color: #c6a15b;

        }


        /* =========================================
           MOBILE
           ========================================= */

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

                padding: 25px 20px 35px;

            }

        }


    </style>

</head>


<body>


<div class="dashboard-layout">


    <!-- =========================================
         ADMIN SIDEBAR
         ========================================= -->

    <aside class="sidebar">


        <div class="sidebar-title">

            <h2>Admin Portal</h2>

            <p>Internship Management</p>

        </div>


        <nav class="sidebar-menu">


            <div class="menu-title">
                Main Menu
            </div>


            <!-- DASHBOARD -->

            <a href="admin_dashboard.php">

                <span class="menu-icon">⌂</span>

                <span>Dashboard</span>

            </a>


            <!-- MANAGE STUDENTS - ACTIVE -->

            <a href="manage_students.php" class="active">

                <span class="menu-icon">👤</span>

                <span>Manage Students</span>

            </a>


            <!-- MANAGE INTERNSHIPS -->

            <a href="manage_internships.php">

                <span class="menu-icon">🏢</span>

                <span>Manage Internships</span>

            </a>


            <!-- MANAGE APPLICATIONS -->

            <a href="manage_applications.php">

                <span class="menu-icon">📋</span>

                <span>Manage Applications</span>

            </a>


            <!-- REPORTS -->

            <a href="manage_reports.php">

                <span class="menu-icon">📄</span>

                <span>View Reports</span>

            </a>


            <!-- ATTENDANCE -->

            <a href="admin_attendance.php">

                <span class="menu-icon">📅</span>

                <span>Attendance</span>

            </a>
  <a href="manage_meeting.php">
    <span class="menu-icon">💻</span>
    <span>Manage Meeting</span>
</a>

            <!-- CERTIFICATE -->

            <a href="issue_certificate.php">

                <span class="menu-icon">🎓</span>

                <span>Issue Certificate</span>

            </a>


        </nav>


        <!-- =====================================
             LOGOUT
             ===================================== -->

        <div class="sidebar-bottom">

            <a href="admin_logout.php"
               class="logout-link">

                <span>↪</span>

                Logout

            </a>

        </div>


    </aside>



    <!-- =========================================
         RIGHT SIDE
         ========================================= -->

    <main class="main-content">


        <!-- =====================================
             TOP HEADER
             ===================================== -->

        <header class="top-header">


            <!-- LEFT -->

            <div class="page-title-box">

                <h1>
                    Manage Students
                </h1>

            </div>


            <!-- RIGHT -->

            <div class="system-name">

                Digital Internship Management System

            </div>


        </header>



        <!-- =====================================
             PAGE CONTENT
             ===================================== -->

        <section class="page-content">


            <!-- PAGE INTRO -->

            <div class="content-heading">

                <h2>
                    Student Records
                </h2>

                <p>
                    View registered students and access their complete details.
                </p>

            </div>



            <!-- =================================
                 STUDENT TABLE
                 ================================= -->

            <div class="table-card">


                <!-- TABLE TITLE -->

                <div class="table-top">

                    <h3>
                        Registered Students
                    </h3>


                    <span class="student-count">

                        <?php echo mysqli_num_rows($result); ?>

                        Students

                    </span>

                </div>



                <?php if (mysqli_num_rows($result) > 0) { ?>


                    <div class="table-wrapper">

                        <table>


                            <thead>

                                <tr>

                                    <th>
                                        S.No
                                    </th>

                                    <th>
                                        Student Name
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php

                                $sno = 1;

                                while ($row = mysqli_fetch_assoc($result)) {

                                ?>


                                    <tr>


                                        <!-- S.NO -->

                                        <td class="serial">

                                            <?php echo $sno++; ?>

                                        </td>



                                        <!-- STUDENT NAME -->

                                        <td class="student-name student-name-cell">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['student_name']
                                            );
                                            ?>

                                            <a
                                                href="delete_student.php?reg_no=<?php echo urlencode($row['reg_no']); ?>"
                                                class="student-delete"
                                                title="Delete Student"
                                                onclick="return confirm('Are you sure you want to delete this student?');"
                                            >
                                                ×
                                            </a>

                                        </td>



                                        <!-- VIEW -->

                                        <td>

                                            <a
                                                href="view_student.php?reg_no=<?php echo urlencode($row['reg_no']); ?>"
                                                class="view-btn"
                                            >
                                                View
                                            </a>

                                        </td>


                                    </tr>


                                <?php

                                }

                                ?>


                            </tbody>


                        </table>

                    </div>


                <?php } else { ?>


                    <!-- EMPTY -->

                    <div class="empty-state">

                        <div class="empty-state-icon">
                            👤
                        </div>


                        <h3>
                            No Students Found
                        </h3>


                        <p>
                            No students have been registered yet.
                        </p>

                    </div>


                <?php } ?>


            </div>



            <!-- =================================
                 BACK BUTTON
                 ================================= -->

            <div class="back-area">

                <a
                    href="admin_dashboard.php"
                    class="back-btn"
                >

                    ← Back to Admin Dashboard

                </a>

            </div>


        </section>


    </main>


</div>


</body>

</html>