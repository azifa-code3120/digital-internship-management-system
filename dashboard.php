<?php
session_start();
include "db_connect.php";

/* ==============================
   STUDENT LOGIN CHECK
   ============================== */

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];
/* ==============================
   GET UNREAD NOTIFICATION COUNT
   ============================== */

$unread_count = 0;

$sql_notifications = "SELECT COUNT(*) AS unread_count
                      FROM notifications
                      WHERE reg_no = ?
                      AND status = 'Unread'";

$stmt_notifications = mysqli_prepare($conn, $sql_notifications);

if ($stmt_notifications) {

    mysqli_stmt_bind_param(
        $stmt_notifications,
        "s",
        $reg_no
    );

    mysqli_stmt_execute($stmt_notifications);

    $result_notifications =
        mysqli_stmt_get_result($stmt_notifications);

    if ($result_notifications) {

        $notification_data =
            mysqli_fetch_assoc($result_notifications);

        $unread_count =
            $notification_data['unread_count'] ?? 0;
    }

    mysqli_stmt_close($stmt_notifications);
}


/* ==============================
   GET STUDENT NAME
   ============================== */

$student_name = "Student";

$sql = "SELECT student_name
        FROM student
        WHERE reg_no = ?";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "s", $reg_no);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $student = mysqli_fetch_assoc($result);

        $student_name = $student['student_name'];
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Student Dashboard | Digital Internship Management System
    </title>


    <!-- Elegant Fonts -->

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


        /* =====================================
           MAIN LAYOUT
        ===================================== */

        .dashboard-layout {

            min-height: 100vh;

            display: flex;
        }


        /* =====================================
           SIDEBAR
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
        }


        /* Chrome Scrollbar */

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
           SIDEBAR HEADER
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

            transition: all 0.25s ease;
        }


        .sidebar-menu a:hover {

            background: rgba(198,161,91,0.17);

            color: #ffffff;

            padding-left: 18px;
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


        /* =====================================
           LOGOUT
        ===================================== */

        .sidebar-bottom {

            padding: 18px 15px;

            border-top: 1px solid rgba(198,161,91,0.40);

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

            transition: 0.3s;
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

            display: flex;

            flex-direction: column;
        }


        /* =====================================
           TOP HEADER
        ===================================== */

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

            letter-spacing: 0.3px;
        }


        /* =====================================
           WELCOME CARD
        ===================================== */

        .welcome-wrapper {

            width: 88%;

            max-width: 1050px;

            margin: 65px auto 0;
        }


        .welcome-card {

            position: relative;

            background: #fffdf9;

            border: 1px solid #d9d0c3;

            border-radius: 7px;

            min-height: 310px;

            display: flex;

            align-items: center;

            padding: 45px 70px;

            box-shadow:
                0 12px 30px rgba(36,52,71,0.08);

            overflow: hidden;
        }


        /* Inner Border */

        .welcome-card::before {

            content: "";

            position: absolute;

            top: 12px;

            left: 12px;

            right: 12px;

            bottom: 12px;

            border: 1px solid #eee5d8;

            pointer-events: none;
        }


        /* Gold Corner */

        .welcome-card::after {

            content: "";

            position: absolute;

            top: 0;

            left: 0;

            width: 30px;

            height: 30px;

            background: #c6a15b;

            clip-path: polygon(0 0, 100% 0, 0 100%);
        }


        /* =====================================
           WELCOME CONTENT
        ===================================== */

        .welcome-content {

            position: relative;

            z-index: 2;

            width: 100%;
        }


        /* Dashboard Badge */

        .dashboard-badge {

            display: inline-block;

            background: #243447;

            color: #f3d99b;

            padding: 11px 23px;

            border-radius: 4px;

            font-family: 'Poppins', sans-serif;

            font-size: 11px;

            font-weight: 600;

            letter-spacing: 1.5px;

            text-transform: uppercase;

            box-shadow: 0 4px 10px rgba(36,52,71,0.15);

            margin-bottom: 25px;
        }


        /* Welcome */

        .welcome-label {

            margin: 0 0 5px;

            font-family: 'Cinzel', serif;

            font-size: 27px;

            color: #243447;

            font-weight: 500;
        }


        /* Student Name */

        .student-name {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 39px;

            font-weight: 600;

            color: #a47b32;

            letter-spacing: 0.5px;
        }


        /* Gold Line */

        .gold-line {

            width: 75px;

            height: 2px;

            background: #c6a15b;

            margin: 17px 0 16px;
        }


        /* Quote */

        .student-quote {

            margin: 0;

            color: #707982;

            font-family: Georgia, "Times New Roman", serif;

            font-size: 15px;

            font-style: italic;

            letter-spacing: 0.2px;
        }


        /* =====================================
           FOOTER
        ===================================== */

        .dashboard-footer {

            margin-top: auto;

            padding: 17px;

            text-align: center;

            background: #eee9e1;

            border-top: 1px solid #d8d0c4;

            color: #70777e;

            font-size: 11px;

            letter-spacing: 0.3px;
        }


        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 800px) {

            .sidebar {

                width: 215px;
            }


            .main-content {

                margin-left: 215px;

                width: calc(100% - 215px);
            }


            .top-header {

                padding: 0 25px;
            }


            .system-name {

                display: none;
            }


            .welcome-card {

                padding: 40px;
            }

        }


        @media (max-width: 650px) {

            .dashboard-layout {

                display: block;
            }


            .sidebar {

                position: relative;

                width: 100%;

                height: auto;

                min-height: auto;
            }


            .main-content {

                margin-left: 0;

                width: 100%;
            }


            .top-header {

                padding: 0 20px;
            }


            .welcome-wrapper {

                margin-top: 40px;
            }


            .welcome-card {

                padding: 35px 25px;

                min-height: 270px;
            }


            .student-name {

                font-size: 30px;
            }


            .welcome-label {

                font-size: 23px;
            }

        }

    </style>

