<?php

session_start();
include "db_connect.php";

/* ==============================
   LOGIN CHECK
   ============================== */

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];


/* ==============================
   STUDENT NAME
   ============================== */

$student_name = "Student";

$sql = "
    SELECT student_name
    FROM student
    WHERE reg_no = ?
";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $reg_no
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $student_name = $row['student_name'];
    }

    mysqli_stmt_close($stmt);
}


/* ==============================
   FIND APPROVED INTERNSHIP
   ============================== */

$approved_internship_id = 0;

$sql_approved = "
    SELECT internship_id
    FROM internship_applications
    WHERE reg_no = ?
    AND LOWER(status) = 'approved'
    ORDER BY application_id DESC
    LIMIT 1
";

$stmt_approved = mysqli_prepare($conn, $sql_approved);

if ($stmt_approved) {

    mysqli_stmt_bind_param(
        $stmt_approved,
        "s",
        $reg_no
    );

    mysqli_stmt_execute($stmt_approved);

    $result_approved = mysqli_stmt_get_result($stmt_approved);

    if ($approved_row = mysqli_fetch_assoc($result_approved)) {
        $approved_internship_id = $approved_row['internship_id'];
    }

    mysqli_stmt_close($stmt_approved);
}


/* ==============================
   DEFAULT MEETING VALUES
   ============================== */

$meeting_title = "";
$meeting_message = "";
$meeting_link = "";

$meeting_id = 0;
$meeting_internship_id = 0;


/* ==============================
   CHECK ACTIVE MEETING
   FOR APPROVED INTERNSHIP
   ============================== */

if ($approved_internship_id > 0) {

    $sql_meeting = "
        SELECT
            meeting_id,
            internship_id,
            meeting_title,
            meeting_message,
            meeting_link
        FROM online_meetings
        WHERE internship_id = ?
        AND LOWER(status) = 'active'
        ORDER BY meeting_id DESC
        LIMIT 1
    ";

    $stmt_meeting = mysqli_prepare(
        $conn,
        $sql_meeting
    );

    if ($stmt_meeting) {

        mysqli_stmt_bind_param(
            $stmt_meeting,
            "i",
            $approved_internship_id
        );

        mysqli_stmt_execute($stmt_meeting);

        $result_meeting = mysqli_stmt_get_result(
            $stmt_meeting
        );

        if ($meeting = mysqli_fetch_assoc($result_meeting)) {

            $meeting_id = $meeting['meeting_id'];

            $meeting_internship_id =
                $meeting['internship_id'];

            $meeting_title =
                $meeting['meeting_title'];

            $meeting_message =
                $meeting['meeting_message'];

            $meeting_link =
                $meeting['meeting_link'];
        }

        mysqli_stmt_close($stmt_meeting);
    }
}


/* ==============================
   CHECK WHETHER STUDENT ALREADY
   JOINED THIS INTERNSHIP MEETING
   ============================== */

$meeting_joined = false;

if (
    isset($_SESSION['meeting_joined_internship_id']) &&
    $meeting_internship_id > 0 &&
    $_SESSION['meeting_joined_internship_id'] == $meeting_internship_id
) {

    $meeting_joined = true;
}


/*
 * If the student already joined this internship's
 * meeting, hide the meeting details on refresh.
 */
if ($meeting_joined) {

    $meeting_title = "";
    $meeting_message = "";
    $meeting_link = "";
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Online Meeting | Student Portal
</title>


<!-- GOOGLE FONTS -->

<link
    rel="preconnect"
    href="https://fonts.googleapis.com"
>

<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin
>

<link
    href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
    rel="stylesheet"
>


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


.dashboard-layout {

    min-height: 100vh;

    display: flex;

}


/* ================= SIDEBAR ================= */

.sidebar {

    width: 255px;

    height: 100vh;

    background: #243447;

    position: fixed;

    left: 0;

    top: 0;

    display: flex;

    flex-direction: column;

    box-shadow:
        5px 0 18px rgba(36,52,71,0.15);

    overflow-y: auto;

    scrollbar-width: thin;

    scrollbar-color:
        #c6a15b #243447;

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

    border-bottom:
        1px solid rgba(198,161,91,0.40);

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

    background:
        rgba(198,161,91,.17);

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

    border-top:
        1px solid rgba(198,161,91,.4);

    flex-shrink: 0;

}


.logout-link {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    padding: 11px;

    border:
        1px solid #d8b66c;

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


/* ================= MAIN ================= */

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

    border-bottom:
        1px solid #ddd4c8;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 40px;

    box-shadow:
        0 3px 12px rgba(36,52,71,0.05);

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


/* ================= CONTENT ================= */

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


/* ================= MEETING CARD ================= */

.meeting-card {

    width: 100%;

    max-width: 900px;

    background: #fffdf9;

    border:
        1px solid #ddd4c8;

    border-radius: 6px;

    padding: 35px;

    box-shadow:
        0 8px 25px rgba(36,52,71,0.08);

}


.meeting-card h3 {

    margin: 0 0 25px;

    font-family: 'Cinzel', serif;

    font-size: 22px;

    color: #243447;

}


/* ================= MESSAGE ================= */

.message-title {

    font-size: 10px;

    color: #777;

    text-transform: uppercase;

    letter-spacing: 1.2px;

    margin-bottom: 10px;

}


.message-box {

    background: #f8f3eb;

    border-left:
        3px solid #c6a15b;

    padding: 20px;

    border-radius: 3px;

    margin-bottom: 25px;

}


.message-box p {

    margin: 0;

    color: #4b3a2f;

    font-size: 13px;

    line-height: 1.8;

}


/* ================= LINK ================= */

.link-title {

    font-size: 10px;

    color: #777;

    text-transform: uppercase;

    letter-spacing: 1.2px;

    margin-bottom: 10px;

}


.meeting-link-box {

    background: #fff;

    border:
        1px solid #ddd4c8;

    padding: 15px;

    border-radius: 4px;

    margin-bottom: 25px;

    word-break: break-all;

}


.meeting-link-box a {

    color: #243447;

    font-size: 13px;

    text-decoration: none;

}


.meeting-link-box a:hover {

    color: #c6a15b;

}


/* ================= JOIN BUTTON ================= */

.join-button {

    display: inline-block;

    padding: 13px 25px;

    background: #243447;

    color: #fff;

    text-decoration: none;

    border-radius: 4px;

    font-size: 13px;

}


.join-button:hover {

    background: #c6a15b;

    color: #243447;

}


/* ================= JOINED ================= */

.joined-box {

    padding: 15px 18px;

    background: #f8f3eb;

    border-left:
        3px solid #c6a15b;

    border-radius: 4px;

    color: #4b3a2f;

    font-size: 13px;

}


/* ================= NO MEETING ================= */

.no-meeting {

    padding: 25px;

    background: #f8f3eb;

    border-left:
        3px solid #c6a15b;

    border-radius: 4px;

    color: #66717c;

    font-size: 13px;

}


/* ================= MOBILE ================= */

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

        padding: 30px 20px;

    }

}

