<?php

session_start();

include "db_connect.php";

date_default_timezone_set("Asia/Kolkata");


/* ==============================
   STUDENT LOGIN CHECK
   ============================== */

if (!isset($_SESSION['reg_no'])) {

    header("Location: login.php");
    exit();

}

$reg_no = $_SESSION['reg_no'];


/* ==============================
   MEETING COMPLETION CHECK
   ============================== */

$meeting_end_datetime = "2026-09-14 19:00:00";

$current_time = date("Y-m-d H:i:s");


if ($current_time < $meeting_end_datetime) {

    header("Location: meeting.php");
    exit();

}


/* ==============================
   GET STUDENT DETAILS
   ============================== */

$student_name = "Student";
$department = "";

$sql = "SELECT student_name, department
        FROM student
        WHERE reg_no = ?";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "s", $reg_no);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        $student_name = $row['student_name'];

        $department = $row['department'];

    }

    mysqli_stmt_close($stmt);
}


/* ==============================
   ATTENDANCE MESSAGE
   ============================== */

$message = "";

$attendance_marked = false;


/* ==============================
   CHECK TODAY'S ATTENDANCE
   ============================== */

$today = date("Y-m-d");

$check_sql = "SELECT *
              FROM attendance
              WHERE reg_no = ?
              AND attendance_date = ?";

$check_stmt = mysqli_prepare($conn, $check_sql);

if ($check_stmt) {

    mysqli_stmt_bind_param(
        $check_stmt,
        "ss",
        $reg_no,
        $today
    );

    mysqli_stmt_execute($check_stmt);

    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {

        $attendance_marked = true;

    }

    mysqli_stmt_close($check_stmt);
}


/* ==============================
   MARK ATTENDANCE
   ============================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($attendance_marked) {

        $message = "Attendance already marked for today.";

    } else {

        $status = "Present";

        $insert_sql = "INSERT INTO attendance
                       (reg_no, attendance_date, status)
                       VALUES (?, ?, ?)";

        $insert_stmt = mysqli_prepare($conn, $insert_sql);

        if ($insert_stmt) {

            mysqli_stmt_bind_param(
                $insert_stmt,
                "sss",
                $reg_no,
                $today,
                $status
            );

            if (mysqli_stmt_execute($insert_stmt)) {

                $message = "Attendance marked successfully!";

                $attendance_marked = true;

            } else {

                $message = "Attendance could not be marked.";

            }

            mysqli_stmt_close($insert_stmt);

        } else {

            $message = "Database error while marking attendance.";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Attendance | Student Portal</title>


<link rel="preconnect"
      href="https://fonts.googleapis.com">

<link rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
      rel="stylesheet">


<style>

/* ==============================
   BASIC
   ============================== */

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    background: #f7f3ec;

    color: #243447;

    font-family: 'Poppins', sans-serif;

}


/* ==============================
   LAYOUT
   ============================== */

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

    max-width: 900px;

    background: #fffdf9;

    border: 1px solid #ddd4c8;

    border-radius: 6px;

    padding: 35px;

    box-shadow: 0 8px 25px rgba(36,52,71,0.08);

}


.attendance-card h3 {

    margin: 0 0 25px;

    font-family: 'Cinzel', serif;

    font-size: 22px;

    color: #243447;

}


/* ==============================
   INFO BOX
   ============================== */

.info-box {

    background: #f8f3eb;

    border-left: 3px solid #c6a15b;

    padding: 20px;

    margin-bottom: 25px;

}


.info-row {

    display: flex;

    margin-bottom: 12px;

    font-size: 13px;

}


.info-row:last-child {

    margin-bottom: 0;

}


.info-label {

    width: 160px;

    color: #66717c;

}


.info-value {

    color: #243447;

    font-weight: 500;

}


/* ==============================
   MESSAGE
   ============================== */

