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
   GET ALL INTERNSHIPS
============================== */

$sql = "SELECT internship_id,
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
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Internship Details | Digital Internship Management System
    </title>


    <!-- SAME FONTS AS DASHBOARD -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
          rel="stylesheet">


    <style>

        /* =====================================
           BASIC
        ===================================== */

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
           SAME AS DASHBOARD
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
           SAME AS DASHBOARD
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
           SAME AS DASHBOARD
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


        /* =====================================
           ACTIVE = INTERNSHIP DETAILS
        ===================================== */

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
           SAME AS DASHBOARD
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
           INTERNSHIP CONTENT
        ===================================== */

        .internship-wrapper {

            width: 88%;

            max-width: 1050px;

            margin: 40px auto 50px;
        }


        .page-intro {

            margin-bottom: 25px;
        }


        .page-intro h2 {

            margin: 0 0 5px;

            font-family: 'Cinzel', serif;

            font-size: 30px;

            color: #243447;

            font-weight: 600;
        }


        .page-intro p {

            margin: 0;

            color: #707982;

            font-size: 13px;
        }


        /* =====================================
           INTERNSHIP CARD
        ===================================== */

        .internship-card {

            background: #fffdf9;

            border: 1px solid #d9d0c3;

            border-top: 5px solid #c6a15b;

            margin-bottom: 25px;

            box-shadow: 0 10px 25px rgba(36,52,71,0.07);
        }


        /* =====================================
           COMPANY HEADER
        ===================================== */

        .company-header {

            padding: 27px 35px;

            border-bottom: 1px solid #ddd4c8;
        }


        .company-label {

            margin: 0 0 6px;

            color: #a47b32;

            font-size: 10px;

            font-weight: 600;

            letter-spacing: 1.7px;

            text-transform: uppercase;
        }


        .company-name {

            margin: 0 0 5px;

            font-family: 'Cinzel', serif;

            font-size: 27px;

            color: #243447;

            font-weight: 600;
        }


        .domain {

            margin: 0;

            color: #536273;

            font-size: 13px;
        }


        .domain strong {

            color: #243447;
        }


        /* =====================================
           DETAILS
        ===================================== */

        .details-area {

            padding: 0 35px;
        }


        .detail-row {

            display: flex;

            align-items: center;

            min-height: 58px;

            border-bottom: 1px solid #e4ddd3;
        }


        .detail-row:last-child {

            border-bottom: none;
        }


        .detail-label {

            width: 220px;

            color: #a47b32;

            font-size: 10px;

            font-weight: 600;

            letter-spacing: 1.5px;

            text-transform: uppercase;
        }


        .detail-value {

            flex: 1;

            color: #243447;

            font-size: 13px;
        }


        /* =====================================
           STATUS
        ===================================== */

        .status-badge {

            display: inline-block;

            padding: 9px 18px;

            background: #c6a15b;

            color: #243447;

            border-radius: 4px;

            font-size: 12px;

            font-weight: 600;
        }


        /* =====================================
           APPLY BUTTON
        ===================================== */

        .apply-area {

            padding: 22px 35px;

            border-top: 1px solid #ddd4c8;

            text-align: right;
        }


        .apply-btn {

            display: inline-block;

            padding: 11px 25px;

            background: #243447;

            color: #f3d99b;

            text-decoration: none;

            border-radius: 4px;

            font-size: 12px;

            font-weight: 600;

            letter-spacing: 0.5px;
        }


        .apply-btn:hover {

            background: #c6a15b;

            color: #243447;
        }


        /* =====================================
           NO INTERNSHIP
        ===================================== */

        .no-internship {

            background: #fffdf9;

            border: 1px solid #d9d0c3;

            padding: 45px;

            text-align: center;

            color: #707982;

            font-size: 14px;
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


            .internship-wrapper {

                width: 90%;
            }


            .detail-label {

                width: 170px;
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


            .internship-wrapper {

                margin-top: 30px;

                width: 90%;
            }


            .company-header {

                padding: 25px;
            }


            .details-area {

                padding: 0 25px;
            }


            .detail-row {

                display: block;

                padding: 15px 0;
            }


            .detail-label {

                width: 100%;

                margin-bottom: 7px;
            }


            .apply-area {

                padding: 20px 25px;

                text-align: center;
            }

        }

    </style>

</head>


<body>


<div class="dashboard-layout">


    <!-- =====================================
         SIDEBAR
         EXACT DASHBOARD SIDEBAR
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


            <a href="dashboard.php">

                <span class="menu-icon">⌂</span>

                <span>Dashboard</span>

            </a>


            <a href="profile.php">

                <span class="menu-icon">👤</span>

                <span>My Profile</span>

            </a>


            <!-- ACTIVE PAGE -->

            <a href="internship.php"
               class="active">

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


        <!-- TOP HEADER -->

        <header class="top-header">

            <h1>
                Internship Details
            </h1>

            <span class="system-name">
                Digital Internship Management System
            </span>

        </header>



        <!-- INTERNSHIP CONTENT -->

        <section class="internship-wrapper">


            <div class="page-intro">

                <h2>
                    Available Internships
                </h2>

                <p>
                    Explore the internship opportunities available for students.
                </p>

            </div>



            <?php if (mysqli_num_rows($result) > 0): ?>


                <?php while ($row = mysqli_fetch_assoc($result)): ?>


                    <div class="internship-card">


                        <!-- COMPANY -->

                        <div class="company-header">

                            <p class="company-label">
                                Internship Company
                            </p>

                            <h2 class="company-name">

                                <?php
                                echo htmlspecialchars($row['company_name']);
                                ?>

                            </h2>

                            <p class="domain">

                                <strong>Domain:</strong>

                                <?php
                                echo htmlspecialchars($row['internship_domain']);
                                ?>

                            </p>

                        </div>



                        <!-- DETAILS -->

                        <div class="details-area">


                            <div class="detail-row">

                                <div class="detail-label">
                                    Description
                                </div>

                                <div class="detail-value">

                                    <?php
                                    echo htmlspecialchars($row['description']);
                                    ?>

                                </div>

                            </div>


                            <div class="detail-row">

                                <div class="detail-label">
                                    Start Date
                                </div>

                                <div class="detail-value">

                                    <?php
                                    echo htmlspecialchars($row['start_date']);
                                    ?>

                                </div>

                            </div>


                            <div class="detail-row">

                                <div class="detail-label">
                                    End Date
                                </div>

                                <div class="detail-value">

                                    <?php
                                    echo htmlspecialchars($row['end_date']);
                                    ?>

                                </div>

                            </div>


                            <div class="detail-row">

                                <div class="detail-label">
                                    Guide Name
                                </div>

                                <div class="detail-value">

                                    <?php
                                    echo htmlspecialchars($row['guide_name']);
                                    ?>

                                </div>

                            </div>


                            <div class="detail-row">

                                <div class="detail-label">
                                    Status
                                </div>

                                <div class="detail-value">

                                    <span class="status-badge">

                                        <?php
                                        echo htmlspecialchars($row['status']);
                                        ?>

                                    </span>

                                </div>

                            </div>


                        </div>



                        <!-- APPLY -->

                        <div class="apply-area">

                            <a class="apply-btn"
                               href="apply.php?internship_id=<?php echo $row['internship_id']; ?>">

                                Apply Now

                            </a>

                        </div>


                    </div>


                <?php endwhile; ?>


            <?php else: ?>


                <div class="no-internship">

                    No internships are currently available.

                </div>


            <?php endif; ?>


        </section>



        <!-- FOOTER -->

        <footer class="dashboard-footer">

            Digital Internship Management System © 2026

        </footer>


    </main>


</div>


</body>

</html>