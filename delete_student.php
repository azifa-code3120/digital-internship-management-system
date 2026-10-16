<?php

session_start();

include "db_connect.php";


/* =========================================
   ADMIN ACCESS CHECK
========================================= */

if (!isset($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true) {

    header("Location: admin_login.php");
    exit();
}


/* =========================================
   CHECK STUDENT
========================================= */

if (!isset($_GET['reg_no'])) {

    header("Location: manage_students.php");
    exit();
}


$reg_no = $_GET['reg_no'];


/* =========================================
   DELETE STUDENT
========================================= */

$sql = "DELETE FROM student
        WHERE reg_no = ?";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $reg_no
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}


/* =========================================
   BACK TO STUDENTS
========================================= */

header("Location: manage_students.php");
exit();

?>