.message {

    background: #f8f3eb;

    border-left: 3px solid #c6a15b;

    padding: 15px 18px;

    margin-bottom: 25px;

    color: #4b3a2f;

    font-size: 13px;

}


/* ==============================
   BUTTON
   ============================== */

.attendance-button {

    border: none;

    background: #c6a15b;

    color: #243447;

    padding: 13px 25px;

    border-radius: 4px;

    font-family: 'Poppins', sans-serif;

    font-size: 13px;

    font-weight: 600;

    cursor: pointer;

}


.attendance-button:hover {

    background: #c6a15b;

    color: #243447;

}


.completed-text {

    color: #4b3a2f;

    font-size: 13px;

    font-weight: 500;

}


/* ==============================
   BACK BUTTON
   ============================== */

.back-button {

    display: inline-block;

    margin-top: 20px;

    padding: 11px 20px;

    background: #243447;

    color: #fff;

    text-decoration: none;

    border-radius: 4px;

    font-size: 13px;

}


.back-button:hover {

    background: #243447;

    color: #fff;

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

        <h2>Student Portal</h2>

        <p>Internship Management</p>

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


        <a href="status.php">

            <span class="menu-icon">📊</span>

            <span>Internship Status</span>

        </a>


        <a href="meeting.php">

            <span class="menu-icon">💻</span>

            <span>Online Meeting</span>

        </a>


        <a href="attendance.php" class="active">

            <span class="menu-icon">📅</span>

            <span>Attendance</span>

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

        <a href="logout.php" class="logout-link">

            <span>↪</span>

            Logout

        </a>

    </div>


</aside>


<!-- ==============================
     MAIN CONTENT
     ============================== -->

<main class="main-content">


    <header class="top-header">

        <h1>Attendance</h1>

        <div class="system-name">

            Digital Internship Management System

        </div>

    </header>


    <section class="page-content">


        <div class="welcome">

            <h2>Internship Attendance</h2>

            <p>

                Mark your attendance after completing the online meeting.

            </p>

        </div>


        <div class="attendance-card">


            <h3>Meeting Attendance</h3>


            <?php if ($message != ""): ?>

                <div class="message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <div class="info-box">


                <div class="info-row">

                    <div class="info-label">
                        Student Name
                    </div>

                    <div class="info-value">

                        <?php echo htmlspecialchars($student_name); ?>

                    </div>

                </div>


                <div class="info-row">

                    <div class="info-label">
                        Register Number
                    </div>

                    <div class="info-value">

                        <?php echo htmlspecialchars($reg_no); ?>

                    </div>

                </div>


                <div class="info-row">

                    <div class="info-label">
                        Department
                    </div>

                    <div class="info-value">

                        <?php echo htmlspecialchars($department); ?>

                    </div>

                </div>


                <div class="info-row">

                    <div class="info-label">
                        Meeting
                    </div>

                    <div class="info-value">

                        Internship Online Meeting

                    </div>

                </div>


                <div class="info-row">

                    <div class="info-label">
                        Date
                    </div>

                    <div class="info-value">

                        <?php echo date("d-m-Y"); ?>

                    </div>

                </div>


                <div class="info-row">

                    <div class="info-label">
                        Attendance Status
                    </div>

                    <div class="info-value">

                        <?php

                        if ($attendance_marked) {

                            echo "Present";

                        } else {

                            echo "Not Marked";

                        }

                        ?>

                    </div>

                </div>


            </div>


            <?php if (!$attendance_marked): ?>


                <form method="POST">


                    <button
                        type="submit"
                        class="attendance-button"
                    >

                        ✓ Mark Attendance

                    </button>


                </form>


            <?php else: ?>


                <div class="completed-text">

                    ✓ Your attendance has already been marked successfully.

                </div>


                <a
                    href="report.php"
                    class="back-button"
                >

                    Continue to Internship Report

                </a>


            <?php endif; ?>


        </div>


    </section>


</main>


</div>


</body>

</html>