</head>


<body>


<div class="dashboard-layout">


    <!-- =====================================
         SIDEBAR
    ===================================== -->

    <aside class="sidebar">


        <div class="sidebar-title">

            <h2>
                Student Portal
            </h2>

            <p>
                Internship Management
            </p>

        </div>


        <nav class="sidebar-menu">

            <div class="menu-title">
                Main Menu
            </div>


            <a href="dashboard.php"
               class="active">

                <span class="menu-icon">⌂</span>

                <span>Dashboard</span>

            </a>


            <a href="profile.php">

                <span class="menu-icon">👤</span>

                <span>My Profile</span>

            </a>


            <a href="internship.php">

                <span class="menu-icon">🏢</span>

                <span>Internship Details</span>

            </a>


            <a href="status.php">

                <span class="menu-icon">📊</span>

                <span>Internship Status</span>

            </a>


           <a href="meeting.php">
    <span class="menu-icon">💻</span>
    <span>Online Meeting</span>
</a>


            <a href="report.php">

                <span class="menu-icon">📄</span>

                <span>Internship Report</span>

            </a>


            <a href="certificate.php">

                <span class="menu-icon">🎓</span>

                <span>Certificate</span>

            </a>

        </nav>


        <div class="sidebar-bottom">

            <a href="logout.php"
               class="logout-link">

                <span>↪</span>

                Logout

            </a>

        </div>


    </aside>



    <!-- =====================================
         MAIN CONTENT
    ===================================== -->

    <main class="main-content">


        <!-- Top Header -->

        <header class="top-header">

    <h1>
        Student Dashboard
    </h1>

    <div>

        <a href="notifications.php"
           style="text-decoration:none; color:#243447; font-size:20px;">

            🔔

            <?php
            if ($unread_count > 0) {
                echo " <span style='color:red; font-size:13px; font-weight:600;'>(" 
                     . $unread_count . ")</span>";
            }
            ?>

        </a>

        <span class="system-name"
              style="margin-left:25px;">
            Digital Internship Management System
        </span>

    </div>

</header>


        <!-- Welcome -->

        <section class="welcome-wrapper">

            <div class="welcome-card">


                <div class="welcome-content">


                    <!-- Dashboard Box -->

                    <div class="dashboard-badge">

                        Student Dashboard

                    </div>


                    <!-- Welcome -->

                    <p class="welcome-label">

                        Welcome,

                    </p>


                    <!-- Student Name -->

                    <h2 class="student-name">

                        <?php
                        echo htmlspecialchars($student_name);
                        ?>

                    </h2>


                    <!-- Divider -->

                    <div class="gold-line"></div>


                    <!-- Quote -->

                    <p class="student-quote">

                        “Every step you take today shapes your future tomorrow.”

                    </p>


                </div>


            </div>

        </section>



        <!-- Footer -->

        <footer class="dashboard-footer">

            Digital Internship Management System © 2026

        </footer>


    </main>


</div>


</body>

</html>