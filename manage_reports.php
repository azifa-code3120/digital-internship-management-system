<?php
include "db_connect.php";

$message = "";

/* =========================
   APPROVE / REJECT REPORT
   ========================= */

if (isset($_GET['action']) && isset($_GET['report_id'])) {

    $report_id = intval($_GET['report_id']);
    $action = $_GET['action'];

    /* =========================================
       GET STUDENT REGISTER NUMBER FOR REPORT
       ========================================= */

    $sql_student = "SELECT reg_no
                    FROM internship_reports
                    WHERE report_id = ?";

    $stmt_student = mysqli_prepare($conn, $sql_student);

    $student_reg_no = "";

    if ($stmt_student) {

        mysqli_stmt_bind_param(
            $stmt_student,
            "i",
            $report_id
        );

        mysqli_stmt_execute($stmt_student);

        $result_student = mysqli_stmt_get_result($stmt_student);

        if ($result_student && mysqli_num_rows($result_student) > 0) {

            $student_data = mysqli_fetch_assoc($result_student);

            $student_reg_no = $student_data['reg_no'];
        }

        mysqli_stmt_close($stmt_student);
    }


    /* =========================
       APPROVE REPORT
       ========================= */

    if ($action == "approve") {

        $status = "Approved";

        $sql = "UPDATE internship_reports
                SET status = ?
                WHERE report_id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $status,
                $report_id
            );

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        }


        /* =========================================
           AUTOMATIC NOTIFICATION - APPROVED
           ========================================= */

        if (!empty($student_reg_no)) {

            $notification_message =
                "Your internship report has been approved.";

            $notification_status = "Unread";

            $sql_notification =
                "INSERT INTO notifications
                (reg_no, message, status)
                VALUES (?, ?, ?)";

            $stmt_notification =
                mysqli_prepare(
                    $conn,
                    $sql_notification
                );

            if ($stmt_notification) {

                mysqli_stmt_bind_param(
                    $stmt_notification,
                    "sss",
                    $student_reg_no,
                    $notification_message,
                    $notification_status
                );

                mysqli_stmt_execute(
                    $stmt_notification
                );

                mysqli_stmt_close(
                    $stmt_notification
                );
            }
        }


        header("Location: manage_reports.php");
        exit();
    }


    /* =========================
       REJECT REPORT
       ========================= */

    if ($action == "reject") {

        $status = "Rejected";

        $sql = "UPDATE internship_reports
                SET status = ?
                WHERE report_id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $status,
                $report_id
            );

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        }


        /* =========================================
           AUTOMATIC NOTIFICATION - REJECTED
           ========================================= */

        if (!empty($student_reg_no)) {

            $notification_message =
                "Your internship report has been rejected.";

            $notification_status = "Unread";

            $sql_notification =
                "INSERT INTO notifications
                (reg_no, message, status)
                VALUES (?, ?, ?)";

            $stmt_notification =
                mysqli_prepare(
                    $conn,
                    $sql_notification
                );

            if ($stmt_notification) {

                mysqli_stmt_bind_param(
                    $stmt_notification,
                    "sss",
                    $student_reg_no,
                    $notification_message,
                    $notification_status
                );

                mysqli_stmt_execute(
                    $stmt_notification
                );

                mysqli_stmt_close(
                    $stmt_notification
                );
            }
        }


        header("Location: manage_reports.php");
        exit();
    }
}


/* =========================
   FETCH REPORTS
   ========================= */

$sql = "SELECT
            report_id,
            reg_no,
            report_title,
            report_file,
            submission_date,
            status
        FROM internship_reports
        ORDER BY report_id DESC";

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

<title>View Reports</title>


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


/* =========================================
   BODY
   ========================================= */

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


/* scrollbar */

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


/* NO MOVEMENT ON HOVER */

.sidebar-menu a:hover {

    background: rgba(198,161,91,.17);

    color: #fff;

    padding-left: 14px;

    transform: none;
}


/* ACTIVE */

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


/* =========================================
   PAGE AREA
   ========================================= */

.page-area {

    padding: 38px 40px 50px;

    flex: 1;
}


/* =========================================
   REPORT CARD
   ========================================= */

.report-card {

    background: #fffdf9;

    border: 1px solid #e1d8cb;

    border-radius: 8px;

    padding: 30px;

    box-shadow:
        0 8px 25px rgba(36,52,71,0.07);
}


