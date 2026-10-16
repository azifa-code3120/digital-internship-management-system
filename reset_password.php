<?php

include "db_connect.php";

$reg_no = "26ma006";
$new_password = "123456";

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE student SET password = ? WHERE reg_no = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $reg_no);

if (mysqli_stmt_execute($stmt)) {
    echo "PASSWORD RESET SUCCESS";
} else {
    echo "ERROR: " . mysqli_error($conn);
}

?>