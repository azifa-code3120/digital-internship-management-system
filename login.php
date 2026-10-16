<?php
session_start();
include "db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    /* ==============================
       LOGIN USING USERNAME
       ============================== */

    $sql = "SELECT username, reg_no, password
            FROM student
            WHERE username = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {

        die("Database Error: " . mysqli_error($conn));

    }

    mysqli_stmt_bind_param($stmt, "s", $username);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $student = mysqli_fetch_assoc($result);

        if (password_verify($password, $student["password"])) {

            $_SESSION["reg_no"] = $student["reg_no"];

            $_SESSION["username"] = $student["username"];

            header("Location: dashboard.php");
            exit();

        } else {

            $message = "Incorrect Password";

        }

    } else {

        $message = "Username Not Found";

    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Student Login | Digital Internship Management System</title>

    <link rel="stylesheet" href="style.css">

    <!-- Elegant Fonts -->

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

            font-family: 'Poppins', sans-serif;

            background: #f7f3ec;

            color: #30343b;

            min-height: 100vh;
        }


        /* =========================
           TOP LINE
        ========================= */

        .top-line {

            height: 7px;

            background: #243447;

            border-bottom: 2px solid #c6a15b;
        }


        /* =========================
           MAIN WRAPPER
        ========================= */

        .login-page {

            min-height: calc(100vh - 7px);

            display: flex;

            flex-direction: column;
        }


        /* =========================
           PAGE HEADER
        ========================= */

        .page-header {

            text-align: center;

            padding-top: 48px;

            padding-bottom: 25px;
        }


        .small-logo {

            width: 62px;

            height: 62px;

            margin: 0 auto 18px;

            border-radius: 50%;

            background: #243447;

            color: #f3d99b;

            display: flex;

            align-items: center;

            justify-content: center;

            font-family: 'Cinzel', serif;

            font-size: 18px;

            font-weight: 700;

            letter-spacing: 1px;

            border: 3px solid #c6a15b;

            box-shadow: 0 5px 15px rgba(36,52,71,0.15);
        }


        .page-header h1 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 32px;

            font-weight: 600;

            color: #243447;

            letter-spacing: 0.5px;
        }


        .page-header p {

            margin: 10px 0 0;

            color: #6d747c;

            font-size: 14px;

            letter-spacing: 0.5px;
        }


        /* =========================
           DECORATIVE DIVIDER
        ========================= */

        .divider {

            width: 180px;

            margin: 15px auto 0;

            display: flex;

            align-items: center;

            gap: 9px;
        }


        .divider::before,
        .divider::after {

            content: "";

            height: 1px;

            background: #c6a15b;

            flex: 1;
        }


        .divider span {

            color: #c6a15b;

            font-size: 12px;
        }


        /* =========================
           LOGIN AREA
        ========================= */

        .login-wrapper {

            width: 90%;

            max-width: 900px;

            margin: 20px auto 45px;

            display: grid;

            grid-template-columns: 0.9fr 1.1fr;

            background: #fffdf9;

            border: 1px solid #d8d0c4;

            box-shadow: 0 12px 35px rgba(36,52,71,0.12);

            border-radius: 5px;

            overflow: hidden;
        }


        /* =========================
           LEFT INFORMATION PANEL
        ========================= */

        .login-info {

            background: #243447;

            color: white;

            padding: 45px 38px;

            display: flex;

            flex-direction: column;

            justify-content: center;

            position: relative;
        }


        .login-info::before {

            content: "";

            position: absolute;

            top: 15px;

            left: 15px;

            right: 15px;

            bottom: 15px;

            border: 1px solid rgba(198,161,91,0.5);

            pointer-events: none;
        }


        .info-icon {

            font-size: 38px;

            margin-bottom: 20px;

            position: relative;

            z-index: 1;
        }


        .login-info h2 {

            margin: 0 0 15px;

            font-family: 'Cinzel', serif;

            font-size: 24px;

            font-weight: 600;

            color: #f3d99b;

            position: relative;

            z-index: 1;
        }


        .login-info p {

            margin: 0;

            color: #e0e5e9;

            font-size: 13px;

            line-height: 1.9;

            position: relative;

            z-index: 1;
        }


        .info-line {

            width: 60px;

            height: 2px;

            background: #c6a15b;

            margin: 22px 0;

            position: relative;

            z-index: 1;
        }


        .info-text {

            font-size: 12px;

            color: #cbd1d6;

            line-height: 1.8;

            position: relative;

            z-index: 1;
        }


        /* =========================
           FORM PANEL
        ========================= */

        .login-form-section {

            padding: 45px 45px 40px;

            background: #fffdf9;
        }


        .form-title {

            margin: 0 0 8px;

            font-family: 'Cinzel', serif;

            font-size: 23px;

            color: #243447;

            text-align: center;
        }


        .form-subtitle {

            text-align: center;

            margin: 0 0 28px;

            color: #7a8086;

            font-size: 13px;
        }


        /* =========================
           ERROR MESSAGE
        ========================= */

        .error-message {

            background: #f8e9e6;

            border-left: 4px solid #8b3e32;

            color: #7a3027;

            padding: 11px 13px;

            margin-bottom: 20px;

            font-size: 13px;

            border-radius: 2px;
        }


        /* =========================
           FORM LABEL
        ========================= */

        .form-group {

            margin-bottom: 20px;
        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            color: #243447;

            font-size: 13px;

            font-weight: 600;

            letter-spacing: 0.3px;
        }


        /* =========================
           INPUT
        ========================= */

        .form-group input {

            width: 100%;

            padding: 13px 14px;

            border: 1px solid #d3c8ba;

            background: #fffefa;

            color: #30343b;

            font-family: 'Poppins', sans-serif;

            font-size: 13px;

            border-radius: 3px;

            outline: none;

            transition: 0.3s;
        }


        .form-group input:focus {

            border-color: #c6a15b;

            box-shadow: 0 0 0 3px rgba(198,161,91,0.12);
        }


        .form-group input::placeholder {

            color: #9b9b9b;
        }


        /* =========================
           LOGIN BUTTON
        ========================= */

        .login-button {

            width: 100%;

            padding: 13px;

            margin-top: 5px;

            border: 1px solid #243447;

            background: #243447;

            color: #ffffff;

            font-family: 'Poppins', sans-serif;

            font-size: 14px;

            font-weight: 500;

            letter-spacing: 0.6px;

            border-radius: 3px;

            cursor: pointer;

            transition: 0.3s;
        }


        .login-button:hover {

            background: #c6a15b;

            border-color: #c6a15b;

            color: #243447;
        }


        /* =========================
           REGISTER AREA
        ========================= */

        .register-area {

            text-align: center;

            margin-top: 24px;

            padding-top: 20px;

            border-top: 1px solid #e5ddd2;

            font-size: 13px;

            color: #777;
        }


        .register-area a {

            color: #243447;

            font-weight: 600;

            text-decoration: none;
        }


        .register-area a:hover {

            color: #b28b42;

            text-decoration: underline;
        }


        /* =========================
           BACK HOME
        ========================= */

        .back-home {

            text-align: center;

            margin-top: 18px;
        }


        .back-home a {

            color: #6d747c;

            text-decoration: none;

            font-size: 12px;

            transition: 0.3s;
        }


        .back-home a:hover {

            color: #243447;
        }


        /* =========================
           FOOTER
        ========================= */

        .login-footer {

            margin-top: auto;

            padding: 18px;

            text-align: center;

            border-top: 1px solid #d8d0c4;

            background: #eee9e1;

            color: #747a80;

            font-size: 11px;

            letter-spacing: 0.3px;
        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 750px) {

            .login-wrapper {

                grid-template-columns: 1fr;

                max-width: 500px;
            }


            .login-info {

                padding: 35px;

                text-align: center;
            }


            .info-line {

                margin: 20px auto;
            }


            .login-form-section {

                padding: 35px 30px;
            }


            .page-header h1 {

                font-size: 27px;
            }
        }

    </style>

