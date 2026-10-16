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
   GET INTERNSHIP ID
   ========================================= */

if (!isset($_GET['internship_id'])) {
    die("Internship not found.");
}

$internship_id = intval($_GET['internship_id']);


/* =========================================
   GET INTERNSHIP DETAILS
   ========================================= */

$sql = "SELECT
            internship_id,
            company_name,
            internship_domain,
            description,
            start_date,
            end_date,
            guide_name,
            status
        FROM internships
        WHERE internship_id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $internship_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$internship = mysqli_fetch_assoc($result);

if (!$internship) {
    die("Internship not found.");
}

mysqli_stmt_close($stmt);

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        View Internship | Digital Internship Management System
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
           MAIN LAYOUT
           ========================================= */

        .dashboard-layout {

            min-height: 100vh;

            display: flex;

        }


        /* =========================================
           ADMIN SIDEBAR
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


        /* SCROLLBAR */

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
           NO SIDEBAR MOVEMENT
           ========================================= */

        .sidebar-menu a:hover {

            background: rgba(198,161,91,.17);

            color: #fff;

            padding-left: 14px;

            transform: none;

        }


        /* ACTIVE MENU */

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
           RIGHT MAIN CONTENT
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


        /* LEFT SMALL TITLE BOX */

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


        /* RIGHT SYSTEM NAME */

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
           DETAIL CARD
           ========================================= */

        .details-card {

            max-width: 950px;

            margin: 0 auto;

            background: #fffdf9;

            border: 1px solid #ddd4c8;

            border-radius: 7px;

            box-shadow: 0 8px 25px rgba(36,52,71,0.08);

            overflow: hidden;

        }


        /* =========================================
           CARD HEADER
           ========================================= */

        .details-header {

            padding: 25px 30px;

            background: #243447;

            border-bottom: 3px solid #c6a15b;

        }


        .details-header h2 {

            margin: 0;

            font-family: 'Cinzel', serif;

            color: #f3d99b;

            font-size: 22px;

            font-weight: 600;

        }


        .details-header p {

            margin: 7px 0 0;

            color: #e5e9ed;

            font-size: 11px;

        }


        /* =========================================
           DETAILS AREA
           ========================================= */

        .details-body {

            padding: 25px 30px 30px;

        }


        /* =========================================
           DETAIL ROW
           ========================================= */

        .detail-row {

            display: grid;

            grid-template-columns: 220px 1fr;

            min-height: 55px;

            border-bottom: 1px solid #e5ded5;

        }


        .detail-row:last-child {

            border-bottom: none;

        }


        .detail-label {

            padding: 17px 15px;

            background: #f7f3ec;

            color: #5f5147;

            font-family: 'Cinzel', serif;

            font-size: 11px;

            font-weight: 600;

        }


        .detail-value {

            padding: 17px 18px;

            color: #3f4b57;

            font-size: 12px;

            line-height: 1.6;

        }


        /* =========================================
           COMPANY NAME
           ========================================= */

        .company-value {

            color: #243447;

            font-weight: 600;

            font-size: 13px;

        }


        /* =========================================
           DESCRIPTION
           ========================================= */

        .description-value {

            line-height: 1.8;

        }


        /* =========================================
           STATUS
           ========================================= */

        .status {

            display: inline-block;

            padding: 6px 14px;

            border-radius: 20px;

            background: #ead39b;

            color: #5b4727;

            font-size: 10px;

            font-weight: 600;

        }


        /* =========================================
           BUTTON AREA
           ========================================= */

        .button-area {

            margin-top: 25px;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .back-btn {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding: 11px 18px;

            background: #243447;

            color: #f3d99b;

            text-decoration: none;

            border: 1px solid #243447;

            border-radius: 4px;

            font-size: 11px;

            font-weight: 500;

            transition: none;

        }


        .back-btn:hover {

            background: #c6a15b;

            color: #243447;

            border-color: #c6a15b;

        }


        .record-label {

            color: #8a8f94;

            font-size: 10px;

        }


        /* =========================================
           MOBILE
           ========================================= */

        @media (max-width: 900px) {

            .sidebar {

                width: 230px;

            }


            .main-content {

                margin-left: 230px;

                width: calc(100% - 230px);

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


            .detail-row {

                grid-template-columns: 170px 1fr;

            }

        }


        @media (max-width: 650px) {

            .detail-row {

                grid-template-columns: 1fr;

            }


            .detail-label {

                padding-bottom: 8px;

            }


            .detail-value {

                padding-top: 5px;

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


            <!-- DASHBOARD -->

            <a href="admin_dashboard.php">

                <span class="menu-icon">
                    ⌂
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            <!-- MANAGE STUDENTS -->

            <a href="manage_students.php">

                <span class="menu-icon">
                    👤
                </span>

                <span>
                    Manage Students
                </span>

            </a>


            <!-- MANAGE INTERNSHIPS ACTIVE -->

            <a href="manage_internships.php"
               class="active">

                <span class="menu-icon">
                    🏢
                </span>

                <span>
                    Manage Internships
                </span>

            </a>


            <!-- APPLICATIONS -->

            <a href="manage_applications.php">

                <span class="menu-icon">
                    📋
                </span>

                <span>
                    Manage Applications
                </span>

            </a>


            <!-- REPORTS -->

            <a href="manage_reports.php">

                <span class="menu-icon">
                    📄
                </span>

                <span>
                    View Reports
                </span>

            </a>


            <!-- ATTENDANCE -->

            <a href="admin_attendance.php">

                <span class="menu-icon">
                    📅
                </span>

                <span>
                    Attendance
                </span>

            </a>


            <!-- CERTIFICATE -->

            <a href="issue_certificate.php">

                <span class="menu-icon">
                    🎓
                </span>

                <span>
                    Issue Certificate
                </span>

            </a>


        </nav>


        <!-- LOGOUT -->

        <div class="sidebar-bottom">

            <a href="admin_logout.php"
               class="logout-link">

                <span>
                    ↪
                </span>

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
                    View Internship
                </h1>

            </div>


            <div class="system-name">

                Digital Internship Management System

            </div>


        </header>



        <!-- =====================================
             PAGE CONTENT
             ===================================== -->

        <section class="page-content">


            <div class="details-card">


                <!-- CARD HEADER -->

                <div class="details-header">

                    <h2>
                        Internship Details
                    </h2>

                    <p>
                        Complete information about the selected internship.
                    </p>

                </div>



                <!-- DETAILS -->

                <div class="details-body">


                    <!-- INTERNSHIP ID -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Internship ID
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars(
                                $internship['internship_id']
                            );
                            ?>

                        </div>

                    </div>



                    <!-- COMPANY -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Company Name
                        </div>

                        <div class="detail-value company-value">

                            <?php
                            echo htmlspecialchars(
                                $internship['company_name']
                            );
                            ?>

                        </div>

                    </div>



                    <!-- DOMAIN -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Internship Domain
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars(
                                $internship['internship_domain']
                            );
                            ?>

                        </div>

                    </div>



                    <!-- DESCRIPTION -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Description
                        </div>

                        <div class="detail-value description-value">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $internship['description']
                                )
                            );
                            ?>

                        </div>

                    </div>



                    <!-- START DATE -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Start Date
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars(
                                $internship['start_date']
                            );
                            ?>

                        </div>

                    </div>



                    <!-- END DATE -->

                    <div class="detail-row">

                        <div class="detail-label">
                            End Date
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars(
                                $internship['end_date']
                            );
                            ?>

                        </div>

                    </div>



                    <!-- GUIDE -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Guide Name
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars(
                                $internship['guide_name']
                            );
                            ?>

                        </div>

                    </div>



                    <!-- STATUS -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Internship Status
                        </div>

                        <div class="detail-value">

                            <span class="status">

                                <?php
                                echo htmlspecialchars(
                                    $internship['status']
                                );
                                ?>

                            </span>

                        </div>

                    </div>



                    <!-- BUTTON -->

                    <div class="button-area">


                        <a
                            href="manage_internships.php"
                            class="back-btn"
                        >

                            ← Back to Manage Internships

                        </a>


                        <span class="record-label">

                            Internship Record

                        </span>


                    </div>


                </div>


            </div>


        </section>


    </main>


</div>


</body>

</html>