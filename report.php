<?php
session_start();
include "db_connect.php";

/* =====================================
   STUDENT LOGIN CHECK
===================================== */

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];

$message = "";
$message_type = "";


/* =====================================
   GET APPROVED INTERNSHIP
===================================== */

$approved_internship_id = 0;
$approved_company_name = "";

$sql_internship = "
    SELECT
        ia.internship_id,
        i.company_name
    FROM internship_applications ia
    INNER JOIN internships i
        ON ia.internship_id = i.internship_id
    WHERE ia.reg_no = ?
    AND LOWER(ia.status) = 'approved'
    ORDER BY ia.application_id DESC
    LIMIT 1
";

$stmt_internship = mysqli_prepare($conn, $sql_internship);

if ($stmt_internship) {

    mysqli_stmt_bind_param(
        $stmt_internship,
        "s",
        $reg_no
    );

    mysqli_stmt_execute($stmt_internship);

    $internship_result =
        mysqli_stmt_get_result($stmt_internship);

    if (mysqli_num_rows($internship_result) > 0) {

        $internship_row =
            mysqli_fetch_assoc($internship_result);

        $approved_internship_id =
            $internship_row['internship_id'];

        $approved_company_name =
            $internship_row['company_name'];
    }

    mysqli_stmt_close($stmt_internship);
}


/* =====================================
   REPORT SUBMISSION
===================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $report_title = trim($_POST['report_title']);
    $description = trim($_POST['description']);


    /* Check approved internship */

    if ($approved_internship_id <= 0) {

        $message =
            "You cannot submit a report because no internship has been approved for you.";

        $message_type = "error";

    }

    /* Check title and description */

    elseif ($report_title == "" || $description == "") {

        $message =
            "Please fill all required fields.";

        $message_type = "error";

    }

    /* Check file */

    elseif (
        !isset($_FILES['report']) ||
        $_FILES['report']['error'] != 0
    ) {

        $message =
            "Please select an internship report file.";

        $message_type = "error";

    }

    else {

        $file_name =
            $_FILES['report']['name'];

        $file_tmp =
            $_FILES['report']['tmp_name'];

        $file_size =
            $_FILES['report']['size'];


        $file_ext = strtolower(
            pathinfo(
                $file_name,
                PATHINFO_EXTENSION
            )
        );


        /* Allowed file types */

        $allowed_extensions = [
            "pdf",
            "doc",
            "docx"
        ];


        if (!in_array(
            $file_ext,
            $allowed_extensions
        )) {

            $message =
                "Only PDF, DOC and DOCX files are allowed.";

            $message_type = "error";

        }

        elseif (
            $file_size >
            10 * 1024 * 1024
        ) {

            $message =
                "File size must be less than 10 MB.";

            $message_type = "error";

        }

        else {

            /* Create uploads folder */

            $upload_folder = "uploads/";

            if (!is_dir($upload_folder)) {

                mkdir(
                    $upload_folder,
                    0777,
                    true
                );
            }


            /* Create unique file name */

            $new_file_name =
                $reg_no . "_" .
                time() . "_" .
                basename($file_name);


            $upload_path =
                $upload_folder .
                $new_file_name;


            /* Move uploaded file */

            if (
                move_uploaded_file(
                    $file_tmp,
                    $upload_path
                )
            ) {


                /* =====================================
                   INSERT REPORT WITH INTERNSHIP ID
                ===================================== */

                $sql = "
                    INSERT INTO internship_reports
                    (
                        reg_no,
                        internship_id,
                        report_title,
                        report_file,
                        submission_date,
                        status
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        NOW(),
                        'Pending'
                    )
                ";


                $stmt =
                    mysqli_prepare(
                        $conn,
                        $sql
                    );


                if ($stmt) {


                    mysqli_stmt_bind_param(
                        $stmt,
                        "siss",
                        $reg_no,
                        $approved_internship_id,
                        $report_title,
                        $new_file_name
                    );


                    if (
                        mysqli_stmt_execute(
                            $stmt
                        )
                    ) {

                        $message =
                            "Report submitted successfully!";

                        $message_type =
                            "success";

                    }

                    else {

                        if (
                            file_exists(
                                $upload_path
                            )
                        ) {

                            unlink(
                                $upload_path
                            );
                        }


                        $message =
                            "Database error: " .
                            mysqli_error($conn);

                        $message_type =
                            "error";
                    }


                    mysqli_stmt_close($stmt);

                }

                else {

                    if (
                        file_exists(
                            $upload_path
                        )
                    ) {

                        unlink(
                            $upload_path
                        );
                    }


                    $message =
                        "Database preparation failed.";

                    $message_type =
                        "error";
                }

            }

            else {

                $message =
                    "File upload failed. Please try again.";

                $message_type =
                    "error";
            }
        }
    }
}


