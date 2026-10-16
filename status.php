<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
   GET STUDENT APPLICATIONS
============================== */

$sql = "SELECT
            ia.application_id,
            ia.status,
            ia.application_date,
            i.company_name,
            i.internship_domain,
            i.start_date,
            i.end_date

        FROM internship_applications ia

        INNER JOIN internships i
            ON ia.internship_id = i.internship_id

        WHERE ia.reg_no = ?

        ORDER BY ia.application_id DESC";


$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    die("Database Error: " . mysqli_error($conn));
}


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

<title>
    Internship Status | Digital Internship Management System
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
   EXACT DASHBOARD STYLE
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
   EXACT DASHBOARD STYLE
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
   EXACT DASHBOARD STYLE
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


/* ACTIVE STATUS */

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
   EXACT DASHBOARD STYLE
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

    transition: all 0.25s ease;
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
   DASHBOARD STYLE
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
   STATUS CONTAINER
===================================== */

.status-container {

    width: 88%;

    max-width: 1050px;

    margin: 40px auto 50px;
}


/* =====================================
   PAGE HEADING
===================================== */

.page-heading {

    margin-bottom: 25px;
}


.page-heading h2 {

    margin: 0 0 5px;

    font-family: 'Cinzel', serif;

    font-size: 30px;

    color: #243447;

    font-weight: 600;
}


.page-heading p {

    margin: 0;

    color: #707982;

    font-size: 13px;
}


/* =====================================
   STATUS CARD
===================================== */

.status-card {

    background: #fffdf9;

    border: 1px solid #d9d0c3;

    border-radius: 7px;

    overflow: hidden;

    box-shadow: 0 10px 25px rgba(36,52,71,0.07);
}


/* GOLD TOP LINE */

.status-card::before {

    content: "";

    display: block;

    height: 5px;

    background: #c6a15b;
}


/* =====================================
   TABLE
===================================== */

.table-wrapper {

    width: 100%;

    overflow-x: auto;
}


.status-table {

    width: 100%;

    border-collapse: collapse;

    min-width: 850px;
}


/* =====================================
   TABLE HEADER
===================================== */

.status-table thead th {

    background: #243447;

    color: #f3d99b;

    padding: 16px 18px;

    text-align: left;

    font-family: 'Poppins', sans-serif;

    font-size: 11px;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: 0.8px;
}


/* =====================================
   TABLE BODY
===================================== */

.status-table tbody td {

    padding: 17px 18px;

    border-bottom: 1px solid #e1d9ce;

    color: #243447;

    font-family: 'Poppins', sans-serif;

    font-size: 13px;

    vertical-align: middle;
}


.status-table tbody tr:nth-child(even) {

    background: #faf7f1;
}


.status-table tbody tr:hover {

    background: #f1eadf;
}


.status-table tbody tr:last-child td {

    border-bottom: none;
}


/* =====================================
   COMPANY
===================================== */

.company-name {

    font-family: 'Cinzel', serif;

    font-size: 15px;

    font-weight: 600;

    color: #243447;
}


/* =====================================
   DOMAIN
===================================== */

.domain-name {

    color: #5f6871;

    font-size: 13px;
}


/* =====================================
   DATE
===================================== */

.date-value {

    white-space: nowrap;

    color: #4e5963;
}


/* =====================================
   STATUS BADGES
===================================== */

.status-badge {

    display: inline-block;

    padding: 8px 16px;

    border-radius: 4px;

    font-family: 'Poppins', sans-serif;

    font-size: 11px;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: 0.4px;
}


/* PENDING */

.pending {

    background: #f3d98b;

    color: #5a4438;
}


/* APPROVED */

.approved {

    background: #c8dfc4;

    color: #285c2a;
}


/* REJECTED */

.rejected {

    background: #e8b8b8;

    color: #7a0000;
}


/* =====================================
   FOOTER
===================================== */

.status-footer {

    padding: 20px 25px;

    background: #faf7f1;

    border-top: 1px solid #e1d9ce;
}


.back-btn {

    display: inline-block;

    padding: 11px 23px;

    background: #243447;

    color: #f3d99b;

    border: 1px solid #c6a15b;

    border-radius: 4px;

    text-decoration: none;

    font-family: 'Poppins', sans-serif;

    font-size: 13px;

    font-weight: 600;
}


.back-btn:hover {

    background: #c6a15b;

    color: #243447;
}


/* =====================================
   NO DATA
===================================== */

.no-data {

    padding: 70px 30px;

    text-align: center;
}


.no-data h3 {

    margin: 0;

    font-family: 'Cinzel', serif;

    color: #243447;

    font-size: 22px;
}


.no-data p {

    margin-top: 10px;

    color: #707982;

    font-size: 13px;
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


    .status-container {

        width: 90%;
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


    .status-container {

        width: 90%;

        margin-top: 30px;
    }

}

</style>

</head>


<body>


<div class="dashboard-layout">


<!-- =====================================
     SIDEBAR
     SAME AS DASHBOARD
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


        <a href="internship.php">

            <span class="menu-icon">🏢</span>

            <span>Internship Details</span>

        </a>


        <!-- ACTIVE PAGE -->

        <a href="status.php"
           class="active">

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
            Internship Status
        </h1>

        <span class="system-name">
            Digital Internship Management System
        </span>

    </header>



    <!-- STATUS CONTENT -->

    <section class="status-container">


        <div class="page-heading">

            <h2>
                Application Status
            </h2>

            <p>
                Track the status of your internship applications.
            </p>

        </div>



        <div class="status-card">


        <?php if ($result && mysqli_num_rows($result) > 0) { ?>


            <div class="table-wrapper">


                <table class="status-table">


                    <thead>

                        <tr>

                            <th>Company</th>

                            <th>Domain</th>

                            <th>Start Date</th>

                            <th>End Date</th>

                            <th>Application Date</th>

                            <th>Status</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>


                        <tr>


                            <td>

                                <div class="company-name">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['company_name']
                                    );
                                    ?>

                                </div>

                            </td>


                            <td>

                                <div class="domain-name">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['internship_domain']
                                    );
                                    ?>

                                </div>

                            </td>


                            <td>

                                <div class="date-value">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['start_date']
                                    );
                                    ?>

                                </div>

                            </td>


                            <td>

                                <div class="date-value">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['end_date']
                                    );
                                    ?>

                                </div>

                            </td>


                            <td>

                                <div class="date-value">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['application_date']
                                    );
                                    ?>

                                </div>

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

                                <?php

                                }

                                ?>


                            </td>


                        </tr>


                    <?php } ?>


                    </tbody>


                </table>


            </div>


        <?php } else { ?>


            <div class="no-data">

                <h3>
                    No Internship Applications
                </h3>

                <p>
                    You have not applied for any internship yet.
                </p>

            </div>


        <?php } ?>


            <div class="status-footer">


                <a href="dashboard.php"
                   class="back-btn">

                    ← Back to Dashboard

                </a>


            </div>


        </div>


    </section>


</main>


</div>


</body>

</html>