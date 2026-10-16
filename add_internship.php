<?php
include "db_connect.php";

$message = "";
$message_type = "";

/* ==============================
   ADD INTERNSHIP
   ============================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_name = trim($_POST['company_name']);
    $internship_domain = trim($_POST['internship_domain']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $guide_name = trim($_POST['guide_name']);
    $status = $_POST['status'];

    if (
        $company_name == "" ||
        $internship_domain == "" ||
        $description == "" ||
        $start_date == "" ||
        $end_date == "" ||
        $guide_name == "" ||
        $status == ""
    ) {

        $message = "Please fill all required fields.";
        $message_type = "error";

    } elseif ($end_date < $start_date) {

        $message = "End date cannot be before start date.";
        $message_type = "error";

    } else {

        $sql = "INSERT INTO internships
                (company_name,
                 internship_domain,
                 description,
                 start_date,
                 end_date,
                 guide_name,
                 status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {

            $message = "Database Error: " . mysqli_error($conn);
            $message_type = "error";

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "sssssss",
                $company_name,
                $internship_domain,
                $description,
                $start_date,
                $end_date,
                $guide_name,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {

                header("Location: manage_internships.php");
                exit();

            } else {

                $message = "Failed to add internship: " . mysqli_error($conn);
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);
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

    <title>Add Internship | Digital Internship Management System</title>

    <!-- Google Fonts -->

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
           PAGE LAYOUT
           ===================================== */

        .page-layout {

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


        /* IMPORTANT:
           Sidebar must NOT move */

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
           SIDEBAR BOTTOM
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

            margin-bottom: 28px;
        }


        .page-header h1 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 27px;

            color: #243447;

            font-weight: 600;

            letter-spacing: 0.5px;
        }


        .page-header h1::after {

            content: "";

            display: block;

            width: 58px;

            height: 2px;

            background: #c6a15b;

            margin-top: 9px;
        }


        .page-header p {

            margin: 9px 0 0;

            color: #66717c;

            font-size: 12px;
        }


        /* =====================================
           FORM CARD
           ===================================== */

        .form-card {

            max-width: 900px;

            margin: 0 auto;

            background: #fffdf9;

            border: 1px solid #ddd4c8;

            border-radius: 8px;

            box-shadow: 0 10px 28px rgba(36,52,71,0.08);

            padding: 34px 38px 36px;
        }


        .form-card-title {

            margin: 0 0 25px;

            font-family: 'Cinzel', serif;

            font-size: 18px;

            color: #243447;

            font-weight: 600;
        }


        .form-card-title::after {

            content: "";

            display: block;

            width: 42px;

            height: 2px;

            background: #c6a15b;

            margin-top: 8px;
        }


        /* =====================================
           FORM GRID
           ===================================== */

        .form-grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 22px 25px;
        }


        .form-group {

            display: flex;

            flex-direction: column;
        }


        .form-group.full-width {

            grid-column: 1 / -1;
        }


        .form-group label {

            margin-bottom: 8px;

            color: #243447;

            font-size: 12px;

            font-weight: 600;

            letter-spacing: 0.2px;
        }


        .form-group input,
        .form-group textarea,
        .form-group select {

            width: 100%;

            padding: 12px 13px;

            border: 1px solid #d7cec2;

            border-radius: 4px;

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

            box-shadow: 0 0 0 2px rgba(198,161,91,0.12);
        }


        .form-group textarea {

            min-height: 120px;

            resize: vertical;
        }


        .form-group input::placeholder,
        .form-group textarea::placeholder {

            color: #999;
        }


        /* =====================================
           BUTTON AREA
           ===================================== */

        .button-area {

            display: flex;

            align-items: center;

            gap: 12px;

            margin-top: 30px;

            padding-top: 25px;

            border-top: 1px solid #e1d9cf;
        }


        .add-btn {

            border: none;

            padding: 12px 25px;

            background: #243447;

            color: #f3d99b;

            border-radius: 4px;

            font-family: 'Poppins', sans-serif;

            font-size: 12px;

            font-weight: 600;

            cursor: pointer;

            transition: none;
        }


        .add-btn:hover {

            background: #c6a15b;

            color: #243447;
        }


        .cancel-btn {

            display: inline-block;

            padding: 11px 22px;

            background: transparent;

            border: 1px solid #c6a15b;

            color: #6b5142;

            text-decoration: none;

            border-radius: 4px;

            font-size: 12px;

            font-weight: 500;
        }


        .cancel-btn:hover {

            background: #c6a15b;

            color: #243447;
        }


        /* =====================================
           MESSAGE
           ===================================== */

        .message {

            margin-bottom: 22px;

            padding: 12px 15px;

            border-radius: 4px;

            font-size: 12px;

            font-weight: 500;
        }


        .message.error {

            background: #f8e8e8;

            color: #b3261e;

            border-left: 4px solid #c62828;
        }


        .message.success {

            background: #eaf5ec;

            color: #2e6b3e;

            border-left: 4px solid #4c9a5f;
        }


        /* =====================================
           RESPONSIVE
           ===================================== */

        @media (max-width: 850px) {

            .sidebar {

                width: 220px;
            }


            .main-content {

                margin-left: 220px;

                width: calc(100% - 220px);

                padding: 30px 20px;
            }


            .form-grid {

                grid-template-columns: 1fr;
            }


            .form-group.full-width {

                grid-column: auto;
            }
        }


        @media (max-width: 650px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;
            }


            .main-content {

                margin-left: 0;

                width: 100%;
            }


            .page-layout {

                display: block;
            }
        }

    </style>

