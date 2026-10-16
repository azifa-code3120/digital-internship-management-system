<?php
session_start();
include "db_connect.php";

/* ==============================
   ADMIN LOGIN CHECK
   ============================== */

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}


/* ==============================
   APPROVE / REJECT APPLICATION
   + AUTOMATIC NOTIFICATION
   ============================== */

if (isset($_GET['action']) && isset($_GET['id'])) {

    $application_id = intval($_GET['id']);
    $action = $_GET['action'];

    /* ==============================
       GET STUDENT REG NO
       ============================== */

    $sql_student = "SELECT reg_no
                    FROM internship_applications
                    WHERE application_id = ?";

    $stmt_student = mysqli_prepare($conn, $sql_student);

    if ($stmt_student) {

        mysqli_stmt_bind_param(
            $stmt_student,
            "i",
            $application_id
        );

        mysqli_stmt_execute($stmt_student);

        $result_student = mysqli_stmt_get_result($stmt_student);

        $student_data = mysqli_fetch_assoc($result_student);

        mysqli_stmt_close($stmt_student);

    } else {

        die("Database Error: " . mysqli_error($conn));
    }


    /* ==============================
       APPROVE APPLICATION
       ============================== */

    if ($action == "approve") {

        $status = "Approved";

        $sql_update = "UPDATE internship_applications
                       SET status = ?
                       WHERE application_id = ?";

        $stmt_update = mysqli_prepare($conn, $sql_update);

        if ($stmt_update) {

            mysqli_stmt_bind_param(
                $stmt_update,
                "si",
                $status,
                $application_id
            );

            mysqli_stmt_execute($stmt_update);

            mysqli_stmt_close($stmt_update);
        }


        /* ==============================
           AUTOMATIC NOTIFICATION
           ============================== */

        if ($student_data && !empty($student_data['reg_no'])) {

            $reg_no = $student_data['reg_no'];

            $notification_message =
                "Your internship application has been approved.";

            $notification_status = "Unread";

            $sql_notification = "INSERT INTO notifications
                                 (reg_no, message, status)
                                 VALUES (?, ?, ?)";

            $stmt_notification =
                mysqli_prepare($conn, $sql_notification);

            if ($stmt_notification) {

                mysqli_stmt_bind_param(
                    $stmt_notification,
                    "sss",
                    $reg_no,
                    $notification_message,
                    $notification_status
                );

                mysqli_stmt_execute($stmt_notification);

                mysqli_stmt_close($stmt_notification);
            }
        }


    /* ==============================
       REJECT APPLICATION
       ============================== */

    } elseif ($action == "reject") {

        $status = "Rejected";

        $sql_update = "UPDATE internship_applications
                       SET status = ?
                       WHERE application_id = ?";

        $stmt_update = mysqli_prepare($conn, $sql_update);

        if ($stmt_update) {

            mysqli_stmt_bind_param(
                $stmt_update,
                "si",
                $status,
                $application_id
            );

            mysqli_stmt_execute($stmt_update);

            mysqli_stmt_close($stmt_update);
        }


        /* ==============================
           AUTOMATIC NOTIFICATION
           ============================== */

        if ($student_data && !empty($student_data['reg_no'])) {

            $reg_no = $student_data['reg_no'];

            $notification_message =
                "Your internship application has been rejected.";

            $notification_status = "Unread";

            $sql_notification = "INSERT INTO notifications
                                 (reg_no, message, status)
                                 VALUES (?, ?, ?)";

            $stmt_notification =
                mysqli_prepare($conn, $sql_notification);

            if ($stmt_notification) {

                mysqli_stmt_bind_param(
                    $stmt_notification,
                    "sss",
                    $reg_no,
                    $notification_message,
                    $notification_status
                );

                mysqli_stmt_execute($stmt_notification);

                mysqli_stmt_close($stmt_notification);
            }
        }
    }


    header("Location: manage_applications.php");
    exit();
}


/* ==============================
   FETCH APPLICATIONS
   ============================== */

/*
   IMPORTANT:
   company_name and internship_domain
   are taken from internships table.
*/

$sql = "SELECT
            ia.application_id,
            s.student_name,
            s.reg_no,
            s.department,
            i.company_name,
            i.internship_domain,
            ia.application_date,
            ia.status

        FROM internship_applications ia

        LEFT JOIN student s
            ON ia.reg_no = s.reg_no

        LEFT JOIN internships i
            ON ia.internship_id = i.internship_id

        ORDER BY ia.application_id DESC";


