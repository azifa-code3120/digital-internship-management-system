<?php

session_start();

include "db_connect.php";


/* ==============================
   STUDENT LOGIN CHECK
   ============================== */

if (!isset($_SESSION['reg_no'])) {
    header("Location: login.php");
    exit();
}


$reg_no = $_SESSION['reg_no'];


/* ==============================
   MARK NOTIFICATIONS AS READ
   ============================== */

if (isset($_GET['read']) && $_GET['read'] == "all") {

    $sql_read = "UPDATE notifications
                 SET status = 'Read'
                 WHERE reg_no = ?";

    $stmt_read = mysqli_prepare($conn, $sql_read);

    if ($stmt_read) {

        mysqli_stmt_bind_param(
            $stmt_read,
            "s",
            $reg_no
        );

        mysqli_stmt_execute($stmt_read);

        mysqli_stmt_close($stmt_read);
    }

    header("Location: notifications.php");
    exit();
}


/* ==============================
   GET NOTIFICATIONS
   ============================== */

$sql = "SELECT
            notification_id,
            message,
            status,
            created_at
        FROM notifications
        WHERE reg_no = ?
        ORDER BY notification_id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $reg_no
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Notifications | Digital Internship Management System</title>

</head>


<body style="
    margin:0;
    background:#f7f3ec;
    color:#243447;
    font-family:Arial, sans-serif;
">


<div style="
    width:90%;
    max-width:850px;
    margin:50px auto;
">


    <div style="
        background:#243447;
        color:#f3d99b;
        padding:20px 25px;
        border-radius:6px 6px 0 0;
    ">

        <h2 style="margin:0;">
            🔔 Notifications
        </h2>

        <p style="
            margin:7px 0 0;
            color:#e5e9ed;
            font-size:13px;
        ">
            Your internship updates and notifications
        </p>

    </div>


    <div style="
        background:#fffdf9;
        padding:25px;
        border:1px solid #ddd4c8;
    ">


        <div style="
            text-align:right;
            margin-bottom:20px;
        ">

            <a href="notifications.php?read=all"
               style="
                   color:#243447;
                   text-decoration:none;
                   font-size:13px;
               ">

                Mark all as read

            </a>

        </div>


        <?php

        if (mysqli_num_rows($result) > 0) {

            while ($notification = mysqli_fetch_assoc($result)) {

                $background = "#fffdf9";

                if ($notification['status'] == "Unread") {
                    $background = "#f5eee2";
                }

        ?>


                <div style="
                    background:<?php echo $background; ?>;
                    border:1px solid #ddd4c8;
                    padding:18px;
                    margin-bottom:12px;
                    border-radius:5px;
                ">


                    <div style="
                        font-size:14px;
                        font-weight:600;
                        color:#243447;
                    ">

                        <?php
                        echo htmlspecialchars(
                            $notification['message']
                        );
                        ?>

                    </div>


                    <div style="
                        margin-top:8px;
                        font-size:11px;
                        color:#7b858e;
                    ">

                        <?php
                        echo htmlspecialchars(
                            $notification['created_at']
                        );
                        ?>


                        <?php

                        if ($notification['status'] == "Unread") {

                            echo " • Unread";

                        } else {

                            echo " • Read";

                        }

                        ?>

                    </div>


                </div>


        <?php

            }

        } else {

        ?>


            <div style="
                text-align:center;
                padding:50px 20px;
                color:#7b858e;
            ">

                <div style="
                    font-size:35px;
                    margin-bottom:15px;
                ">
                    🔔
                </div>

                <h3 style="
                    margin:0 0 8px;
                    color:#243447;
                ">
                    No Notifications
                </h3>

                <p style="
                    margin:0;
                    font-size:13px;
                ">
                    You don't have any notifications yet.
                </p>

            </div>


        <?php

        }

        ?>


        <div style="
            margin-top:25px;
        ">

            <a href="dashboard.php"
               style="
                   display:inline-block;
                   background:#243447;
                   color:#f3d99b;
                   padding:11px 18px;
                   text-decoration:none;
                   border-radius:4px;
                   font-size:12px;
               ">

                ← Back to Dashboard

            </a>

        </div>


    </div>


</div>


</body>

</html>