<?php

session_start();

include "db_connect.php";


/* =========================================
   ADMIN LOGIN CHECK
   ========================================= */

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    header("Location: admin_login.php");
    exit();

}


/* =========================================
   FETCH INTERNSHIPS
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
        ORDER BY internship_id ASC";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die("Database Error: " . mysqli_error($conn));

}


$total_internships = mysqli_num_rows($result);

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">


    <title>
        Manage Internships | Digital Internship Management System
    </title>


    <!-- =====================================
         GOOGLE FONTS
         ===================================== -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
          rel="stylesheet">


    <style>


        /* =====================================
           RESET
           ===================================== */

        * {

            box-sizing: border-box;

        }


        /* =====================================
           BODY
           ===================================== */

        body {

            margin: 0;

            background: #f7f3ec;

            color: #243447;

            font-family: 'Poppins', sans-serif;

        }


        /* =====================================
           MAIN LAYOUT
           ===================================== */

        .dashboard-layout {

            min-height: 100vh;

            display: flex;

        }


        /* =====================================
           ADMIN SIDEBAR
           ===================================== */

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


        /* =====================================
           SIDEBAR SCROLLBAR
           ===================================== */

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


        /* =====================================
           SIDEBAR TITLE
           ===================================== */

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


        /* =====================================
           SIDEBAR MENU
           ===================================== */

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


        /* =====================================
           SIDEBAR LINKS
           ===================================== */

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


        /* =====================================
           NO SIDEBAR MOVEMENT
           ===================================== */

        .sidebar-menu a:hover {

            background: rgba(198,161,91,.17);

            color: #fff;

            padding-left: 14px;

            transform: none;

        }


        /* =====================================
           ACTIVE LINK
           ===================================== */

        .sidebar-menu a.active {

            background: #c6a15b;

            color: #243447;

            font-weight: 600;

        }


        /* =====================================
           MENU ICON
           ===================================== */

        .menu-icon {

            width: 24px;

            min-width: 24px;

            text-align: center;

            font-size: 16px;

        }


        /* =====================================
           SIDEBAR BOTTOM
           ===================================== */

        .sidebar-bottom {

            padding: 18px 15px;

            border-top: 1px solid rgba(198,161,91,.4);

            flex-shrink: 0;

        }


        /* =====================================
           LOGOUT
           ===================================== */

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


        /* =====================================
           MAIN CONTENT
           ===================================== */

        .main-content {

            margin-left: 255px;

            width: calc(100% - 255px);

            min-height: 100vh;

        }


        /* =====================================
           TOP HEADER
           ===================================== */

        .top-header {

            min-height: 80px;

            background: #fffdf9;

            border-bottom: 1px solid #ddd4c8;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 40px;

            box-shadow: 0 3px 12px rgba(36,52,71,0.05);

        }


        /* =====================================
           PAGE TITLE
           ===================================== */

        .page-title-box {

            border-left: 4px solid #c6a15b;

            padding: 8px 15px;

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


        /* =====================================
           SYSTEM NAME
           ===================================== */

        .system-name {

            color: #66717c;

            font-size: 11px;

            letter-spacing: .5px;

        }


        /* =====================================
           PAGE AREA
           ===================================== */

        .page-content {

            padding: 35px 40px 45px;

        }


        /* =====================================
           INTRO SECTION
           ===================================== */

        .intro-section {

            display: flex;

            align-items: flex-end;

            justify-content: space-between;

            margin-bottom: 25px;

        }


        .intro-text h2 {

            margin: 0;

            font-family: 'Cinzel', serif;

            color: #243447;

            font-size: 22px;

            font-weight: 600;

            letter-spacing: .4px;

        }


        .intro-line {

            width: 55px;

            height: 2px;

            background: #c6a15b;

            margin: 10px 0;

        }


        .intro-text p {

            margin: 0;

            color: #68737d;

            font-size: 11px;

        }


        /* =====================================
           ADD BUTTON
           ===================================== */

        .add-btn {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 12px 18px;

            background: #243447;

            color: #f3d99b;

            border: 1px solid #243447;

            border-radius: 4px;

            text-decoration: none;

            font-size: 11px;

            font-weight: 600;

            transition: none;

        }


        .add-btn:hover {

            background: #c6a15b;

            color: #243447;

            border-color: #c6a15b;

        }


        /* =====================================
           TABLE CARD
           ===================================== */

        .table-card {

            background: #fffdf9;

            border: 1px solid #ddd4c8;

            border-radius: 7px;

            box-shadow: 0 8px 25px rgba(36,52,71,0.08);

            overflow: hidden;

        }


        /* =====================================
           TABLE TOP
           ===================================== */

        .table-top {

            min-height: 70px;

            padding: 0 25px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            border-bottom: 1px solid #ddd4c8;

        }


        .table-top h3 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 15px;

            color: #243447;

            font-weight: 600;

            letter-spacing: .5px;

            text-transform: uppercase;

        }


        /* =====================================
           COUNT BADGE
           ===================================== */

        .internship-count {

            padding: 7px 13px;

            border: 1px solid #dfd0b9;

            border-radius: 20px;

            color: #6b5a48;

            background: #faf7f1;

            font-size: 10px;

            font-weight: 500;

        }


        /* =====================================
           TABLE WRAPPER
           ===================================== */

        .table-wrapper {

            width: 100%;

            overflow-x: auto;

        }


        /* =====================================
           TABLE
           ===================================== */

        table {

            width: 100%;

            border-collapse: collapse;

        }


        /* =====================================
           TABLE HEADER
           ===================================== */

        thead th {

            background: #243447;

            color: #f3d99b;

            padding: 15px 20px;

            text-align: left;

            font-size: 11px;

            font-weight: 600;

            letter-spacing: .4px;

            white-space: nowrap;

        }


        /* =====================================
           TABLE BODY
           ===================================== */

        tbody td {

            padding: 18px 20px;

            border-bottom: 1px solid #e5ded5;

            color: #53606c;

            font-size: 12px;

            vertical-align: middle;

        }


        tbody tr:last-child td {

            border-bottom: none;

        }


        tbody tr:hover {

            background: #faf7f1;

        }


        /* =====================================
           COMPANY NAME
           ===================================== */

        .company-name {

            color: #243447;

            font-weight: 600;

            font-size: 13px;

        }


        /* =====================================
           ACTION BUTTONS
           ===================================== */

        .action-buttons {

            display: flex;

            align-items: center;

            gap: 7px;

        }


        .action-btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-width: 52px;

            padding: 8px 12px;

            border-radius: 4px;

            text-decoration: none;

            font-size: 10px;

            font-weight: 600;

            transition: none;

        }


        /* =====================================
           VIEW BUTTON
           ===================================== */

        .view-btn {

            background: #243447;

            color: #f3d99b;

            border: 1px solid #243447;

        }


        .view-btn:hover {

            background: #c6a15b;

            color: #243447;

            border-color: #c6a15b;

        }


        /* =====================================
           EDIT BUTTON
           ===================================== */

        .edit-btn {

            background: #8a6b4c;

            color: #fffdf9;

            border: 1px solid #8a6b4c;

        }


        .edit-btn:hover {

            background: #6f543c;

            border-color: #6f543c;

        }


        /* =====================================
           DELETE BUTTON
           ===================================== */

        .delete-btn {

            background: #a40000;

            color: white;

            border: 1px solid #a40000;

        }


        .delete-btn:hover {

            background: #820000;

            border-color: #820000;

        }


        /* =====================================
           EMPTY STATE
           ===================================== */

        .empty-state {

            text-align: center;

            padding: 60px 20px;

        }


        .empty-icon {

            font-size: 35px;

            margin-bottom: 12px;

        }


        .empty-state h3 {

            margin: 0 0 7px;

            font-family: 'Cinzel', serif;

            color: #243447;

            font-size: 17px;

        }


        .empty-state p {

            margin: 0;

            color: #7a8188;

            font-size: 11px;

        }


        /* =====================================
           BACK BUTTON
           ===================================== */

        .back-area {

            margin-top: 25px;

        }


        .back-btn {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 11px 17px;

            background: #243447;

            color: #f3d99b;

            text-decoration: none;

            border-radius: 4px;

            border: 1px solid #243447;

            font-size: 10px;

            font-weight: 500;

            transition: none;

        }


        .back-btn:hover {

            background: #c6a15b;

            color: #243447;

            border-color: #c6a15b;

        }


        /* =====================================
           MOBILE
           ===================================== */

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


            .page-content {

                padding: 25px 20px 35px;

            }


            .system-name {

                display: none;

            }

        }


        @media (max-width: 650px) {

            .intro-section {

                align-items: flex-start;

                flex-direction: column;

                gap: 18px;

            }


            .action-buttons {

                flex-wrap: wrap;

            }

        }


    </style>