$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database Error: " . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$total_applications = mysqli_num_rows($result);

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Applications</title>


    <!-- Google Fonts -->

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


        /*
           IMPORTANT:
           Sidebar should NOT move.
        */

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


        /* =====================================
           LOGOUT
           ===================================== */

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


        /* =====================================
           MAIN CONTENT
           ===================================== */

        .main-content {

            margin-left: 255px;

            width: calc(100% - 255px);

            min-height: 100vh;

            padding: 38px 40px 50px;
        }


        /* =====================================
           PAGE HEADER
           ===================================== */

        .page-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            margin-bottom: 25px;
        }


        .page-heading h1 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 27px;

            color: #243447;

            font-weight: 600;

            letter-spacing: .5px;
        }


        .page-heading h1::after {

            content: "";

            display: block;

            width: 58px;

            height: 2px;

            background: #c6a15b;

            margin-top: 9px;
        }


        .page-heading p {

            margin: 9px 0 0;

            color: #66717c;

            font-size: 12px;
        }


        /* =====================================
           APPLICATION COUNT
           ===================================== */

        .count-box {

            padding: 10px 18px;

            background: #fffdf9;

            border: 1px solid #ddd1bf;

            border-radius: 20px;

            color: #243447;

            font-size: 12px;

            font-weight: 500;

            white-space: nowrap;
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


        .table-header {

            padding: 19px 24px;

            border-bottom: 1px solid #ddd4c8;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }


        .table-header h2 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 17px;

            color: #243447;

            font-weight: 600;
        }


        .table-header span {

            color: #7b858e;

            font-size: 11px;
        }


        /* =====================================
           TABLE
           ===================================== */

        .table-wrapper {

            overflow-x: auto;
        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 900px;
        }


        thead th {

            background: #243447;

            color: #f3d99b;

            padding: 14px 15px;

            text-align: left;

            font-size: 11px;

            font-weight: 600;

            letter-spacing: .2px;

            white-space: nowrap;
        }


        tbody td {

            padding: 15px;

            border-bottom: 1px solid #e5dfd6;

            color: #52606d;

            font-size: 12px;

            vertical-align: middle;
        }


        tbody tr:nth-child(even) {

            background: #faf7f1;
        }


        tbody tr:hover {

            background: #f5eee2;
        }


        .student-name {

            color: #243447;

            font-weight: 600;
        }


        .register-number {

            color: #66717c;

            font-size: 11px;
        }


        .department {

            display: inline-block;

            padding: 5px 9px;

            background: #f3eee5;

            color: #5d5145;

            border-radius: 4px;

            font-size: 10px;

            font-weight: 500;
        }


        /* =====================================
           STATUS
           ===================================== */

        .status {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 15px;

            font-size: 10px;

            font-weight: 600;

            white-space: nowrap;
        }


        .status.approved {

            background: #e4f0e5;

            color: #3d6b45;
        }


        .status.rejected {

            background: #f7e2e0;

            color: #a0443d;
        }


        .status.pending {

            background: #f5ead0;

            color: #806323;
        }


        .status.completed {

            background: #e1eaf3;

            color: #3f5d78;
        }


        .status.applied {

            background: #eee9df;

            color: #685c4e;
        }


        /* =====================================
           ACTION BUTTONS
           ===================================== */

        .action-group {

            display: flex;

            align-items: center;

            gap: 7px;
        }


        .action-btn {

            display: inline-block;

            padding: 8px 12px;

            border-radius: 4px;

            text-decoration: none;

            font-size: 10px;

            font-weight: 600;

            border: none;

            cursor: pointer;

            white-space: nowrap;
        }


        .approve-btn {

            background: #243447;

            color: white;
        }


        .approve-btn:hover {

            background: #30465d;
        }


        .reject-btn {

            background: #a90000;

            color: white;
        }


        .reject-btn:hover {

            background: #870000;
        }


        /* =====================================
           NO APPLICATIONS
           ===================================== */

        .empty-box {

            text-align: center;

            padding: 55px 20px;

            color: #7a838b;
        }


        .empty-box h3 {

            margin: 0 0 8px;

            font-family: 'Cinzel', serif;

            color: #243447;

            font-size: 18px;
        }


        .empty-box p {

            margin: 0;

            font-size: 12px;
        }


        /* =====================================
           BACK BUTTON
           ===================================== */

        .back-btn {

            display: inline-block;

            margin-top: 22px;

            padding: 11px 18px;

            background: #243447;

            color: #f3d99b;

            text-decoration: none;

            border-radius: 4px;

            font-size: 11px;

            font-weight: 500;

            transition: none;
        }


        .back-btn:hover {

            background: #30465d;

            color: #f3d99b;
        }


        /* =====================================
           RESPONSIVE
           ===================================== */

        @media (max-width: 900px) {

            .sidebar {

                width: 220px;
            }

            .main-content {

                margin-left: 220px;

                width: calc(100% - 220px);

                padding: 30px 20px;
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

                <span class="menu-icon">👤</span>

                <span>Manage Students</span>

            </a>


            <a href="manage_internships.php">

                <span class="menu-icon">🏢</span>

                <span>Manage Internships</span>

            </a>


            <a href="manage_applications.php"
               class="active">

                <span class="menu-icon">📋</span>

                <span>Manage Applications</span>

            </a>


            <a href="manage_reports.php">

                <span class="menu-icon">📄</span>

                <span>View Reports</span>

            </a>


            <a href="admin_attendance.php">

                <span class="menu-icon">📅</span>

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


            <a href="admin_logout.php"
               class="logout-link">

                <span>↪</span>

                Logout

            </a>


        </div>


    </aside>



    <!-- =====================================
         RIGHT SIDE CONTENT
         ===================================== -->

    <main class="main-content">


        <!-- PAGE HEADER -->

        <div class="page-header">


            <div class="page-heading">

                <h1>
                    Manage Applications
                </h1>

                <p>
                    Review and manage student internship applications.
                </p>

            </div>


            <div class="count-box">

                <?php echo $total_applications; ?>

                Applications

            </div>


        </div>



        <!-- TABLE -->

        <div class="table-card">


            <div class="table-header">

                <h2>
                    Internship Applications
                </h2>

                <span>
                    Application Records
                </span>

            </div>



            <?php if ($total_applications > 0) { ?>


                <div class="table-wrapper">


                    <table>


                        <thead>

                            <tr>

                                <th>S.No</th>

                                <th>Student Name</th>

                                <th>Register No</th>

                                <th>Department</th>

                                <th>Company</th>

                                <th>Domain</th>

                                <th>Application Date</th>

                                <th>Status</th>

                                <th>Action</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        $serial_no = 1;

                        while ($row = mysqli_fetch_assoc($result)) {

                        ?>


                            <tr>


                                <td>
                                    <?php echo $serial_no++; ?>
                                </td>


                                <td>

                                    <div class="student-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $row['student_name'] ?? 'N/A'
                                        );
                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <span class="register-number">

                                        <?php
                                        echo htmlspecialchars(
                                            $row['reg_no'] ?? 'N/A'
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="department">

                                        <?php
                                        echo htmlspecialchars(
                                            $row['department'] ?? 'N/A'
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row['company_name'] ?? 'N/A'
                                    );
                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row['internship_domain'] ?? 'N/A'
                                    );
                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row['application_date'] ?? 'N/A'
                                    );
                                    ?>

                                </td>


                                <td>


                                    <?php

                                    $current_status = $row['status'];

                                    $status_class = strtolower(
                                        str_replace(
                                            ' ',
                                            '',
                                            $current_status
                                        )
                                    );

                                    ?>


                                    <span class="status <?php
                                        echo htmlspecialchars(
                                            $status_class
                                        );
                                    ?>">

                                        <?php
                                        echo htmlspecialchars(
                                            $current_status
                                        );
                                        ?>

                                    </span>


                                </td>


                                <td>


                                    <div class="action-group">


                                        <?php

                                        if (
                                            $current_status != "Approved"
                                            &&
                                            $current_status != "Completed"
                                        ) {

                                        ?>


                                            <a
                                                href="manage_applications.php?action=approve&id=<?php echo $row['application_id']; ?>"
                                                class="action-btn approve-btn"
                                                onclick="return confirm('Approve this application?');"
                                            >
                                                Approve
                                            </a>


                                        <?php
                                        }


                                        if (
                                            $current_status == "Approved"
                                            ||
                                            $current_status == "Completed"
                                        ) {

                                        ?>


                                            <a
                                                href="manage_applications.php?action=reject&id=<?php echo $row['application_id']; ?>"
                                                class="action-btn reject-btn"
                                                onclick="return confirm('Reject this application?');"
                                            >
                                                Reject
                                            </a>


                                        <?php
                                        }


                                        if (
                                            $current_status == "Rejected"
                                        ) {

                                        ?>


                                            <a
                                                href="manage_applications.php?action=approve&id=<?php echo $row['application_id']; ?>"
                                                class="action-btn approve-btn"
                                                onclick="return confirm('Approve this application again?');"
                                            >
                                                Approve
                                            </a>


                                        <?php
                                        }

                                        ?>


                                    </div>


                                </td>


                            </tr>


                        <?php

                        }

                        ?>


                        </tbody>


                    </table>


                </div>


            <?php } else { ?>


                <div class="empty-box">

                    <h3>
                        No Applications
                    </h3>

                    <p>
                        No student internship applications are available.
                    </p>

                </div>


            <?php } ?>


        </div>



        <!-- BACK BUTTON -->

        <a
            href="admin_dashboard.php"
            class="back-btn"
        >
            ← Back to Admin Dashboard
        </a>


    </main>


</div>


</body>

</html>