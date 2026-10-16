<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";
$message_type = "";

/* Fetch internships */
$internship_sql = "
    SELECT internship_id, company_name, internship_domain
    FROM internships
    ORDER BY internship_id DESC
";

$internship_result = mysqli_query($conn, $internship_sql);


/* Save Meeting */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $internship_id = intval($_POST["internship_id"]);
    $meeting_title = trim($_POST["meeting_title"]);
    $meeting_message = trim($_POST["meeting_message"]);
    $meeting_link = trim($_POST["meeting_link"]);

    if (
        empty($internship_id) ||
        empty($meeting_title) ||
        empty($meeting_message) ||
        empty($meeting_link)
    ) {

        $message = "Please fill all meeting details.";
        $message_type = "error";

    } else {

        /*
         * Deactivate previous meeting
         * for this internship
         */
        $update_sql = "
            UPDATE online_meetings
            SET status = 'Inactive'
            WHERE internship_id = ?
        ";

        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param(
            $update_stmt,
            "i",
            $internship_id
        );
        mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);


        /*
         * Insert new active meeting
         */
        $insert_sql = "
            INSERT INTO online_meetings
            (
                internship_id,
                meeting_title,
                meeting_message,
                meeting_link,
                status
            )
            VALUES (?, ?, ?, ?, 'Active')
        ";

        $insert_stmt = mysqli_prepare($conn, $insert_sql);

        mysqli_stmt_bind_param(
            $insert_stmt,
            "isss",
            $internship_id,
            $meeting_title,
            $meeting_message,
            $meeting_link
        );

        if (mysqli_stmt_execute($insert_stmt)) {

            $message = "Meeting details sent successfully.";
            $message_type = "success";

        } else {

            $message = "Failed to save meeting details.";
            $message_type = "error";
        }

        mysqli_stmt_close($insert_stmt);
    }
}


/* Fetch active meetings */
$active_sql = "
    SELECT 
        om.meeting_id,
        om.meeting_title,
        om.meeting_link,
        om.status,
        i.company_name,
        i.internship_domain
    FROM online_meetings om
    INNER JOIN internships i
        ON om.internship_id = i.internship_id
    WHERE om.status = 'Active'
    ORDER BY om.meeting_id DESC
";

