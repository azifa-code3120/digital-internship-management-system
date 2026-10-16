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
   GET STUDENT DETAILS
   ========================================= */

$student = null;
$message = "";

if (isset($_GET['reg_no'])) {

    $reg_no = trim($_GET['reg_no']);

    $sql = "SELECT
                username,
                student_name,
                department,
                email
            FROM student
            WHERE reg_no = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Database Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $reg_no);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        $student = mysqli_fetch_assoc($result);

    } else {

        $message = "Student not found.";

    }

    mysqli_stmt_close($stmt);

} else {

    $message = "Invalid student request.";

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        View Student | Digital Internship Management System
    </title>


    <!-- GOOGLE FONTS -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
          rel="stylesheet">


    <style>

        /* =========================================
           RESET
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
           LAYOUT
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


        /* NO MOVEMENT */

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


        .system-name {

            color: #66717c;

            font-size: 11px;

            letter-spacing: .5px;

        }


        /* =========================================
           PAGE CONTENT
           ========================================= */

        .page-content {

            padding: 40px;

        }


        /* =========================================
           CONTENT HEADING
           ========================================= */

        .content-heading {

            margin-bottom: 25px;

        }


        .content-heading h2 {

            margin: 0;

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
           DETAILS CARD
           ========================================= */

        .details-card {

            background: #fffdf9;

            border: 1px solid #ddd4c8;

            border-radius: 7px;

            box-shadow: 0 8px 25px rgba(36,52,71,0.08);

            overflow: hidden;

            max-width: 850px;

        }


        /* =========================================
           CARD HEADER
           ========================================= */

        .details-card-header {

            background: #243447;

            padding: 20px 25px;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .details-card-header h3 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 16px;

            font-weight: 600;

            color: #f3d99b;

        }


        .student-label {

            font-size: 10px;

            color: #e5e9ed;

            letter-spacing: 1px;

            text-transform: uppercase;

        }


        /* =========================================
           DETAILS TABLE
           ========================================= */

        .details-table {

            width: 100%;

            border-collapse: collapse;

        }


        .details-table tr {

            border-bottom: 1px solid #e5ded5;

        }


        .details-table tr:last-child {

            border-bottom: none;

        }


        .details-table td {

            padding: 19px 24px;

            font-size: 13px;

        }


        .details-table .label {

            width: 35%;

            background: #f7f3ec;

            color: #6b5142;

            font-weight: 600;

            font-size: 12px;

            letter-spacing: .2px;

        }


        .details-table .value {

            color: #3e4a55;

            background: #fffdf9;

        }


        .reg-value {

            color: #243447;

            font-weight: 600;

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

            padding: 10px 18px;

            background: #243447;

            color: #f3d99b;

            text-decoration: none;

            border-radius: 4px;

            border: 1px solid #243447;

            font-size: 11px;

            font-weight: 500;

            transition: none;

        }


        .back-btn:hover {

            background: #c6a15b;

            color: #243447;

            border-color: #c6a15b;

        }


        /* =========================================
           ERROR MESSAGE
           ========================================= */

        .message-card {

            max-width: 700px;

            background: #fffdf9;

            border: 1px solid #ddd4c8;

            border-left: 4px solid #c6a15b;

            padding: 25px;

            border-radius: 6px;

            color: #6b5142;

            font-size: 13px;

            box-shadow: 0 7px 20px rgba(36,52,71,0.07);

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

                padding: 25px 20px;

            }


            .details-table td {

                padding: 15px;

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


            <a href="admin_dashboard.php">

                <span class="menu-icon">⌂</span>

                <span>Dashboard</span>

            </a>


            <!-- ACTIVE -->

            <a href="manage_students.php" class="active">

                <span class="menu-icon">👤</span>

                <span>Manage Students</span>

            </a>


            <a href="manage_internships.php">

                <span class="menu-icon">🏢</span>

                <span>Manage Internships</span>

            </a>


            <a href="manage_applications.php">

                <span class="menu-icon">📋</span>

                <span>Manage Applications</span>

            </a>


            <a href="manage_reports.php">

                <span class="menu-icon">📄</span>

                <span>View Reports</span>

            </a>


            <a href="admin_attendance.php">

                <span class="menu-icon">📅</span>

                <span>Attendance</span>

            </a>


            <a href="issue_certificate.php">

                <span class="menu-icon">🎓</span>

                <span>Issue Certificate</span>

            </a>


        </nav>


        <!-- LOGOUT -->

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


        <!-- TOP HEADER -->

        <header class="top-header">


            <div class="page-title-box">

                <h1>
                    View Student
                </h1>

            </div>


            <div class="system-name">

                Digital Internship Management System

            </div>


        </header>



        <!-- PAGE CONTENT -->

        <section class="page-content">


            <?php if ($student) { ?>


                <!-- PAGE HEADING -->

                <div class="content-heading">

                    <h2>
                        Student Details
                    </h2>

                    <p>
                        Complete information of the selected student.
                    </p>

                </div>



                <!-- DETAILS CARD -->

                <div class="details-card">


                    <div class="details-card-header">

                        <h3>
                            Student Information
                        </h3>


                        <span class="student-label">
                            Registered Student
                        </span>

                    </div>



                    <table class="details-table">


                        <tr>

                            <td class="label">
                                Username
                            </td>

                            <td class="value">

                                <?php
                                echo htmlspecialchars(
                                    $student['username']
                                );
                                ?>

                            </td>

                        </tr>


                        <tr>

                            <td class="label">
                                Student Name
                            </td>

                            <td class="value">

                                <?php
                                echo htmlspecialchars(
                                    $student['student_name']
                                );
                                ?>

                            </td>

                        </tr>


                        <tr>

                            <td class="label">
                                Department
                            </td>

                            <td class="value">

                                <?php
                                echo htmlspecialchars(
                                    $student['department']
                                );
                                ?>

                            </td>

                        </tr>


                        


                        <tr>

                            <td class="label">
                                Email
                            </td>

                            <td class="value">

                                <?php
                                echo htmlspecialchars(
                                    $student['email']
                                );
                                ?>

                            </td>

                        </tr>


                    </table>


                </div>



                <!-- BACK -->

                <div class="back-area">

                    <a
                        href="manage_students.php"
                        class="back-btn"
                    >

                        ← Back to Manage Students

                    </a>

                </div>


            <?php } else { ?>


                <!-- ERROR -->

                <div class="content-heading">

                    <h2>
                        Student Details
                    </h2>

                </div>


                <div class="message-card">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>


                <div class="back-area">

                    <a
                        href="manage_students.php"
                        class="back-btn"
                    >

                        ← Back to Manage Students

                    </a>

                </div>


            <?php } ?>


        </section>


    </main>


</div>


</body>

</html>