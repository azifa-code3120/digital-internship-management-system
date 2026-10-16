<?php
session_start();
include "db_connect.php";

$certificate = null;
$error = "";

if (!isset($_SESSION['username'])) {

    $error = "Student login session not found.";

} else {

    $username = $_SESSION['username'];

    $sql = "
        SELECT
            c.certificate_id,
            c.username,
            c.internship_id,
            c.certificate_date,
            c.certificate_status,

            s.student_name,
            s.department,
            s.email,

            i.company_name,
            i.internship_domain,
            i.description,
            i.start_date,
            i.end_date,
            i.guide_name

        FROM certificates c

        INNER JOIN student s
            ON c.username = s.username

        INNER JOIN internships i
            ON c.internship_id = i.internship_id

         WHERE c.username = ?

ORDER BY c.certificate_id DESC
LIMIT 1

        
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {

            $certificate = mysqli_fetch_assoc($result);

        } else {

            $error = "Certificate has not been issued yet.";

        }

        mysqli_stmt_close($stmt);

    } else {

        $error = "Database Error: " . mysqli_error($conn);

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Certificate</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">


<style>

/* ==========================================
   BASIC
========================================== */

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


/* ==========================================
   SIDEBAR
========================================== */

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

    transition: none;
}

.logout-link:hover {
    background: #c6a15b;

    color: #243447;
}


/* ==========================================
   MAIN CONTENT
========================================== */

.main-content {
    margin-left: 255px;

    width: calc(100% - 255px);

    min-height: 100vh;

    display: flex;

    flex-direction: column;
}


/* ==========================================
   HEADER
========================================== */

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


/* ==========================================
   PAGE CONTENT
========================================== */

.page-content {
    padding: 38px 40px 50px;

    flex: 1;
}

.page-title {
    margin: 0;

    font-family: 'Cinzel', serif;

    font-size: 28px;

    color: #243447;

    font-weight: 600;
}

.page-title::after {
    content: "";

    display: block;

    width: 58px;

    height: 2px;

    background: #c6a15b;

    margin-top: 10px;
}

.page-subtitle {
    margin-top: 10px;

    color: #66717c;

    font-size: 13px;
}


/* ==========================================
   CERTIFICATE CONTAINER
========================================== */

.certificate-card {
    margin-top: 30px;

    background: transparent;

    border: none;

    padding: 0;

    box-shadow: none;
}


/* ==========================================
   NEW COLORFUL CERTIFICATE
========================================== */

.certificate {
    width: 100%;

    max-width: 1050px;

    margin: auto;

    padding: 10px;

    background: linear-gradient(
        135deg,
        #6a1b4d,
        #9c426f,
        #d89b58,
        #7b3f61
    );

    border-radius: 12px;

    box-shadow:
        0 15px 40px rgba(90,40,70,0.25);
}

.certificate-inner {
    position: relative;

    background:
        linear-gradient(
            135deg,
            #fff8ef,
            #fffdf8
        );

    border-radius: 7px;

    min-height: 610px;

    padding: 45px 60px;

    text-align: center;

    overflow: hidden;
}


/* Decorative circles */

.certificate-inner::before {
    content: "";

    position: absolute;

    width: 180px;

    height: 180px;

    border-radius: 50%;

    background: rgba(216,155,88,0.16);

    top: -75px;

    left: -70px;
}

.certificate-inner::after {
    content: "";

    position: absolute;

    width: 220px;

    height: 220px;

    border-radius: 50%;

    background: rgba(106,27,77,0.10);

    bottom: -100px;

    right: -80px;
}


/* ==========================================
   TOP LABEL
========================================== */

.certificate-small {
    position: relative;

    z-index: 2;

    font-size: 11px;

    color: #8b536f;

    letter-spacing: 4px;

    font-weight: 600;
}


/* ==========================================
   HEADING
========================================== */

.certificate-heading {
    position: relative;

    z-index: 2;

    margin: 16px 0 8px;

    font-family: 'Cinzel', serif;

    font-size: 36px;

    color: #5a2145;

    font-weight: 700;

    letter-spacing: 1px;

    text-transform: uppercase;
}