/* =====================================
   GET STUDENT REPORTS
===================================== */

$sql_reports = "
    SELECT
        report_id,
        internship_id,
        report_title,
        report_file,
        submission_date,
        status
    FROM internship_reports
    WHERE reg_no = ?
    ORDER BY report_id DESC
";


$stmt_reports =
    mysqli_prepare(
        $conn,
        $sql_reports
    );


if ($stmt_reports) {

    mysqli_stmt_bind_param(
        $stmt_reports,
        "s",
        $reg_no
    );

    mysqli_stmt_execute(
        $stmt_reports
    );

    $reports_result =
        mysqli_stmt_get_result(
            $stmt_reports
        );

}

else {

    $reports_result = false;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
    Internship Report | Digital Internship Management System
</title>


<!-- Elegant Fonts -->

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


/* =====================================
   BODY
===================================== */

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
    overflow-x: hidden;
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
   SIDEBAR HEADER
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


.sidebar-menu a:hover {
    background: rgba(198,161,91,0.17);
    color: #ffffff;
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
    transition: none;
}


.logout-link:hover {
    background: #c6a15b;
    color: #243447;
    transform: none;
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
   REPORT CONTENT
===================================== */

.report-container {
    width: 88%;
    max-width: 1100px;
    margin: 45px auto 50px;
}


/* =====================================
   PAGE INTRO
===================================== */

.report-intro {
    margin-bottom: 28px;
}


.report-intro h2 {
    margin: 0;
    font-family: 'Cinzel', serif;
    font-size: 29px;
    color: #243447;
    font-weight: 600;
}


.report-intro h2::after {
    content: "";
    display: block;
    width: 65px;
    height: 2px;
    background: #c6a15b;
    margin-top: 10px;
}


.report-intro p {
    margin: 12px 0 0;
    color: #707982;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 14px;
}


/* =====================================
   MESSAGE
===================================== */

.message {
    padding: 14px 18px;
    margin-bottom: 25px;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 500;
    border-left: 4px solid;
}


.message.success {
    background: #edf6ed;
    color: #315d35;
    border-left-color: #5d8c61;
}


.message.error {
    background: #faeeee;
    color: #8a3333;
    border-left-color: #b45b5b;
}


/* =====================================
   REPORT CARD
===================================== */

.report-card {
    background: #fffdf9;
    border: 1px solid #d9d0c3;
    border-radius: 6px;
    box-shadow: 0 10px 28px rgba(36,52,71,0.07);
    overflow: hidden;
    margin-bottom: 30px;
}


.report-card::before {
    content: "";
    display: block;
    height: 5px;
    background: #c6a15b;
}


/* =====================================
   CARD HEADER
===================================== */

.card-header {
    padding: 25px 32px;
    border-bottom: 1px solid #e1d9ce;
    background: #fffdf9;
}


.card-header h3 {
    margin: 0;
    font-family: 'Cinzel', serif;
    color: #243447;
    font-size: 20px;
    font-weight: 600;
}


.card-header p {
    margin: 7px 0 0;
    color: #707982;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 13px;
}


/* =====================================
   FORM AREA
===================================== */

.report-form {
    padding: 30px 32px;
}


.form-group {
    margin-bottom: 23px;
}


.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #243447;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}


.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 13px 15px;
    border: 1px solid #d3c7b8;
    border-radius: 4px;
    background: #fffdf9;
    color: #243447;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    outline: none;
}


.form-group input[type="text"]:focus,
.form-group textarea:focus {
    border-color: #c6a15b;
    box-shadow: 0 0 0 2px rgba(198,161,91,0.12);
}


.form-group textarea {
    min-height: 120px;
    resize: vertical;
}


/* =====================================
   FILE UPLOAD
===================================== */

.file-upload-box {
    border: 1px dashed #c6a15b;
    background: #faf7f1;
    padding: 20px;
    border-radius: 5px;
}


.file-upload-box input[type="file"] {
    width: 100%;
    color: #4e5963;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
}


.file-note {
    margin: 9px 0 0;
    color: #7a7f84;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 12px;
}


/* =====================================
   SUBMIT BUTTON
===================================== */

.submit-btn {
    display: inline-block;
    padding: 12px 28px;
    background: #243447;
    color: #f3d99b;
    border: 1px solid #c6a15b;
    border-radius: 4px;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.4px;
    cursor: pointer;
}


.submit-btn:hover {
    background: #c6a15b;
    color: #243447;
}


/* =====================================
   PREVIOUS REPORTS
===================================== */

.previous-reports {
    background: #fffdf9;
    border: 1px solid #d9d0c3;
    border-radius: 6px;
    box-shadow: 0 10px 28px rgba(36,52,71,0.07);
    overflow: hidden;
}


.previous-reports::before {
    content: "";
    display: block;
    height: 5px;
    background: #c6a15b;
}


.previous-header {
    padding: 25px 32px;
    border-bottom: 1px solid #e1d9ce;
}


.previous-header h3 {
    margin: 0;
    font-family: 'Cinzel', serif;
    font-size: 20px;
    color: #243447;
    font-weight: 600;
}


.previous-header p {
    margin: 7px 0 0;
    color: #707982;
    font-size: 12px;
}


/* =====================================
   TABLE
===================================== */

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}


