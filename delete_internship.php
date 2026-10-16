<?php

include "db_connect.php";

/* Get Internship ID */

if (!isset($_GET['id'])) {
    die("Invalid Internship ID.");
}

$internship_id = intval($_GET['id']);


/* Delete Internship */

$sql = "DELETE FROM internships
        WHERE internship_id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database error.");
}

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $internship_id
);


if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: manage_internships.php");
    exit();

} else {

    mysqli_stmt_close($stmt);

    die("Failed to delete internship.");
}

?>