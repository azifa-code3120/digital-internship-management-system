<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "db_connect.php";


/* ==============================
   LOGIN CHECK
============================== */

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}


/* ==============================
   CHECK INTERNSHIP ID
============================== */

if (!isset($_GET['internship_id'])) {
    die("Invalid Internship.");
}

$internship_id = intval($_GET['internship_id']);

$reg_no = $_SESSION['reg_no'];


/* ==============================
   FETCH INTERNSHIP
============================== */

$sql = "SELECT *
        FROM internships
        WHERE internship_id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $internship_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Internship not found.");
}

$internship = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* ==============================
   CHECK ALREADY APPLIED
============================== */

$check_sql = "SELECT *
              FROM internship_applications
              WHERE reg_no = ?
              AND internship_id = ?";

$check_stmt = mysqli_prepare($conn, $check_sql);

if (!$check_stmt) {
    die("Database Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $check_stmt,
    "si",
    $reg_no,
    $internship_id
);

mysqli_stmt_execute($check_stmt);

$check_result = mysqli_stmt_get_result($check_stmt);

$already_applied = mysqli_num_rows($check_result) > 0;

mysqli_stmt_close($check_stmt);


/* ==============================
   APPLY
============================== */

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$already_applied) {

    $status = "Pending";

    $insert_sql = "INSERT INTO internship_applications
                   (reg_no, internship_id, status)
                   VALUES (?, ?, ?)";

    $insert_stmt = mysqli_prepare($conn, $insert_sql);

    if (!$insert_stmt) {

        die("Database Error: " . mysqli_error($conn));

    }

    mysqli_stmt_bind_param(
        $insert_stmt,
        "sis",
        $reg_no,
        $internship_id,
        $status
    );


    if (mysqli_stmt_execute($insert_stmt)) {

        $message = "Application submitted successfully.";
        $message_type = "success";

        $already_applied = true;

    } else {

        $message = "Application failed: " . mysqli_error($conn);
        $message_type = "error";
    }

    mysqli_stmt_close($insert_stmt);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Apply Internship</title>


<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    background: #f7f3ec;

    color: #243447;

    font-family: Georgia, "Times New Roman", serif;
}


/* ==============================
   MAIN
============================== */

.apply-container {

    width: 90%;

    max-width: 800px;

    margin: 60px auto;
}


/* ==============================
   CARD
============================== */

.apply-card {

    background: #fffdf9;

    border: 1px solid #d8d0c4;

    border-radius: 7px;

    overflow: hidden;

    box-shadow: 0 8px 22px rgba(36,52,71,0.10);
}


.gold-line {

    height: 5px;

    background: #c6a15b;
}


.card-content {

    padding: 40px;
}


.label {

    color: #9a7a42;

    font-family: Arial, sans-serif;

    font-size: 10px;

    font-weight: bold;

    text-transform: uppercase;

    letter-spacing: 1.3px;

    margin-bottom: 8px;
}


h1 {

    margin: 0;

    color: #243447;

    font-size: 28px;
}


.domain {

    margin-top: 8px;

    color: #707982;

    font-family: Arial, sans-serif;

    font-size: 14px;
}


/* ==============================
   DETAILS
============================== */

.details {

    margin-top: 30px;

    border-top: 1px solid #e1d9ce;
}


.row {

    display: flex;

    padding: 17px 5px;

    border-bottom: 1px solid #e3ddd4;
}


.row:last-child {

    border-bottom: none;
}


.row-label {

    width: 30%;

    color: #9a7a42;

    font-family: Arial, sans-serif;

    font-size: 10px;

    font-weight: bold;

    text-transform: uppercase;

    letter-spacing: 1px;
}


.row-value {

    width: 70%;

    color: #243447;

    font-family: Arial, sans-serif;

    font-size: 14px;

    line-height: 1.6;
}


/* ==============================
   MESSAGE
============================== */

.message {

    margin-top: 25px;

    padding: 14px 18px;

    border-radius: 4px;

    font-family: Arial, sans-serif;

    font-size: 13px;
}