$active_result = mysqli_query($conn, $active_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Manage Meeting | Admin Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

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
            transition: none;
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
            transition: none;
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


        /* ================= CONTENT ================= */

        .content-area {
            padding: 35px 40px;
        }

        .meeting-card {
            max-width: 850px;
            background: #fffdf9;
            border: 1px solid #ddd4c8;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 5px 18px rgba(36,52,71,0.08);
        }

        .meeting-card h2 {
            margin-top: 0;
            font-family: 'Cinzel', serif;
            color: #243447;
            font-size: 22px;
        }

        .subtitle {
            color: #66717c;
            font-size: 13px;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #243447;
            font-size: 13px;
            font-weight: 500;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px 13px;
            margin-bottom: 20px;
            border: 1px solid #d8cfc3;
            border-radius: 5px;
            background: #fff;
            color: #243447;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            outline: none;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #c6a15b;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .save-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            background: #243447;
            color: white;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }

        .save-btn:hover {
            background: #c6a15b;
            color: #243447;
        }


        /* ================= MESSAGE ================= */

        .message {
            max-width: 850px;
            padding: 13px 16px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 13px;
        }

        .success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .error {
            background: #fdecea;
            color: #c62828;
            border-left: 4px solid #c62828;
        }


        /* ================= ACTIVE MEETINGS ================= */

        .active-section {
            max-width: 1000px;
            margin-top: 35px;
        }

        .active-section h2 {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            color: #243447;
            margin-bottom: 18px;
        }

        .meeting-table {
            width: 100%;
            border-collapse: collapse;
            background: #fffdf9;
            border: 1px solid #ddd4c8;
            box-shadow: 0 4px 15px rgba(36,52,71,0.06);
        }

        .meeting-table th {
            background: #243447;
            color: #f3d99b;
            padding: 13px;
            text-align: left;
            font-size: 12px;
            font-weight: 500;
        }

        .meeting-table td {
            padding: 13px;
            border-bottom: 1px solid #eee6dc;
            font-size: 12px;
            color: #4d5965;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            background: #e8f5e9;
            color: #2e7d32;
            font-size: 11px;
            font-weight: 600;
        }

    </style>

</head>


<body>

<div class="dashboard-layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="sidebar-title">
            <h2>Admin Portal</h2>
            <p>Internship Management</p>
        </div>

        <nav class="sidebar-menu">

            <div class="menu-title">Main Menu</div>

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

            <a href="admin_reports.php">
                <span class="menu-icon">📄</span>
                <span>View Reports</span>
            </a>

            <a href="admin_attendance.php">
                <span class="menu-icon">📅</span>
                <span>Attendance</span>
            </a>

            <a href="manage_meeting.php" class="active">
                <span class="menu-icon">💻</span>
                <span>Manage Meeting</span>
            </a>

            <a href="issue_certificate.php">
                <span class="menu-icon">🎓</span>
                <span>Issue Certificate</span>
            </a>

        </nav>

        <div class="sidebar-bottom">

            <a href="logout.php" class="logout-link">
                <span>↪</span>
                Logout
            </a>

        </div>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-content">

        <header class="top-header">

            <h1>Manage Meeting</h1>

            <div class="system-name">
                Digital Internship Management System
            </div>

        </header>


        <div class="content-area">

            <?php if (!empty($message)) { ?>

                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php } ?>


            <div class="meeting-card">

                <h2>Send Online Meeting Details</h2>

                <div class="subtitle">
                    Select the internship and provide the meeting details for approved students.
                </div>


                <form method="POST">


                    <label>
                        Select Internship
                    </label>

                    <select name="internship_id" required>

                        <option value="">
                            -- Select Internship --
                        </option>

                        <?php

                        if ($internship_result && mysqli_num_rows($internship_result) > 0) {

                            while ($internship = mysqli_fetch_assoc($internship_result)) {

                        ?>

                            <option value="<?php echo $internship['internship_id']; ?>">

                                <?php
                                echo htmlspecialchars(
                                    $internship['company_name']
                                    . " - "
                                    . $internship['internship_domain']
                                );
                                ?>

                            </option>

                        <?php

                            }

                        }

                        ?>

                    </select>


                    <label>
                        Meeting Title
                    </label>

                    <input
                        type="text"
                        name="meeting_title"
                        placeholder="Example: Internship Online Meeting"
                        required
                    >


                    <label>
                        Meeting Message
                    </label>

                    <textarea
                        name="meeting_message"
                        placeholder="Enter the message for students..."
                        required
                    ></textarea>


                    <label>
                        Meeting Link
                    </label>

                    <input
                        type="url"
                        name="meeting_link"
                        placeholder="https://meet.google.com/..."
                        required
                    >


                    <button
                        type="submit"
                        class="save-btn"
                    >
                        Send Meeting Details
                    </button>

                </form>

            </div>


            <!-- ACTIVE MEETINGS -->

            <div class="active-section">

                <h2>Currently Active Meetings</h2>

                <table class="meeting-table">

                    <tr>
                        <th>Company</th>
                        <th>Domain</th>
                        <th>Meeting Title</th>
                        <th>Status</th>
                    </tr>

                    <?php

                    if ($active_result && mysqli_num_rows($active_result) > 0) {

                        while ($meeting = mysqli_fetch_assoc($active_result)) {

                    ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($meeting['company_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($meeting['internship_domain']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($meeting['meeting_title']); ?>
                            </td>

                            <td>
                                <span class="status-badge">
                                    Active
                                </span>
                            </td>

                        </tr>

                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td colspan="4">
                                No active meetings available.
                            </td>

                        </tr>

                    <?php } ?>

                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>