</head>


<body>


<div class="login-page">


    <!-- TOP LINE -->

    <div class="top-line"></div>


    <!-- HEADER -->

    <header class="page-header">


        <div class="small-logo">

            DIML

        </div>


        <h1>

            Student Login

        </h1>


        <div class="divider">

            <span>◆</span>

        </div>


        <p>

            Access your Digital Internship Portal

        </p>


    </header>


    <!-- LOGIN BOX -->

    <div class="login-wrapper">


        <!-- LEFT PANEL -->

        <div class="login-info">


            <div class="info-icon">

                🎓

            </div>


            <h2>

                Student Portal

            </h2>


            <div class="info-line"></div>


            <p>

                Welcome to the Digital Internship
                Management System.

            </p>


            <br>


            <div class="info-text">

                Login to access internship opportunities,
                submit applications, monitor your progress,
                manage reports and view your internship certificate.

            </div>


        </div>


        <!-- RIGHT FORM -->

        <div class="login-form-section">


            <h2 class="form-title">

                Sign In

            </h2>


            <p class="form-subtitle">

                Enter your credentials to continue

            </p>


            <?php if ($message != "") { ?>

                <div class="error-message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php } ?>


            <form method="POST"
                  action="login.php">


                <!-- USERNAME -->

                <div class="form-group">


                    <label for="username">

                        Username

                    </label>


                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        required
                    >

                </div>


                <!-- PASSWORD -->

                <div class="form-group">


                    <label for="password">

                        Password

                    </label>


                    <div style="
                        position:relative;
                        width:100%;
                    ">


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                            style="
                                width:100%;
                                padding:13px 45px 13px 14px;
                            "
                        >


                        <!-- CLASSIC EYE -->

                        <span
                            id="passwordEye"
                            onclick="togglePassword()"
                            title="Show password"
                            style="
                                position:absolute;
                                right:12px;
                                top:50%;
                                transform:translateY(-50%);
                                width:24px;
                                height:24px;
                                cursor:pointer;
                                color:#6d747c;
                                z-index:10;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            "
                        >


                            <!-- OPEN EYE -->

                            <svg
                                id="eyeOpen"
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                style="
                                    display:block;
                                    width:20px;
                                    height:20px;
                                "
                            >

                                <path
                                    d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.8"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />

                            </svg>


                            <!-- CLOSED EYE -->

                            <svg
                                id="eyeClosed"
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                style="
                                    display:none;
                                    width:20px;
                                    height:20px;
                                "
                            >

                                <path
                                    d="M3 3l18 18"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />

                                <path
                                    d="M10.6 6.2C11.1 6.1 11.5 6 12 6c6.5 0 10 6 10 6a17 17 0 0 1-3.2 3.8"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />

                                <path
                                    d="M6.2 6.7C3.5 8.3 2 12 2 12s3.5 6 10 6c1.1 0 2.2-.2 3.2-.6"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />

                            </svg>


                        </span>


                    </div>


                </div>


                <!-- LOGIN -->

                <button
                    type="submit"
                    class="login-button">

                    Login to Portal

                </button>


            </form>


            <!-- REGISTER -->

            <div class="register-area">

                New student?

                <a href="student_register.php">

                    Register Here

                </a>

            </div>


            <!-- BACK -->

            <div class="back-home">

                <a href="index.php">

                    ← Back to Home

                </a>

            </div>


        </div>


    </div>


    <!-- FOOTER -->

    <footer class="login-footer">

        © 2026 Digital Internship Management System
        | Academic Internship Management Portal

    </footer>


</div>


<!-- =========================
     PASSWORD SHOW / HIDE
========================= -->

<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const eyeOpen =
        document.getElementById("eyeOpen");

    const eyeClosed =
        document.getElementById("eyeClosed");

    const eye =
        document.getElementById("passwordEye");


    if (password.type === "password") {

        password.type = "text";

        eyeOpen.style.display = "none";

        eyeClosed.style.display = "block";

        eye.title = "Hide password";

    } else {

        password.type = "password";

        eyeOpen.style.display = "block";

        eyeClosed.style.display = "none";

        eye.title = "Show password";

    }

}

</script>


</body>

</html>