.success {

    background: #e8f1e8;

    border: 1px solid #b8cdb8;

    color: #315431;
}


.error {

    background: #f5e6e3;

    border: 1px solid #d5b5ae;

    color: #733b31;
}


/* ==============================
   BUTTONS
============================== */

.button-area {

    margin-top: 30px;

    display: flex;

    justify-content: space-between;

    align-items: center;
}


.back-btn {

    padding: 11px 22px;

    background: transparent;

    color: #243447;

    border: 1px solid #243447;

    border-radius: 4px;

    text-decoration: none;

    font-family: Arial, sans-serif;

    font-size: 13px;

    font-weight: bold;
}


.back-btn:hover {

    background: #243447;

    color: #f3d99b;
}


.apply-btn {

    padding: 12px 27px;

    background: #243447;

    color: #f3d99b;

    border: 1px solid #c6a15b;

    border-radius: 4px;

    font-family: Arial, sans-serif;

    font-size: 13px;

    font-weight: bold;

    cursor: pointer;
}


.apply-btn:hover {

    background: #c6a15b;

    color: #243447;
}


.applied-btn {

    padding: 12px 27px;

    background: #c6a15b;

    color: #243447;

    border: none;

    border-radius: 4px;

    font-family: Arial, sans-serif;

    font-size: 13px;

    font-weight: bold;
}


/* ==============================
   MOBILE
============================== */

@media (max-width: 650px) {

    .apply-container {

        width: 94%;

        margin: 30px auto;
    }

    .card-content {

        padding: 25px;
    }

    .row {

        flex-direction: column;

        gap: 7px;
    }

    .row-label,
    .row-value {

        width: 100%;
    }

    .button-area {

        flex-direction: column;

        gap: 15px;

        align-items: stretch;
    }

    .back-btn,
    .apply-btn,
    .applied-btn {

        text-align: center;

        width: 100%;
    }
}

</style>

</head>


<body>


<div class="apply-container">


    <div class="apply-card">


        <div class="gold-line"></div>


        <div class="card-content">


            <div class="label">
                Internship Application
            </div>


            <h1>
                <?php
                echo htmlspecialchars(
                    $internship['company_name']
                );
                ?>
            </h1>


            <div class="domain">

                Domain:
                <?php
                echo htmlspecialchars(
                    $internship['internship_domain']
                );
                ?>

            </div>


            <div class="details">


                <div class="row">

                    <div class="row-label">
                        Description
                    </div>

                    <div class="row-value">

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $internship['description']
                            )
                        );
                        ?>

                    </div>

                </div>


                <div class="row">

                    <div class="row-label">
                        Start Date
                    </div>

                    <div class="row-value">

                        <?php
                        echo htmlspecialchars(
                            $internship['start_date']
                        );
                        ?>

                    </div>

                </div>


                <div class="row">

                    <div class="row-label">
                        End Date
                    </div>

                    <div class="row-value">

                        <?php
                        echo htmlspecialchars(
                            $internship['end_date']
                        );
                        ?>

                    </div>

                </div>


                <div class="row">

                    <div class="row-label">
                        Guide
                    </div>

                    <div class="row-value">

                        <?php
                        echo htmlspecialchars(
                            $internship['guide_name']
                        );
                        ?>

                    </div>

                </div>


            </div>


            <?php if ($message != "") { ?>

                <div class="message <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php } ?>


            <form method="POST">


                <div class="button-area">


                    <a
                        href="internship.php"
                        class="back-btn"
                    >
                        ← Back to Internships
                    </a>


                    <?php if ($already_applied) { ?>

                        <span class="applied-btn">
                            Already Applied
                        </span>

                    <?php } else { ?>

                        <button
                            type="submit"
                            class="apply-btn"
                        >
                            Confirm Application
                        </button>

                    <?php } ?>


                </div>


            </form>


        </div>


    </div>


</div>


</body>

</html>