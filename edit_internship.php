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
   GET INTERNSHIP ID
   ============================== */

if (isset($_GET['internship_id'])) {
    $internship_id = intval($_GET['internship_id']);
} elseif (isset($_GET['id'])) {
    /* Supports old links also */
    $internship_id = intval($_GET['id']);
} else {
    die("Internship not found.");
}


/* ==============================
   UPDATE INTERNSHIP
   ============================== */

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_name = trim($_POST['company_name']);
    $internship_domain = trim($_POST['internship_domain']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $guide_name = trim($_POST['guide_name']);
    $status = $_POST['status'];


    $sql = "UPDATE internships
            SET company_name = ?,
                internship_domain = ?,
                description = ?,
                start_date = ?,
                end_date = ?,
                guide_name = ?,
                status = ?
            WHERE internship_id = ?";


    $stmt = mysqli_prepare($conn, $sql);


    if (!$stmt) {

        $message = "Database error: " . mysqli_error($conn);
        $message_type = "error";

    } else {

        mysqli_stmt_bind_param(
            $stmt,
            "sssssssi",
            $company_name,
            $internship_domain,
            $description,
            $start_date,
            $end_date,
            $guide_name,
            $status,
            $internship_id
        );


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header("Location: manage_internships.php");
            exit();

        } else {

            $message = "Update failed. Please try again.";
            $message_type = "error";

            mysqli_stmt_close($stmt);
        }
    }
}


/* ==============================
   GET EXISTING INTERNSHIP
   ============================== */

$sql = "SELECT
            internship_id,
            company_name,
            internship_domain,
            description,
            start_date,
            end_date,
            guide_name,
            status
        FROM internships
        WHERE internship_id = ?";


$stmt = mysqli_prepare($conn, $sql);


if (!$stmt) {
    die("Database Error: " . mysqli_error($conn));
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $internship_id
);


mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$internship = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


