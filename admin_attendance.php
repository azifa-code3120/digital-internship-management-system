

<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    die("ADMIN SESSION NOT FOUND");
}

include "db_connect.php";

$sql = "SELECT 
            s.username,
            s.student_name,
            s.email,
            a.status
        FROM attendance a
        INNER JOIN student s ON a.reg_no = s.reg_no
        WHERE LOWER(a.status) = 'present'
        ORDER BY s.student_name ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Attendance query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Attendance | Digital Internship Management System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

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

        /* SIDEBAR */

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

        /* MAIN CONTENT */

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

        .content {
            padding: 38px 40px;
        }

        .card {
            background: #fffdf9;
            border: 1px solid #ddd4c8;
            border-radius: 7px;
            padding: 30px;
            box-shadow: 0 5px 18px rgba(36,52,71,0.06);
        }

        .section-title {
            margin: 0;
            font-family: 'Cinzel', serif;
            font-size: 25px;
            color: #243447;
        }

        .section-line {
            width: 55px;
            height: 2px;
            background: #c6a15b;
            margin: 10px 0 8px;
        }

        .description {
            color: #66717c;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th {
            background: #243447;
            color: #f3d99b;
            padding: 14px 15px;
            text-align: left;
            font-family: 'Cinzel', serif;
            font-size: 12px;
            letter-spacing: .4px;
            border-bottom: 2px solid #c6a15b;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e5ddd2;
            color: #394756;
            font-size: 13px;
        }

        tr:hover td {
            background: #faf6ef;
        }

        .status {
            display: inline-block;
            padding: 6px 13px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .present {
            background: #dcefdc;
            color: #26733a;
        }

        .absent {
            background: #f5dddd;
            color: #a33a3a;
        }

        .empty {
            text-align: center;
            padding: 35px;
            color: #777;
            font-size: 13px;
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

            <a href="manage_reports.php">
                <span class="menu-icon">📄</span>
                <span>View Reports</span>
            </a>

            <a href="admin_attendance.php" class="active">
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

            <a href="logout.php" class="logout-link">
                <span>↪</span>
                Logout
            </a>

        </div>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-content">

        <header class="top-header">

            <h1>Attendance</h1>

            <div class="system-name">
                Digital Internship Management System
            </div>

        </header>


        <section class="content">

            <div class="card">

                <h2 class="section-title">Student Attendance</h2>

                <div class="section-line"></div>

                <p class="description">
                    View attendance records of students who have attended the internship meeting.
                </p>


                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Attendance</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (mysqli_num_rows($result) > 0): ?>

                            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($row['username']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['student_name']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </td>

                                    <td>

                                        <?php
                                        $status = strtolower($row['status']);

                                        if ($status == 'present'):
                                        ?>

                                            <span class="status present">
                                                Present
                                            </span>

                                        <?php else: ?>

                                            <span class="status absent">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="4" class="empty">
                                    No attendance records found.
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>