.certificate-line {
    position: relative;

    z-index: 2;

    width: 90px;

    height: 4px;

    border-radius: 10px;

    background: linear-gradient(
        90deg,
        #6a1b4d,
        #d89b58,
        #9c426f
    );

    margin: 0 auto 25px;
}


/* ==========================================
   PRESENTED TEXT
========================================== */

.presented {
    position: relative;

    z-index: 2;

    margin: 0;

    color: #77706c;

    font-size: 13px;
}


/* ==========================================
   STUDENT NAME
========================================== */

.student-name {
    position: relative;

    z-index: 2;

    margin: 10px 0 4px;

    font-family: 'Cinzel', serif;

    font-size: 32px;

    color: #6a1b4d;

    font-weight: 700;

    letter-spacing: 1px;
}


/* Department */

.student-department {
    position: relative;

    z-index: 2;

    color: #9b7750;

    font-size: 11px;

    text-transform: uppercase;

    letter-spacing: 2px;
}


/* ==========================================
   ONE LINE SUMMARY
========================================== */

.certificate-text {
    position: relative;

    z-index: 2;

    max-width: 850px;

    margin: 25px auto 0;

    font-size: 13px;

    line-height: 1.7;

    color: #5f5754;
}

.certificate-text strong {
    color: #6a1b4d;

    font-family: 'Cinzel', serif;

    font-size: 15px;
}

.certificate-text .domain {
    color: #b27632;

    font-family: 'Poppins', sans-serif;

    font-size: 13px;

    font-weight: 600;
}


/* ==========================================
   DETAILS
========================================== */

.certificate-details {
    position: relative;

    z-index: 2;

    display: flex;

    justify-content: center;

    gap: 65px;

    margin-top: 35px;

    padding: 20px 0;

    border-top: 1px solid #eadbc9;

    border-bottom: 1px solid #eadbc9;
}

.detail-box {
    min-width: 170px;
}

.detail-label {
    color: #9b7750;

    font-size: 9px;

    letter-spacing: 1.5px;

    font-weight: 600;
}

.detail-value {
    margin-top: 6px;

    color: #4d4140;

    font-size: 12px;

    font-weight: 500;
}


/* ==========================================
   SIGNATURE AREA
========================================== */

.signature-area {
    position: relative;

    z-index: 2;

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    margin-top: 42px;

    padding: 0 35px;
}

.signature {
    width: 190px;

    border-top: 1px solid #6a515a;

    padding-top: 8px;

    color: #756c69;

    font-size: 10px;

    letter-spacing: .3px;
}


/* ==========================================
   ERROR
========================================== */

.error-message {
    background: #f9e7e7;

    border-left: 4px solid #c62828;

    color: #c62828;

    padding: 15px;

    border-radius: 4px;

    font-size: 13px;

    text-align: center;
}


/* ==========================================
   BUTTONS
========================================== */

.print-button {
    display: block;

    margin: 25px auto 0;

    padding: 12px 28px;

    background: #6a1b4d;

    color: white;

    border: none;

    border-radius: 5px;

    font-family: 'Poppins', sans-serif;

    font-size: 13px;

    cursor: pointer;
}

.print-button:hover {
    background: #d89b58;

    color: #4b2639;
}

.back-button {
    display: inline-block;

    margin-top: 20px;

    padding: 10px 22px;

    border: 1px solid #6a1b4d;

    color: #6a1b4d;

    text-decoration: none;

    border-radius: 4px;

    font-size: 13px;
}

.back-button:hover {
    background: #6a1b4d;

    color: white;
}


/* ==========================================
   FOOTER
========================================== */

.footer {
    height: 53px;

    background: #eee8df;

    border-top: 1px solid #d8d0c6;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #8290a0;

    font-size: 10px;

    letter-spacing: .4px;
}


/* ==========================================
   PRINT
========================================== */

