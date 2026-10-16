<?php

session_start();

include "db_connect.php";


/* =========================================
   ADMIN ACCESS CHECK
   ========================================= */

if (
    !isset($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true
) {

    header("Location: admin_login.php");
    exit();

}


$message = "";
$message_type = "";


/* =========================================
   ISSUE CERTIFICATE
   ========================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $internship_id = intval($_POST['internship_id'] ?? 0);


    /* =========================================
       BASIC VALIDATION
       ========================================= */

    if ($username == "" || $internship_id <= 0) {

        $message =
            "Please enter Student Username and Internship ID.";

        $message_type = "error";

    } else {


        /* =========================================
           FIND STUDENT USING USERNAME
           ========================================= */

        $sql_student = "
            SELECT
                reg_no,
                username,
                student_name,
                email
            FROM student
            WHERE username = ?
            LIMIT 1
        ";

        $stmt_student =
            mysqli_prepare(
                $conn,
                $sql_student
            );


        if (!$stmt_student) {

            $message =
                "Database error.";

            $message_type =
                "error";

        } else {

            mysqli_stmt_bind_param(
                $stmt_student,
                "s",
                $username
            );

            mysqli_stmt_execute(
                $stmt_student
            );

            $student_result =
                mysqli_stmt_get_result(
                    $stmt_student
                );


            if (
                mysqli_num_rows(
                    $student_result
                ) == 0
            ) {

                $message =
                    "Student Username not found.";

                $message_type =
                    "error";

            } else {


                $student =
                    mysqli_fetch_assoc(
                        $student_result
                    );

                $reg_no =
                    $student['reg_no'];


                /* =========================================
                   CHECK APPROVED APPLICATION
                   ========================================= */

                $sql_application = "
                    SELECT
                        ia.application_id,
                        ia.internship_id,
                        i.company_name,
                        i.internship_domain
                    FROM internship_applications ia
                    INNER JOIN internships i
                        ON ia.internship_id = i.internship_id
                    WHERE ia.reg_no = ?
                    AND ia.internship_id = ?
                    AND LOWER(ia.status) = 'approved'
                    LIMIT 1
                ";

                $stmt_application =
                    mysqli_prepare(
                        $conn,
                        $sql_application
                    );


                if (!$stmt_application) {

                    $message =
                        "Application database error.";

                    $message_type =
                        "error";

                } else {

                    mysqli_stmt_bind_param(
                        $stmt_application,
                        "si",
                        $reg_no,
                        $internship_id
                    );

                    mysqli_stmt_execute(
                        $stmt_application
                    );

                    $application_result =
                        mysqli_stmt_get_result(
                            $stmt_application
                        );


                    /* =========================================
                       APPLICATION MUST EXIST AND BE APPROVED
                       ========================================= */

                    if (
                        mysqli_num_rows(
                            $application_result
                        ) == 0
                    ) {

                        $message =
                            "Certificate cannot be issued. This student has not been approved for this internship.";

                        $message_type =
                            "error";

                    } else {


                        $application =
                            mysqli_fetch_assoc(
                                $application_result
                            );


                        /* =========================================
                           CHECK ATTENDANCE
                           ========================================= */

                        $sql_attendance = "
                            SELECT
                                attendance_id
                            FROM attendance
                            WHERE reg_no = ?
                            AND LOWER(status) = 'present'
                            LIMIT 1
                        ";

                        $stmt_attendance =
                            mysqli_prepare(
                                $conn,
                                $sql_attendance
                            );


                        if (!$stmt_attendance) {

                            $message =
                                "Attendance database error.";

                            $message_type =
                                "error";

                        } else {

                            mysqli_stmt_bind_param(
                                $stmt_attendance,
                                "s",
                                $reg_no
                            );

                            mysqli_stmt_execute(
                                $stmt_attendance
                            );

                            $attendance_result =
                                mysqli_stmt_get_result(
                                    $stmt_attendance
                                );


                            /* =========================================
                               ATTENDANCE MUST BE PRESENT
                               ========================================= */

                            if (
                                mysqli_num_rows(
                                    $attendance_result
                                ) == 0
                            ) {

                                $message =
                                    "Certificate cannot be issued. Student has not attended the meeting.";

                                $message_type =
                                    "error";

                            } else {


                                /* =========================================
                                   CHECK REPORT FOR SAME INTERNSHIP
                                   ========================================= */

                                $sql_report = "
                                    SELECT
                                        report_id,
                                        internship_id,
                                        report_title,
                                        status
                                    FROM internship_reports
                                    WHERE reg_no = ?
                                    AND internship_id = ?
                                    AND LOWER(status) = 'approved'
                                    LIMIT 1
                                ";

                                $stmt_report =
                                    mysqli_prepare(
                                        $conn,
                                        $sql_report
                                    );


                                if (!$stmt_report) {

                                    $message =
                                        "Report database error.";

                                    $message_type =
                                        "error";

                                } else {

                                    mysqli_stmt_bind_param(
                                        $stmt_report,
                                        "si",
                                        $reg_no,
                                        $internship_id
                                    );

                                    mysqli_stmt_execute(
                                        $stmt_report
                                    );

                                    $report_result =
                                        mysqli_stmt_get_result(
                                            $stmt_report
                                        );


                                    /* =========================================
                                       REPORT MUST MATCH INTERNSHIP
                                       ========================================= */

                                    if (
                                        mysqli_num_rows(
                                            $report_result
                                        ) == 0
                                    ) {

                                        $message =
                                            "Certificate cannot be issued. The approved report does not belong to this internship.";

                                        $message_type =
                                            "error";

                                    } else {


                                        $report =
                                            mysqli_fetch_assoc(
                                                $report_result
                                            );


                                        /* =========================================
                                           CHECK EXISTING CERTIFICATE
                                           ========================================= */

                                        $sql_existing = "
                                            SELECT
                                                certificate_id
                                            FROM certificates
                                            WHERE username = ?
                                            AND internship_id = ?
                                            LIMIT 1
                                        ";

                                        $stmt_existing =
                                            mysqli_prepare(
                                                $conn,
                                                $sql_existing
                                            );


                                        if (!$stmt_existing) {

                                            $message =
                                                "Certificate database error: " . mysqli_error($conn);

                                            $message_type =
                                                "error";

                                        } else {

                                            mysqli_stmt_bind_param(
                                                $stmt_existing,
                                                "si",
                                                $username,
                                                $internship_id
                                            );

                                            mysqli_stmt_execute(
                                                $stmt_existing
                                            );

                                            $existing_result =
                                                mysqli_stmt_get_result(
                                                    $stmt_existing
                                                );


                                            if (
                                                mysqli_num_rows(
                                                    $existing_result
                                                ) > 0
                                            ) {

                                                $message =
                                                    "Certificate has already been issued for this student and internship.";

                                                $message_type =
                                                    "error";

                                            } else {


                                                /* =========================================
                                                   ISSUE CERTIFICATE
                                                ========================================= */

                                                $sql_insert = "
                                                    INSERT INTO certificates
                                                    (
                                                        username,
                                                        internship_id,
                                                        certificate_date,
                                                        certificate_status
                                                    )
                                                    VALUES
                                                    (
                                                        ?,
                                                        ?,
                                                        CURDATE(),
                                                        'Issued'
                                                    )
                                                ";


                                                $stmt_insert =
                                                    mysqli_prepare(
                                                        $conn,
                                                        $sql_insert
                                                    );


                                                if (!$stmt_insert) {

                                                    $message =
                                                        "Certificate database error: " .
                                                        mysqli_error($conn);

                                                    $message_type =
                                                        "error";

                                                } else {

                                                    mysqli_stmt_bind_param(
                                                        $stmt_insert,
                                                        "si",
                                                        $username,
                                                        $internship_id
                                                    );


                                                    if (
                                                        mysqli_stmt_execute(
                                                            $stmt_insert
                                                        )
                                                    ) {

                                                        $message =
                                                            "Certificate issued successfully for " .
                                                            $student['student_name'] .
                                                            " - " .
                                                            $application['company_name'] .
                                                            ".";

                                                        $message_type =
                                                            "success";
                                                            /* =========================================
   AUTOMATIC NOTIFICATION
   ========================================= */

$notification_message =
    "Your internship certificate has been issued. You can view it now.";

$notification_status = "Unread";

$sql_notification = "
    INSERT INTO notifications
    (
        reg_no,
        message,
        status
    )
    VALUES
    (
        ?,
        ?,
        ?
    )
";

$stmt_notification =
    mysqli_prepare(
        $conn,
        $sql_notification
    );

if ($stmt_notification) {

    mysqli_stmt_bind_param(
        $stmt_notification,
        "sss",
        $reg_no,
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

                                                    } else {

                                                        $message =
                                                            "Certificate issue failed: " .
                                                            mysqli_error($conn);

                                                        $message_type =
                                                            "error";
                                                    }


                                                    mysqli_stmt_close(
                                                        $stmt_insert
                                                    );
                                                }
                                            }


                                            mysqli_stmt_close(
                                                $stmt_existing
                                            );
                                        }
                                    }


                                    mysqli_stmt_close(
                                        $stmt_report
                                    );
                                }
                            }


                            mysqli_stmt_close(
                                $stmt_attendance
                            );
                        }
                    }


                    mysqli_stmt_close(
                        $stmt_application
                    );
                }
            }


            mysqli_stmt_close(
                $stmt_student
            );
        }
    }
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
        Issue Certificate | Digital Internship Management System
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

            box-shadow:
                5px 0 18px rgba(36,52,71,0.15);

            overflow-y: auto;

            scrollbar-width: thin;

            scrollbar-color:
                #c6a15b #243447;

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


        /* =========================================
           SIDEBAR BOTTOM
        ========================================= */

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


        /* =========================================
           PAGE CONTENT
        ========================================= */

        .page-content {

            padding: 45px 40px;

        }


        .content-heading {

            margin-bottom: 30px;

        }


        .content-heading h2 {

            margin: 0 0 8px;

            font-family: 'Cinzel', serif;

            font-size: 29px;

            color: #243447;

            font-weight: 600;

        }


        .content-heading p {

            margin: 0;

            color: #66717c;

            font-size: 13px;

        }


        /* =========================================
           CERTIFICATE CARD
        ========================================= */

        .certificate-card {

            max-width: 760px;

            margin: 0 auto;

            background: #fffdf9;

            border:
                1px solid #ddd4c8;

            border-radius: 8px;

            padding: 40px;

            box-shadow:
                0 10px 30px rgba(36,52,71,0.08);

        }


        .certificate-icon {

            width: 58px;

            height: 58px;

            margin: 0 auto 18px;

            border-radius: 50%;

            background: #243447;

            border:
                2px solid #c6a15b;

            color: #f3d99b;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 25px;

        }


        .certificate-card h3 {

            text-align: center;

            margin: 0 0 8px;

            font-family: 'Cinzel', serif;

            font-size: 24px;

            color: #243447;

            font-weight: 600;

        }


        .certificate-subtitle {

            text-align: center;

            margin: 0 0 30px;

            color: #66717c;

            font-size: 12px;

        }


        /* =========================================
           MESSAGE
        ========================================= */

        .message {

            padding: 12px 15px;

            border-radius: 4px;

            margin-bottom: 22px;

            font-size: 12px;

            font-weight: 500;

        }


        .message.success {

            background: #e8f5e9;

            color: #2e7d32;

            border-left:
                4px solid #43a047;

        }


        .message.error {

            background: #f8e8e8;

            color: #b3261e;

            border-left:
                4px solid #c62828;

        }


        /* =========================================
           FORM
        ========================================= */

        .form-group {

            margin-bottom: 22px;

        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            color: #243447;

            font-size: 13px;

            font-weight: 600;

        }


        .form-group input {

            width: 100%;

            padding: 13px 14px;

            border:
                1px solid #d7cec2;

            border-radius: 4px;

            background: #fffdf9;

            color: #243447;

            font-family: 'Poppins', sans-serif;

            font-size: 13px;

            outline: none;

        }


        .form-group input:focus {

            border-color: #c6a15b;

            box-shadow:
                0 0 0 2px rgba(198,161,91,0.12);

        }


        /* =========================================
           ISSUE BUTTON
        ========================================= */

        .issue-btn {

            width: 100%;

            padding: 13px;

            margin-top: 5px;

            border: none;

            border-radius: 4px;

            background: #243447;

            color: #f3d99b;

            font-family: 'Poppins', sans-serif;

            font-size: 13px;

            font-weight: 600;

            letter-spacing: .4px;

            cursor: pointer;

        }


        .issue-btn:hover {

            background: #c6a15b;

            color: #243447;

        }


        /* =========================================
           BACK BUTTON
        ========================================= */

        .back-btn {

            display: inline-block;

            margin-top: 25px;

            padding: 11px 20px;

            background: #243447;

            color: #fff;

            text-decoration: none;

            border-radius: 4px;

            font-size: 12px;

        }


        .back-btn:hover {

            background: #243447;

            color: #fff;

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

                padding: 30px 20px;

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


            <a href="admin_dashboard.php">

                <span class="menu-icon">
                    ⌂
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="manage_students.php">

                <span class="menu-icon">
                    👥
                </span>

                <span>
                    Manage Students
                </span>

            </a>


            <a href="manage_internships.php">

                <span class="menu-icon">
                    🏢
                </span>

                <span>
                    Manage Internships
                </span>

            </a>


            <a href="manage_applications.php">

                <span class="menu-icon">
                    📋
                </span>

                <span>
                    Manage Applications
                </span>

            </a>


            <a href="manage_reports.php">

                <span class="menu-icon">
                    📄
                </span>

                <span>
                    View Reports
                </span>

            </a>


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


            <a
                href="issue_certificate.php"
                class="active"
            >

                <span class="menu-icon">
                    🎓
                </span>

                <span>
                    Issue Certificate
                </span>

            </a>


        </nav>


        <div class="sidebar-bottom">

            <a
                href="admin_logout.php"
                class="logout-link"
            >

                <span>
                    ↪
                </span>

                Logout

            </a>

        </div>


    </aside>



    <!-- =========================================
         MAIN CONTENT
    ========================================= -->

    <main class="main-content">


        <header class="top-header">

            <h1>
                Certificate Management
            </h1>

            <div class="system-name">
                Digital Internship Management System
            </div>

        </header>


        <section class="page-content">


            <div class="content-heading">

                <h2>
                    Certificate Management
                </h2>

                <p>
                    Issue internship completion certificates to eligible students.
                </p>

            </div>


            <div class="certificate-card">


                <div class="certificate-icon">
                    🎓
                </div>


                <h3>
                    Issue Certificate
                </h3>


                <p class="certificate-subtitle">
                    Enter the student username and internship details below.
                </p>


                <!-- MESSAGE -->

                <?php if ($message != "") { ?>

                    <div class="message <?php echo htmlspecialchars($message_type); ?>">

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </div>

                <?php } ?>


                <!-- FORM -->

                <form method="POST">


                    <!-- USERNAME -->

                    <div class="form-group">

                        <label for="username">
                            Student Username
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter student username"
                            required
                        >

                    </div>


                    <!-- INTERNSHIP ID -->

                    <div class="form-group">

                        <label for="internship_id">
                            Internship ID
                        </label>

                        <input
                            type="number"
                            id="internship_id"
                            name="internship_id"
                            placeholder="Enter internship ID"
                            min="1"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="issue-btn"
                    >

                        Issue Certificate

                    </button>


                </form>


                <a
                    href="admin_dashboard.php"
                    class="back-btn"
                >

                    ← Back to Admin Dashboard

                </a>


            </div>


        </section>


    </main>


</div>


</body>

</html>