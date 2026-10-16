<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

$reg_no = $_SESSION['reg_no'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $report_title = trim($_POST["report_title"]);

    if (isset($_FILES["report_file"]) && $_FILES["report_file"]["error"] == 0) {

        $file_name = $_FILES["report_file"]["name"];
        $file_tmp = $_FILES["report_file"]["tmp_name"];
        $file_size = $_FILES["report_file"]["size"];

        $file_extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        /* Only PDF allowed */
        if ($file_extension != "pdf") {

            $message = "Only PDF files are allowed.";
            $message_type = "error";

        } elseif ($file_size > 5 * 1024 * 1024) {

            $message = "File size must be less than 5 MB.";
            $message_type = "error";

        } else {

            $upload_folder = "uploads/";

            if (!is_dir($upload_folder)) {
                mkdir($upload_folder, 0777, true);
            }

            $new_file_name =
                $reg_no . "_" . time() . ".pdf";

            $file_path =
                $upload_folder . $new_file_name;

            if (move_uploaded_file($file_tmp, $file_path)) {

                $sql = "INSERT INTO internship_reports
                        (reg_no, report_title, report_file, status)
                        VALUES (?, ?, ?, 'Pending')";

                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "sss",
                    $reg_no,
                    $report_title,
                    $new_file_name
                );

                if (mysqli_stmt_execute($stmt)) {

                    $message =
                        "Report submitted successfully!";

                    $message_type = "success";

                } else {

                    $message =
                        "Database error. Please try again.";

                    $message_type = "error";
                }

                mysqli_stmt_close($stmt);

            } else {

                $message =
                    "Unable to upload the file.";

                $message_type = "error";
            }
        }

    } else {

        $message = "Please select a PDF report.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Internship Report</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .report-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
        }

        .report-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .report-title {
            text-align: center;
            color: #4b3a2f;
            margin-bottom: 10px;
        }

        .report-subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        .report-form label {
            display: block;
            margin-top: 18px;
            margin-bottom: 8px;
            color: #5a4438;
            font-weight: bold;
        }

        .report-form input[type="text"],
        .report-form input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .report-form button {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #4b3a2f;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .report-form button:hover {
            background: #6b5142;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 8px;
        }

        .success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .error {
            background: #fdecea;
            color: #b71c1c;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            background: #4b3a2f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

    </style>

</head>

<body>

<div class="report-container">

    <div class="report-card">

        <h1 class="report-title">
            Internship Report
        </h1>

        <p class="report-subtitle">
            Submit your internship report
        </p>

        <?php if ($message != "") { ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php } ?>


        <form
            class="report-form"
            action="internship_report.php"
            method="POST"
            enctype="multipart/form-data"
        >

            <label>Report Title</label>

            <input
                type="text"
                name="report_title"
                placeholder="Enter report title"
                required
            >


            <label>Upload Report (PDF only)</label>

            <input
                type="file"
                name="report_file"
                accept=".pdf"
                required
            >


            <button type="submit">
                Submit Report
            </button>

        </form>


        <a
            href="dashboard.php"
            class="back-btn"
        >
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>

</html>