/* =========================================
   PAGE TITLE
   ========================================= */

.page-title-row {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 25px;
}


.page-title {

    margin: 0;

    font-family: 'Cinzel', serif;

    font-size: 27px;

    color: #243447;

    font-weight: 600;
}


.page-subtitle {

    margin: 6px 0 0;

    color: #7a8087;

    font-size: 12px;
}


/* =========================================
   GOLD LINE
   ========================================= */

.title-line {

    width: 55px;

    height: 2px;

    background: #c6a15b;

    margin-top: 10px;
}


/* =========================================
   TABLE
   ========================================= */

.table-wrapper {

    width: 100%;

    overflow-x: auto;
}


.report-table {

    width: 100%;

    border-collapse: collapse;

    min-width: 800px;
}


.report-table thead th {

    background: #243447;

    color: #f3d99b;

    font-family: 'Cinzel', serif;

    font-size: 13px;

    font-weight: 600;

    text-align: left;

    padding: 15px 14px;

    border-bottom: 2px solid #c6a15b;

    white-space: nowrap;
}


.report-table tbody td {

    padding: 16px 14px;

    border-bottom: 1px solid #e3ddd4;

    color: #354352;

    font-size: 13px;

    vertical-align: middle;
}


.report-table tbody tr:nth-child(even) {

    background: #faf7f1;
}


.report-table tbody tr:hover {

    background: #f5efe5;
}


/* =========================================
   REGISTER NUMBER
   ========================================= */

.reg-number {

    font-weight: 600;

    color: #4b3a2f;
}


/* =========================================
   REPORT TITLE
   ========================================= */

.report-title {

    font-weight: 500;

    color: #243447;

    max-width: 250px;
}


/* =========================================
   STATUS
   ========================================= */

.status-badge {

    display: inline-block;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: 600;

    letter-spacing: .3px;
}


.status-pending {

    background: #f1dfb5;

    color: #6b4f18;
}


.status-approved {

    background: #dcebdc;

    color: #315c38;
}


.status-rejected {

    background: #f2d5d5;

    color: #8a2020;
}


/* =========================================
   VIEW BUTTON
   ========================================= */

.view-btn {

    display: inline-block;

    padding: 9px 15px;

    background: #6b5142;

    color: white;

    text-decoration: none;

    border-radius: 4px;

    font-size: 12px;

    font-weight: 600;

    transition: none;
}


.view-btn:hover {

    background: #4b3a2f;

    color: white;
}


/* =========================================
   ACTION BUTTONS
   ========================================= */

.action-area {

    display: flex;

    align-items: center;

    gap: 7px;

    flex-wrap: wrap;
}


.approve-btn {

    display: inline-block;

    padding: 9px 13px;

    background: #6b5142;

    color: white;

    text-decoration: none;

    border-radius: 4px;

    font-size: 12px;

    font-weight: 600;
}


.approve-btn:hover {

    background: #4b3a2f;

    color: white;
}


.reject-btn {

    display: inline-block;

    padding: 9px 13px;

    background: #a00000;

    color: white;

    text-decoration: none;

    border-radius: 4px;

    font-size: 12px;

    font-weight: 600;
}


.reject-btn:hover {

    background: #7d0000;

    color: white;
}


/* =========================================
   EMPTY STATE
   ========================================= */

.empty-state {

    text-align: center;

    padding: 55px 20px;

    color: #777;
}


.empty-icon {

    font-size: 38px;

    margin-bottom: 12px;

    opacity: .7;
}


.empty-state h3 {

    margin: 0 0 6px;

    font-family: 'Cinzel', serif;

    color: #4b3a2f;

    font-size: 18px;
}


.empty-state p {

    margin: 0;

    font-size: 13px;

    color: #888;
}


/* =========================================
   BACK BUTTON
   ========================================= */

.back-area {

    margin-top: 25px;
}


.back-btn {

    display: inline-block;

    padding: 11px 18px;

    background: #243447;

    color: white;

    text-decoration: none;

    border-radius: 4px;

    font-size: 12px;

    font-weight: 500;

    border: 1px solid #243447;

    transition: none;
}


.back-btn:hover {

    background: #c6a15b;

    color: #243447;
}


/* =========================================
   RESPONSIVE
   ========================================= */