@media print {

    .sidebar,
    .top-header,
    .footer,
    .print-button,
    .back-button,
    .page-title,
    .page-subtitle {
        display: none !important;
    }

    .main-content {
        margin-left: 0;

        width: 100%;
    }

    .page-content {
        padding: 0;
    }

    .certificate-card {
        margin: 0;
    }

    .certificate {
        max-width: none;

        box-shadow: none;

        padding: 8px;
    }

    .certificate-inner {
        min-height: 95vh;
    }
}

</style>

</head>


<body>

<div class="dashboard-layout">


<!-- ==========================================
     SIDEBAR
========================================== -->

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


       


        <a href="report.php">

            <span class="menu-icon">📄</span>

            <span>Internship Report</span>

        </a>


        <a href="certificate.php" class="active">

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



<!-- ==========================================
     MAIN CONTENT
========================================== -->

<div class="main-content">


    <div class="top-header">

        <h1>Certificate</h1>

        <div class="system-name">
            Digital Internship Management System
        </div>

    </div>



    <div class="page-content">


        <h2 class="page-title">
            Internship Certificate
        </h2>


        <div class="page-subtitle">
            View and download your internship completion certificate.
        </div>



        <div class="certificate-card">


            <?php if ($certificate): ?>


                <div class="certificate">


                    <div class="certificate-inner">


                        <div class="certificate-small">
                            DIGITAL INTERNSHIP MANAGEMENT SYSTEM
                        </div>


                        <div class="certificate-heading">
                            Certificate of Internship
                        </div>


                        <div class="certificate-line"></div>


                        <div class="presented">
                            This certificate is proudly presented to
                        </div>


                        <div class="student-name">

                            <?php
                            echo htmlspecialchars(
                                $certificate['student_name']
                            );
                            ?>

                        </div>


                        <div class="student-department">

                            <?php
                            echo htmlspecialchars(
                                $certificate['department']
                            );
                            ?>

                        </div>


                        <!-- ONE LINE SUMMARY -->

                        <div class="certificate-text">

                            For successfully completing an internship at

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $certificate['company_name']
                                );
                                ?>
                            </strong>

                            in

                            <span class="domain">

                                <?php
                                echo htmlspecialchars(
                                    $certificate['internship_domain']
                                );
                                ?>

                            </span>

                            with dedication and active participation.

                        </div>



                        <!-- DETAILS -->

                        <div class="certificate-details">


                            <div class="detail-box">

                                <div class="detail-label">
                                    INTERNSHIP PERIOD
                                </div>

                                <div class="detail-value">

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $certificate['start_date']
                                        )
                                    );
                                    ?>

                                    -

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $certificate['end_date']
                                        )
                                    );
                                    ?>

                                </div>

                            </div>



                            <div class="detail-box">

                                <div class="detail-label">
                                    CERTIFICATE DATE
                                </div>

                                <div class="detail-value">

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $certificate['certificate_date']
                                        )
                                    );
                                    ?>

                                </div>

                            </div>



                            <div class="detail-box">

                                <div class="detail-label">
                                    INTERNSHIP GUIDE
                                </div>

                                <div class="detail-value">

                                    <?php
                                    echo htmlspecialchars(
                                        $certificate['guide_name']
                                    );
                                    ?>

                                </div>

                            </div>


                        </div>



                        <!-- SIGNATURES -->

                        <!-- SIGNATURES -->
<div class="signature-area">
    <div class="signature">
        <?php
        echo htmlspecialchars($certificate['guide_name']);
        ?><br>
        Internship Guide
    </div>

    <div class="signature">
        Company Signature<br>
        Authorized Company Representative
    </div>
</div>

                <button
                    class="print-button"
                    onclick="window.print()">

                    🖨 Download / Save Certificate

                </button>


            <?php else: ?>


                <div class="error-message">

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>


            <?php endif; ?>



            <div style="text-align:center;">

                <a
                    href="dashboard.php"
                    class="back-button">

                    ← Back to Dashboard

                </a>

            </div>


        </div>


    </div>



    <div class="footer">

        Digital Internship Management System © 2026

    </div>


</div>

</div>


</body>

</html>