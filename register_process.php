<?php

include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_name = trim($_POST["student_name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $department = trim($_POST["department"]);
    $password = $_POST["password"];

    /*
     * Register Number is no longer entered by the student.
     * For compatibility with existing project pages,
     * username is automatically used as reg_no.
     */
    $reg_no = $username;

    /*
     * Default year of study
     */
    $year_of_study = 1;

    /*
     * Hash the password
     */
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    /* ==========================
       CHECK USERNAME
       ========================== */

    $check_sql = "SELECT username FROM student WHERE username = ?";

    $check_stmt = mysqli_prepare($conn, $check_sql);

    mysqli_stmt_bind_param(
        $check_stmt,
        "s",
        $username
    );

    mysqli_stmt_execute($check_stmt);

    $check_result = mysqli_stmt_get_result($check_stmt);


    if (mysqli_num_rows($check_result) > 0) {

        echo "
        <script>
            alert('Username already exists. Please choose another username.');
            window.location.href = 'student_register.php';
        </script>
        ";

        exit();
    }

    mysqli_stmt_close($check_stmt);


    /* ==========================
       INSERT STUDENT
       ========================== */

    $sql = "INSERT INTO student
            (reg_no, username, student_name, department, year_of_study, email, password)
            VALUES (?, ?, ?, ?, ?, ?, ?)";


    $stmt = mysqli_prepare($conn, $sql);


    if (!$stmt) {

        die("Database Error: " . mysqli_error($conn));

    }


    mysqli_stmt_bind_param(
        $stmt,
        "ssssiss",
        $reg_no,
        $username,
        $student_name,
        $department,
        $year_of_study,
        $email,
        $hashed_password
    );


    if (mysqli_stmt_execute($stmt)) {

        echo "
        <script>
            alert('Registration successful! Please login.');
            window.location.href = 'login.php';
        </script>
        ";

    } else {

        echo "Registration failed: " . mysqli_error($conn);

    }


    mysqli_stmt_close($stmt);
    mysqli_close($conn);

}

?>