if (!$internship) {
    die("Internship not found.");
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Internship | Digital Internship Management System</title>


    <!-- Google Fonts -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap"
          rel="stylesheet">


    <style>

        /* =========================================
           GLOBAL
           ========================================= */

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

            /* No movement */

            transition: none;

        }


        /* Hover without movement */

        .sidebar-menu a:hover {

            background: rgba(198,161,91,.17);

            color: #fff;

            padding-left: 14px;

            transform: none;

        }


        /* Active */

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

            padding: 40px;

        }


        /* =========================================
           PAGE HEADER
           ========================================= */

        .page-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 25px;

        }


        .page-header h1 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 27px;

            font-weight: 600;

            color: #243447;

            letter-spacing: .5px;

        }


        .page-header h1::after {

            content: "";

            display: block;

            width: 55px;

            height: 2px;

            background: #c6a15b;

            margin-top: 9px;

        }


        .page-subtitle {

            color: #66717c;

            font-size: 12px;

            margin-top: 7px;

        }


        /* =========================================
           EDIT CARD
           ========================================= */

        .edit-card {

            max-width: 900px;

            margin: 0 auto;

            background: #fffdf9;

            border: 1px solid #ddd4c8;

            border-radius: 8px;

            box-shadow: 0 8px 25px rgba(36,52,71,0.08);

            overflow: hidden;

        }


        /* =========================================
           CARD HEADER
           ========================================= */

        .card-header {

            padding: 22px 28px;

            background: #243447;

            border-bottom: 3px solid #c6a15b;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .card-header h2 {

            margin: 0;

            color: #f3d99b;

            font-family: 'Cinzel', serif;

            font-size: 18px;

            font-weight: 600;

        }


        .internship-id {

            padding: 7px 13px;

            border: 1px solid rgba(243,217,155,.5);

            border-radius: 20px;

            color: #f3d99b;

            font-size: 11px;

        }


        /* =========================================
           FORM AREA
           ========================================= */

        .form-area {

            padding: 30px;

        }


        .form-row {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 22px;

            margin-bottom: 22px;

        }


        .form-group {

            margin-bottom: 0;

        }


        .form-group.full {

            grid-column: 1 / -1;

        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            color: #243447;

            font-size: 12px;

            font-weight: 600;

            letter-spacing: .2px;

        }


        .required {

            color: #b08a43;

        }


        .form-group input,

        .form-group textarea,

        .form-group select {

            width: 100%;

            padding: 12px 14px;

            border: 1px solid #d8cdbc;

            border-radius: 5px;

            background: #fffdf9;

            color: #243447;

            font-family: 'Poppins', sans-serif;

            font-size: 13px;

            outline: none;

        }


        .form-group input:focus,

        .form-group textarea:focus,

        .form-group select:focus {

            border-color: #c6a15b;

            box-shadow: 0 0 0 2px rgba(198,161,91,.12);

        }


        .form-group textarea {

            min-height: 120px;

            resize: vertical;

            line-height: 1.6;

        }


        /* =========================================
           MESSAGE
           ========================================= */

        .message {

            margin-bottom: 22px;

            padding: 12px 15px;

            border-radius: 5px;

            font-size: 12px;

            text-align: center;

        }


        .message.error {

            background: #f9eaea;

            border: 1px solid #e2bcbc;

            color: #9c2d2d;

        }


        /* =========================================
           FORM ACTIONS
           ========================================= */

        .form-actions {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-top: 28px;

            padding-top: 22px;

            border-top: 1px solid #e2d9cd;

        }


        .back-btn {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 11px 18px;

            background: #f2ede5;

            border: 1px solid #d7cbbb;

            color: #243447;

            text-decoration: none;

            border-radius: 5px;

            font-size: 12px;

            font-weight: 500;

        }


        .back-btn:hover {

            background: #e8dfd2;

        }


        .update-btn {

            padding: 12px 24px;

            background: #c6a15b;

            color: #243447;

            border: none;

            border-radius: 5px;

            font-family: 'Poppins', sans-serif;

            font-size: 12px;

            font-weight: 600;

            cursor: pointer;

        }


        .update-btn:hover {

            background: #d8b66c;

        }


        /* =========================================
           RESPONSIVE
           ========================================= */

        @media (max-width: 850px) {

            .sidebar {

                width: 220px;

            }

            .main-content {

                margin-left: 220px;

                width: calc(100% - 220px);

                padding: 25px;

            }

            .form-row {

                grid-template-columns: 1fr;

            }

            .form-group.full {

                grid-column: auto;

            }

        }


        @media (max-width: 650px) {

            .sidebar {

                width: 200px;

            }

            .main-content {

                margin-left: 200px;

                width: calc(100% - 200px);

                padding: 18px;

            }

            .form-area {

                padding: 20px;

            }

            .card-header {

                padding: 18px;

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

            <h2>ADMIN PORTAL</h2>

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


            <a href="manage_internships.php"
               class="active">

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


            <a href="admin_attendance.php">

                <span class="menu-icon">🗓️</span>

                <span>Attendance</span>

            </a>


            <a href="issue_certificate.php">

                <span class="menu-icon">🎓</span>

                <span>Issue Certificate</span>

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



    <!-- =========================================
         RIGHT SIDE
         ========================================= -->

    <main class="main-content">


        <!-- Page Heading -->

        <div class="page-header">

            <div>

                <h1>
                    Edit Internship
                </h1>

                <div class="page-subtitle">
                    Update and maintain internship information.
                </div>

            </div>

        </div>



        <!-- =====================================
             EDIT CARD
             ===================================== -->

        <div class="edit-card">


            <div class="card-header">

                <h2>
                    Internship Information
                </h2>


                <div class="internship-id">

                    Internship ID:
                    <?php
                    echo htmlspecialchars(
                        $internship['internship_id']
                    );
                    ?>

                </div>

            </div>



            <div class="form-area">


                <?php if ($message != "") { ?>

                    <div class="message <?php echo $message_type; ?>">

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </div>

                <?php } ?>



                <form method="POST">


                    <!-- Row 1 -->

                    <div class="form-row">


                        <div class="form-group">

                            <label>
                                Company Name
                                <span class="required">*</span>
                            </label>


                            <input
                                type="text"
                                name="company_name"
                                value="<?php
                                echo htmlspecialchars(
                                    $internship['company_name']
                                );
                                ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                Internship Domain
                                <span class="required">*</span>
                            </label>


                            <input
                                type="text"
                                name="internship_domain"
                                value="<?php
                                echo htmlspecialchars(
                                    $internship['internship_domain']
                                );
                                ?>"
                                required
                            >

                        </div>


                    </div>



                    <!-- Description -->

                    <div class="form-row">


                        <div class="form-group full">

                            <label>
                                Internship Description
                                <span class="required">*</span>
                            </label>


                            <textarea
                                name="description"
                                required
                            ><?php
                            echo htmlspecialchars(
                                $internship['description']
                            );
                            ?></textarea>

                        </div>


                    </div>



                    <!-- Dates -->

                    <div class="form-row">


                        <div class="form-group">

                            <label>
                                Start Date
                                <span class="required">*</span>
                            </label>


                            <input
                                type="date"
                                name="start_date"
                                value="<?php
                                echo htmlspecialchars(
                                    $internship['start_date']
                                );
                                ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                End Date
                                <span class="required">*</span>
                            </label>


                            <input
                                type="date"
                                name="end_date"
                                value="<?php
                                echo htmlspecialchars(
                                    $internship['end_date']
                                );
                                ?>"
                                required
                            >

                        </div>


                    </div>



                    <!-- Guide + Status -->

                    <div class="form-row">


                        <div class="form-group">

                            <label>
                                Guide Name
                                <span class="required">*</span>
                            </label>


                            <input
                                type="text"
                                name="guide_name"
                                value="<?php
                                echo htmlspecialchars(
                                    $internship['guide_name']
                                );
                                ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                Internship Status
                                <span class="required">*</span>
                            </label>


                            <select
                                name="status"
                                required
                            >


                                <option
                                    value="Available"
                                    <?php
                                    if (
                                        $internship['status']
                                        == 'Available'
                                    ) {
                                        echo 'selected';
                                    }
                                    ?>
                                >
                                    Available
                                </option>


                                <option
                                    value="Not Available"
                                    <?php
                                    if (
                                        $internship['status']
                                        == 'Not Available'
                                    ) {
                                        echo 'selected';
                                    }
                                    ?>
                                >
                                    Not Available
                                </option>


                            </select>

                        </div>


                    </div>



                    <!-- Buttons -->

                    <div class="form-actions">


                        <a
                            href="manage_internships.php"
                            class="back-btn"
                        >
                            ← Back to Internships
                        </a>


                        <button
                            type="submit"
                            class="update-btn"
                        >
                            ✓ Update Internship
                        </button>


                    </div>


                </form>


            </div>


        </div>


    </main>


</div>


</body>

</html>