@media (max-width: 900px) {

    .page-area {

        padding: 25px 20px;
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


<!-- ==================================================
     ADMIN SIDEBAR
     ================================================== -->

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


        <a href="manage_students.php">

            <span class="menu-icon">👥</span>

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


        <a href="manage_reports.php"
           class="active">

            <span class="menu-icon">📄</span>

            <span>View Reports</span>

        </a>


        <a href="admin_attendance.php">

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


    <div class="sidebar-bottom">

        <a href="index.php"
           class="logout-link">

            <span>↪</span>

            Logout

        </a>

    </div>


</aside>



<!-- ==================================================
     RIGHT SIDE
     ================================================== -->

<main class="main-content">


    <!-- TOP HEADER -->

    <header class="top-header">

        <h1>
            View Reports
        </h1>


        <div class="system-name">

            Digital Internship Management System

        </div>

    </header>



    <!-- PAGE CONTENT -->

    <section class="page-area">


        <div class="report-card">


            <!-- TITLE -->

            <div class="page-title-row">

                <div>

                    <h2 class="page-title">
                        Internship Reports
                    </h2>

                    <div class="title-line"></div>

                    <p class="page-subtitle">
                        Review and manage submitted internship reports
                    </p>

                </div>

            </div>



            <?php if (mysqli_num_rows($result) > 0) { ?>


            <!-- TABLE -->

            <div class="table-wrapper">

                <table class="report-table">


                    <thead>

                        <tr>

                            <th>
                                Register Number
                            </th>

                            <th>
                                Report Title
                            </th>

                            <th>
                                Submission Date
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Report
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php while ($report = mysqli_fetch_assoc($result)) { ?>


                        <tr>


                            <!-- REGISTER NUMBER -->

                            <td>

                                <span class="reg-number">

                                    <?php
                                    echo htmlspecialchars(
                                        $report['reg_no']
                                    );
                                    ?>

                                </span>

                            </td>



                            <!-- REPORT TITLE -->

                            <td>

                                <div class="report-title">

                                    <?php
                                    echo htmlspecialchars(
                                        $report['report_title']
                                    );
                                    ?>

                                </div>

                            </td>



                            <!-- SUBMISSION DATE -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $report['submission_date']
                                );

                                ?>

                            </td>



                            <!-- STATUS -->

                            <td>


                                <?php

                                $status =
                                    $report['status'];

                                if ($status == "Approved") {

                                    echo '<span class="status-badge status-approved">
                                            Approved
                                          </span>';

                                }

                                elseif ($status == "Rejected") {

                                    echo '<span class="status-badge status-rejected">
                                            Rejected
                                          </span>';

                                }

                                else {

                                    echo '<span class="status-badge status-pending">
                                            Pending
                                          </span>';

                                }

                                ?>


                            </td>



                            <!-- VIEW REPORT -->

                            <td>

                                <a
                                    href="uploads/<?php
                                    echo urlencode(
                                        $report['report_file']
                                    );
                                    ?>"
                                    target="_blank"
                                    class="view-btn"
                                >

                                    View

                                </a>

                            </td>



                            <!-- ACTION -->

                            <td>

                                <div class="action-area">


                                <?php if ($status != "Approved") { ?>

                                    <a
                                        href="manage_reports.php?action=approve&report_id=<?php echo $report['report_id']; ?>"
                                        class="approve-btn"
                                        onclick="return confirm('Approve this report?');"
                                    >

                                        Approve

                                    </a>

                                <?php } ?>


                                <?php if ($status != "Rejected") { ?>

                                    <a
                                        href="manage_reports.php?action=reject&report_id=<?php echo $report['report_id']; ?>"
                                        class="reject-btn"
                                        onclick="return confirm('Reject this report?');"
                                    >

                                        Reject

                                    </a>

                                <?php } ?>


                                </div>

                            </td>


                        </tr>


                    <?php } ?>


                    </tbody>

                </table>

            </div>


            <?php } else { ?>


                <!-- EMPTY -->

                <div class="empty-state">

                    <div class="empty-icon">
                        📄
                    </div>

                    <h3>
                        No Internship Reports
                    </h3>

                    <p>
                        No student reports have been submitted yet.
                    </p>

                </div>


            <?php } ?>



            <!-- BACK BUTTON -->

            <div class="back-area">

                <a
                    href="admin_dashboard.php"
                    class="back-btn"
                >

                    ← Back to Admin Dashboard

                </a>

            </div>


        </div>


    </section>


</main>


</div>


</body>

</html>