</style>

</head>


<body>


<div class="dashboard-layout">


<!-- ================= SIDEBAR ================= -->

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

            <span class="menu-icon">
                ⌂
            </span>

            <span>
                Dashboard
            </span>

        </a>


        <a href="profile.php">

            <span class="menu-icon">
                👤
            </span>

            <span>
                My Profile
            </span>

        </a>


        <a href="internship.php">

            <span class="menu-icon">
                🏢
            </span>

            <span>
                Internship Details
            </span>

        </a>


        <a href="status.php">

            <span class="menu-icon">
                📊
            </span>

            <span>
                Internship Status
            </span>

        </a>


        <a
            href="meeting.php"
            class="active"
        >

            <span class="menu-icon">
                💻
            </span>

            <span>
                Online Meeting
            </span>

        </a>


        <?php if ($meeting_joined): ?>

        <a href="attendance.php">

            <span class="menu-icon">
                📅
            </span>

            <span>
                Attendance
            </span>

        </a>

        <?php endif; ?>


        <a href="report.php">

            <span class="menu-icon">
                📄
            </span>

            <span>
                Internship Report
            </span>

        </a>


        <a href="certificate.php">

            <span class="menu-icon">
                🎓
            </span>

            <span>
                Certificate
            </span>

        </a>


    </nav>


    <div class="sidebar-bottom">

        <a
            href="logout.php"
            class="logout-link"
        >

            <span>
                ↪
            </span>

            Logout

        </a>

    </div>


</aside>


<!-- ================= MAIN ================= -->

<main class="main-content">


    <header class="top-header">

        <h1>
            Online Meeting
        </h1>

        <div class="system-name">

            Digital Internship Management System

        </div>

    </header>


    <section class="page-content">


        <div class="welcome">

            <h2>

                Welcome,
                <?php
                echo htmlspecialchars(
                    $student_name
                );
                ?>

            </h2>

            <p>

                Check the latest meeting information provided by your administrator.

            </p>

        </div>


        <div class="meeting-card">


            <?php if ($meeting_title != "" && !$meeting_joined): ?>


                <h3>

                    <?php
                    echo htmlspecialchars(
                        $meeting_title
                    );
                    ?>

                </h3>


                <!-- MESSAGE -->

                <div class="message-title">

                    Meeting Message

                </div>


                <div class="message-box">

                    <p>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $meeting_message
                            )
                        );
                        ?>

                    </p>

                </div>


                <!-- MEETING LINK -->

                <div class="link-title">

                    Meeting Link

                </div>


                <div class="meeting-link-box">

                    <a
                        href="<?php echo htmlspecialchars($meeting_link); ?>"
                        target="_blank"
                    >

                        <?php
                        echo htmlspecialchars(
                            $meeting_link
                        );
                        ?>

                    </a>

                </div>


                <?php if (!$meeting_joined): ?>


                    <a
                        href="join_meeting.php"
                        class="join-button"
                    >

                        💻 Join Meeting

                    </a>


                <?php else: ?>


                    <div class="joined-box">

                        ✓ You have joined the meeting.

                        You can now proceed to Attendance.

                    </div>


                <?php endif; ?>


            <?php else: ?>


                <div class="no-meeting">

                    <?php if ($meeting_joined): ?>

                        <strong>Meeting Completed</strong><br><br>

                        You have already attended the online meeting
                        for your approved internship.

                        <br><br>

                        No active meeting is available for you at this time.

                    <?php elseif ($approved_internship_id == 0): ?>

                        <strong>No Active Internship</strong><br><br>

                        You do not have an approved internship
                        with an active online meeting.

                    <?php else: ?>

                        <strong>No Active Meeting</strong><br><br>

                        Your internship application has been approved,
                        but the administrator has not provided an
                        online meeting link yet.

                        <br><br>

                        Please check again after the administrator
                        sends the meeting details.

                    <?php endif; ?>

                </div>


            <?php endif; ?>


        </div>


    </section>


</main>


</div>


</body>

</html>