</head>


<body>


<div class="dashboard-layout">


    <!-- =====================================
         ADMIN SIDEBAR
         ===================================== -->

    <aside class="sidebar">


        <!-- SIDEBAR TITLE -->

        <div class="sidebar-title">

            <h2>
                Admin Portal
            </h2>

            <p>
                Internship Management
            </p>

        </div>


        <!-- SIDEBAR MENU -->

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


            <!-- MANAGE INTERNSHIPS -->

            <a href="manage_internships.php"
               class="active">

                <span class="menu-icon">
                    🏢
                </span>

                <span>
                    Manage Internships
                </span>

            </a>


            <!-- MANAGE APPLICATIONS -->

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
              <a href="manage_meeting.php">
    <span class="menu-icon">💻</span>
    <span>Manage Meeting</span>
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


        <!-- SIDEBAR BOTTOM -->

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



    <!-- =====================================
         RIGHT SIDE MAIN CONTENT
         ===================================== -->

    <main class="main-content">


        <!-- =================================
             TOP HEADER
             ================================= -->

        <header class="top-header">


            <div class="page-title-box">

                <h1>
                    Manage Internships
                </h1>

            </div>


            <div class="system-name">

                Digital Internship Management System

            </div>


        </header>



        <!-- =================================
             PAGE CONTENT
             ================================= -->

        <section class="page-content">


            <!-- =================================
                 INTRO
                 ================================= -->

            <div class="intro-section">


                <div class="intro-text">


                    <h2>
                        Internship Opportunities
                    </h2>


                    <div class="intro-line"></div>


                    <p>
                        Manage and maintain available internship programs.
                    </p>


                </div>



                <!-- ADD INTERNSHIP -->

                <a href="add_internship.php"
                   class="add-btn">

                    <span>
                        +
                    </span>

                    Add Internship

                </a>


            </div>



            <!-- =================================
                 TABLE CARD
                 ================================= -->

            <div class="table-card">


                <!-- TABLE HEADER -->

                <div class="table-top">


                    <h3>
                        Registered Internships
                    </h3>


                    <span class="internship-count">

                        <?php
                        echo $total_internships;
                        ?>

                        <?php
                        echo ($total_internships == 1)
                            ? " Internship"
                            : " Internships";
                        ?>

                    </span>


                </div>



                <?php if ($total_internships > 0) { ?>


                    <!-- TABLE -->

                    <div class="table-wrapper">


                        <table>


                            <thead>

                                <tr>

                                    <th>
                                        Company Name
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>


                                    <tr>


                                        <!-- COMPANY NAME -->

                                        <td class="company-name">

                                            <?php

                                            echo htmlspecialchars(
                                                $row['company_name']
                                            );

                                            ?>

                                        </td>



                                        <!-- ACTION -->

                                        <td>


                                            <div class="action-buttons">


                                                <!-- VIEW -->

                                                <a
                                                    href="view_internship.php?internship_id=<?php echo urlencode($row['internship_id']); ?>"
                                                    class="action-btn view-btn"
                                                >

                                                    View

                                                </a>



                                                <!-- EDIT -->

                                                <a
                                                    href="edit_internship.php?internship_id=<?php echo urlencode($row['internship_id']); ?>"
                                                    class="action-btn edit-btn"
                                                >

                                                    Edit

                                                </a>



                                                <!-- DELETE -->

                                                <a
                                                    href="delete_internship.php?internship_id=<?php echo urlencode($row['internship_id']); ?>"
                                                    class="action-btn delete-btn"
                                                    onclick="return confirm('Are you sure you want to delete this internship?');"
                                                >

                                                    Delete

                                                </a>


                                            </div>


                                        </td>


                                    </tr>


                                <?php } ?>


                            </tbody>


                        </table>


                    </div>


                <?php } else { ?>


                    <!-- EMPTY STATE -->

                    <div class="empty-state">


                        <div class="empty-icon">
                            🏢
                        </div>


                        <h3>
                            No Internships Found
                        </h3>


                        <p>
                            Add an internship to display it here.
                        </p>


                    </div>


                <?php } ?>


            </div>



            <!-- =================================
                 BACK BUTTON
                 ================================= -->

            <div class="back-area">


                <a href="admin_dashboard.php"
                   class="back-btn">

                    ← Back to Admin Dashboard

                </a>


            </div>


        </section>


    </main>


</div>


</body>

</html>