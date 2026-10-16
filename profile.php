<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];

$student_name = "Student";
$username = "Not Available";
$department = "Not Available";
$email = "Not Available";

$sql = "SELECT username, reg_no, student_name, department, email
        FROM student
        WHERE reg_no = ?";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "s", $reg_no);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {

        $student = mysqli_fetch_assoc($result);

        $username = $student['username'];
        $student_name = $student['student_name'];
        $reg_no = $student['reg_no'];
        $department = $student['department'];
        $email = $student['email'];
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

<title>My Profile | Digital Internship Management System</title>

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
}


/* No movement */

.sidebar-menu a:hover {

    background: rgba(198,161,91,0.17);

    color: #ffffff;
}


/* Active Profile */

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
   PROFILE AREA
===================================== */

.profile-container {

    width: 88%;

    max-width: 1000px;

    margin: 45px auto;
}


/* Heading */

.profile-heading {

    margin-bottom: 25px;
}

.profile-heading h2 {

    margin: 0;

    font-family: 'Cinzel', serif;

    font-size: 28px;

    color: #243447;

    font-weight: 600;
}

.profile-heading p {

    margin: 8px 0 0;

    color: #707982;

    font-size: 13px;
}


/* =====================================
   PROFILE CARD
===================================== */

.profile-card {

    background: #fffdf9;

    border: 1px solid #d9d0c3;

    border-radius: 7px;

    box-shadow: 0 10px 25px rgba(36,52,71,0.08);

    overflow: hidden;
}


/* Gold top line */

.profile-top-border {

    height: 5px;

    background: #c6a15b;
}


/* =====================================
   PROFILE INITIAL
===================================== */

.profile-header {

    padding: 32px;

    border-bottom: 1px solid #e1d9ce;

    display: flex;

    align-items: center;

    justify-content: center;
}


/* Initial Logo ONLY */

.avatar {

    width: 78px;

    height: 78px;

    border-radius: 50%;

    background: #243447;

    border: 4px solid #c6a15b;

    color: #f3d99b;

    display: flex;

    align-items: center;

    justify-content: center;

    font-family: 'Cinzel', serif;

    font-size: 32px;

    font-weight: 600;
}


/* =====================================
   INFORMATION
===================================== */

.profile-details {

    padding: 30px 40px;
}

.profile-details h3 {

    margin: 0 0 20px;

    font-family: 'Cinzel', serif;

    font-size: 19px;

    color: #243447;

    font-weight: 600;
}

.profile-details h3::after {

    content: "";

    display: block;

    width: 45px;

    height: 2px;

    background: #c6a15b;

    margin-top: 8px;
}


/* =====================================
   WEBSITE STYLE DETAILS
===================================== */

.details-list {

    width: 100%;
}


/* One row */

.detail-row {

    display: flex;

    align-items: center;

    padding: 18px 5px;

    border-bottom: 1px solid #e3ddd4;
}


/* Last row */

.detail-row:last-child {

    border-bottom: none;
}


/* Label */

.detail-label {

    width: 30%;

    color: #9a7a42;

    font-size: 11px;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: 1.1px;
}


/* Value */

.detail-value {

    width: 70%;

    color: #243447;

    font-size: 14px;

    font-weight: 500;

    word-break: break-word;
}


/* =====================================
   FOOTER
===================================== */

.profile-footer {

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

    .profile-details {

        padding: 25px;
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
    }

    .main-content {

        margin-left: 0;

        width: 100%;
    }

    .profile-container {

        width: 92%;
    }

    .detail-row {

        display: block;

        padding: 15px 5px;
    }

    .detail-label {

        width: 100%;

        margin-bottom: 6px;
    }

    .detail-value {

        width: 100%;
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


        <a href="dashboard.php">

            <span class="menu-icon">⌂</span>

            <span>Dashboard</span>

        </a>


        <a href="profile.php" class="active">

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

        <a href="logout.php" class="logout-link">

            <span>↪</span>

            Logout

        </a>

    </div>


</aside>



<!-- =====================================
     MAIN CONTENT
===================================== -->

<main class="main-content">


    <!-- HEADER -->

    <header class="top-header">

        <h1>
            My Profile
        </h1>

        <span class="system-name">
            Digital Internship Management System
        </span>

    </header>



    <!-- PROFILE -->

    <section class="profile-container">


        <div class="profile-heading">

            <h2>
                Personal Profile
            </h2>

            <p>
                View your registered student information.
            </p>

        </div>



        <div class="profile-card">


            <!-- GOLD LINE -->

            <div class="profile-top-border"></div>



            <!-- INITIAL ONLY -->

            <div class="profile-header">

                <div class="avatar">

                    <?php
                    echo strtoupper(substr($student_name, 0, 1));
                    ?>

                </div>

            </div>



            <!-- DETAILS -->

            <div class="profile-details">


                <h3>
                    Student Information
                </h3>


                <div class="details-list">


                    <!-- Username -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Username
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars($username);
                            ?>

                        </div>

                    </div>



                    <!-- Student Name -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Student Name
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars($student_name);
                            ?>

                        </div>

                    </div>



                    <!-- Department -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Department
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars($department);
                            ?>

                        </div>

                    </div>



                    <!-- Email -->

                    <div class="detail-row">

                        <div class="detail-label">
                            Email Address
                        </div>

                        <div class="detail-value">

                            <?php
                            echo htmlspecialchars($email);
                            ?>

                        </div>

                    </div>


                </div>

            </div>


        </div>


    </section>



    <!-- FOOTER -->

    <footer class="profile-footer">

        Digital Internship Management System © 2026

    </footer>


</main>


</div>


</body>

</html>