.report-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 700px;
}


.report-table th {
    background: #243447;
    color: #f3d99b;
    padding: 15px 18px;
    text-align: left;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}


.report-table td {
    padding: 16px 18px;
    border-bottom: 1px solid #e1d9ce;
    color: #4e5963;
    font-size: 12px;
    vertical-align: middle;
}


.report-table tbody tr:nth-child(even) {
    background: #faf7f1;
}


.report-table tbody tr:hover {
    background: #f1eadf;
}


.report-table tbody tr:last-child td {
    border-bottom: none;
}


/* Report title */

.table-report-title {
    color: #243447;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 14px;
    font-weight: bold;
}


/* =====================================
   VIEW BUTTON
===================================== */

.file-btn {
    display: inline-block;
    padding: 7px 15px;
    background: #243447;
    color: #f3d99b;
    border: 1px solid #c6a15b;
    border-radius: 4px;
    text-decoration: none;
    font-size: 11px;
    font-weight: 600;
}


.file-btn:hover {
    background: #c6a15b;
    color: #243447;
}


/* =====================================
   STATUS BADGES
===================================== */

.status-badge {
    display: inline-block;
    padding: 6px 13px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}


.pending {
    background: #f3d98b;
    color: #5a4438;
}


.approved {
    background: #c8dfc4;
    color: #285c2a;
}


.rejected {
    background: #e8b8b8;
    color: #7a0000;
}


/* =====================================
   NO REPORTS
===================================== */

.no-reports {
    padding: 55px 25px;
    text-align: center;
}


.no-reports-icon {
    font-size: 35px;
    margin-bottom: 12px;
}


.no-reports h4 {
    margin: 0;
    color: #243447;
    font-family: 'Cinzel', serif;
    font-size: 18px;
}


.no-reports p {
    margin: 8px 0 0;
    color: #707982;
    font-size: 12px;
}


/* =====================================
   BACK BUTTON
===================================== */

.back-area {
    margin-top: 28px;
}


.back-btn {
    display: inline-block;
    padding: 11px 22px;
    background: transparent;
    color: #243447;
    border: 1px solid #c6a15b;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
}


.back-btn:hover {
    background: #c6a15b;
    color: #243447;
}


/* =====================================
   FOOTER
===================================== */

.report-footer {
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

    .report-container {
        width: 92%;
    }
}