</head>


<body>


<div class="page-layout">


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


            <a href="attendance.php">

                <span class="menu-icon">🗓</span>

                <span>Attendance</span>

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
         MAIN CONTENT
         ===================================== -->

    <main class="main-content">


        <div class="page-header">

            <h1>Add Internship</h1>

            <p>
                Create and register a new internship opportunity.
            </p>

        </div>


        <div class="form-card">


            <h2 class="form-card-title">
                Internship Information
            </h2>


            <?php if ($message != "") { ?>

                <div class="message <?php echo $message_type; ?>">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php } ?>


            <form method="POST">


                <div class="form-grid">


                    <!-- Company Name -->

                    <div class="form-group">

                        <label>
                            Company Name
                        </label>

                        <input
                            type="text"
                            name="company_name"
                            placeholder="Enter company name"
                            required
                        >

                    </div>


                    <!-- Internship Domain -->

                    <div class="form-group">

                        <label>
                            Internship Domain
                        </label>

                        <input
                            type="text"
                            name="internship_domain"
                            placeholder="Example: Web Development"
                            required
                        >

                    </div>


                    <!-- Description -->

                    <div class="form-group full-width">

                        <label>
                            Description
                        </label>

                        <textarea
                            name="description"
                            placeholder="Enter internship description"
                            required
                        ></textarea>

                    </div>


                    <!-- Start Date -->

                    <div class="form-group">

                        <label>
                            Start Date
                        </label>

                        <input
                            type="date"
                            name="start_date"
                            required
                        >

                    </div>


                    <!-- End Date -->

                    <div class="form-group">

                        <label>
                            End Date
                        </label>

                        <input
                            type="date"
                            name="end_date"
                            required
                        >

                    </div>


                    <!-- Guide Name -->

                    <div class="form-group">

                        <label>
                            Guide Name
                        </label>

                        <input
                            type="text"
                            name="guide_name"
                            placeholder="Enter guide name"
                            required
                        >

                    </div>


                    <!-- Status -->

                    <div class="form-group">

                        <label>
                            Status
                        </label>

                        <select name="status" required>

                            <option value="">
                                Select Status
                            </option>

                            <option value="Available">
                                Available
                            </option>

                            <option value="Not Available">
                                Not Available
                            </option>

                        </select>

                    </div>


                </div>


                <!-- BUTTONS -->

                <div class="button-area">


                    <button
                        type="submit"
                        class="add-btn"
                    >
                        + Add Internship
                    </button>


                    <a
                        href="manage_internships.php"
                        class="cancel-btn"
                    >
                        Cancel
                    </a>


                </div>


            </form>


        </div>


    </main>


</div>


</body>

</html>