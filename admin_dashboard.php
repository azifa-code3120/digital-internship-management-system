<?php
session_start();
$admin_username = $_SESSION['admin_username'] ?? 'Admin';
include "db_connect.php";

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Digital Internship Management System</title>


    <!-- Google Fonts -->

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


        /* NO SIDEBAR MOVEMENT */

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

        }


        /* ADMIN DASHBOARD SMALL BOX */

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


        /* SYSTEM NAME RIGHT SIDE */

        .system-name {

            color: #66717c;

            font-size: 11px;

            letter-spacing: .5px;

        }


        /* =========================================
           WELCOME AREA
           ========================================= */

        .welcome-area {

            flex: 1;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            padding: 40px;

        }


        .welcome-content {

            margin-top: -60px;

        }


        .welcome-content h2 {

            margin: 0 0 12px;

            font-family: 'Cinzel', serif;

            font-size: 29px;

            color: #243447;

            font-weight: 600;

        }


        .welcome-content h2::after {

            content: "";

            display: block;

            width: 58px;

            height: 2px;

            background: #c6a15b;

            margin: 13px auto 18px;

        }


        .welcome-content p {

            margin: 0;

            color: #66717c;

            font-size: 13px;

            letter-spacing: .3px;

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

        }


    </style>

</head>


<body>


<div class="dashboard-layout">


    <!-- =========================================
         SIDEBAR
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


            <a href="admin_dashboard.php" class="active">

                <span class="menu-icon">⌂</span>

                <span>Dashboard</span>

            </a>


            <a href="manage_students.php">

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


           <a href="http://localhost:8080/digital_management/admin_attendance.php">
    <span class="menu-icon">🗓️</span>
    <span>Attendance</span>
</a>
            <a href="manage_meeting.php">
    <span class="menu-icon">💻</span>
    <span>Manage Meeting</span>
</a>


            <a href="issue_certificate.php">

                <span class="menu-icon">🎓</span>

                <span>Issue Certificate</span>

            </a>


        </nav>


        <!-- LOGOUT -->

        <div class="sidebar-bottom">

            <a href="admin_logout.php" class="logout-link">

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
                    Admin Dashboard
                </h1>

            </div>


            <div class="system-name">

                Digital Internship Management System

            </div>


        </header>



        <!-- WELCOME -->

        <section class="welcome-area">


            <div class="welcome-content">

                <h2>
                    Welcome, <?php echo htmlspecialchars($admin_username); ?>
                </h2>


                <p>
                    Manage your internship activities efficiently.
                </p>

            </div>


        </section>


    </main>


</div>


</body>

</html>