@media (max-width: 650px) {

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

    .report-container {
        width: 92%;
        margin-top: 30px;
    }

    .report-form {
        padding: 25px 20px;
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


        <a href="report.php" class="active">

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
            Internship Report
        </h1>

        <span class="system-name">
            Digital Internship Management System
        </span>

    </header>



    <!-- REPORT CONTENT -->

    <section class="report-container">


        <!-- PAGE INTRO -->

        <div class="report-intro">

            <h2>
                Internship Report
            </h2>

            <p>
                Submit your internship report and track your submitted reports.
            </p>

        </div>



        <!-- MESSAGE -->

        <?php if ($message != "") { ?>

            <div class="message <?php echo htmlspecialchars($message_type); ?>">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php } ?>



        <!-- =====================================
             SUBMIT REPORT CARD
        ===================================== -->

        <div class="report-card">


            <div class="card-header">

                <h3>
                    Submit New Report
                </h3>

                <p>
                    Enter your internship report details and upload the document.
                </p>

            </div>


            <form
                method="POST"
                enctype="multipart/form-data"
                class="report-form"
            >


                <!-- Report Title -->

                <div class="form-group">

                    <label>
                        Report Title
                    </label>

                    <input
                        type="text"
                        name="report_title"
                        placeholder="Enter your internship report title"
                        required
                    >

                </div>



                <!-- Description -->

                <div class="form-group">

                    <label>
                        Project Description
                    </label>

                    <textarea
                        name="description"
                        placeholder="Enter a short description of your internship project"
                        required
                    ></textarea>

                </div>



                <!-- File -->

                <div class="form-group">

                    <label>
                        Internship Report Document
                    </label>


                    <div class="file-upload-box">

                        <input
                            type="file"
                            name="report"
                            accept=".pdf,.doc,.docx"
                            required
                        >

                        <p class="file-note">

                            Allowed formats: PDF, DOC, DOCX
                            &nbsp; | &nbsp;
                            Maximum size: 10 MB

                        </p>

                    </div>

                </div>



                <!-- Submit -->

                <button
                    type="submit"
                    class="submit-btn"
                >

                    Submit Report

                </button>


            </form>

        </div>



        <!-- =====================================
             PREVIOUS REPORTS
        ===================================== -->

        <div class="previous-reports">


            <div class="previous-header">

                <h3>
                    My Submitted Reports
                </h3>

                <p>
                    View the reports you have already submitted.
                </p>

            </div>



            <?php if (
                $reports_result &&
                mysqli_num_rows($reports_result) > 0
            ) { ?>


                <div class="table-wrapper">


                    <table class="report-table">


                        <thead>

                            <tr>

                                <th>
                                    Report Title
                                </th>

                                <th>
                                    Submission Date
                                </th>

                                <th>
                                    Report
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php while (
                            $report =
                            mysqli_fetch_assoc(
                                $reports_result
                            )
                        ) { ?>


                            <tr>


                                <td>

                                    <div class="table-report-title">

                                        <?php
                                        echo htmlspecialchars(
                                            $report['report_title']
                                        );
                                        ?>

                                    </div>

                                </td>



                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $report['submission_date']
                                    );
                                    ?>

                                </td>



                                <td>

                                    <a
                                        href="uploads/<?php echo rawurlencode($report['report_file']); ?>"
                                        target="_blank"
                                        class="file-btn"
                                    >

                                        View Report

                                    </a>

                                </td>



                                <td>


                                    <?php

                                    $report_status =
                                        $report['status'];


                                    if (
                                        $report_status ==
                                        "Approved"
                                    ) {

                                    ?>

                                        <span class="status-badge approved">

                                            Approved

                                        </span>


                                    <?php

                                    }

                                    elseif (
                                        $report_status ==
                                        "Rejected"
                                    ) {

                                    ?>

                                        <span class="status-badge rejected">

                                            Rejected

                                        </span>


                                    <?php

                                    }

                                    else {

                                    ?>

                                        <span class="status-badge pending">

                                            Pending

                                        </span>

                                    <?php } ?>


                                </td>


                            </tr>


                        <?php } ?>


                        </tbody>


                    </table>


                </div>


            <?php } else { ?>


                <div class="no-reports">

                    <div class="no-reports-icon">
                        📄
                    </div>

                    <h4>
                        No Reports Submitted
                    </h4>

                    <p>
                        You have not submitted any internship reports yet.
                    </p>

                </div>


            <?php } ?>


        </div>



        <!-- BACK -->

        <div class="back-area">

            <a
                href="dashboard.php"
                class="back-btn"
            >

                ← Back to Dashboard

            </a>

        </div>


    </section>



    <!-- FOOTER -->

    <footer class="report-footer">

        Digital Internship Management System © 2026

    </footer>


</main>


</div>


</body>

</html>