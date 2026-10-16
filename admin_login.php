<?php
session_start();

/* =========================================
   ADMIN LOGIN
   ========================================= */

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    /* Admin Login Credentials */

    $admin_username = "admin";
    $admin_password = "admin123";

    if ($username === $admin_username && $password === $admin_password) {

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;

        header("Location: admin_dashboard.php");
        exit();

    } else {

        $message = "Invalid Username or Password.";

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Digital Internship Management System</title>

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
    min-height: 100vh;

    display: flex;
    justify-content: center;
    align-items: center;

    background:
        radial-gradient(
            circle at 15% 20%,
            rgba(216, 182, 108, 0.35),
            transparent 30%
        ),
        radial-gradient(
            circle at 85% 80%,
            rgba(36, 52, 71, 0.9),
            transparent 45%
        ),
        linear-gradient(
            135deg,
            #243447 0%,
            #344b63 45%,
            #d8b66c 100%
        );

    color: #243447;

    font-family: 'Poppins', sans-serif;
}
        /* =================================
           LOGIN CONTAINER
           ================================= */

        .login-container {

            width: 430px;

            background: #fffdf9;

            padding: 45px 45px 35px;

            border: 1px solid #ddd4c8;

            border-radius: 8px;

            box-shadow:
                0 12px 35px rgba(36,52,71,0.12);

            text-align: center;

        }


        /* =================================
           ADMIN ICON
           ================================= */

        .admin-icon {

            width: 62px;

            height: 62px;

            margin: 0 auto 22px;

            border-radius: 50%;

            background: #243447;

            color: #f3d99b;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 27px;

            border: 2px solid #c6a15b;

        }


        /* =================================
           TITLE
           ================================= */

        .login-container h1 {

            margin: 0;

            font-family: 'Cinzel', serif;

            font-size: 28px;

            color: #243447;

            font-weight: 600;

            letter-spacing: 0.5px;

        }


        .login-container h1::after {

            content: "";

            display: block;

            width: 58px;

            height: 2px;

            background: #c6a15b;

            margin: 10px auto 15px;

        }


        .login-container > p {

            margin: 0 0 30px;

            color: #66717c;

            font-size: 12px;

            letter-spacing: 0.5px;

        }


        /* =================================
           ERROR MESSAGE
           ================================= */

        .login-message {

            background: #f8e8e8;

            color: #b3261e;

            border-left: 4px solid #c62828;

            padding: 12px 14px;

            border-radius: 4px;

            margin-bottom: 22px;

            text-align: left;

            font-size: 12px;

            font-weight: 500;

        }


        /* =================================
           FORM
           ================================= */

        form {

            text-align: left;

        }


        label {

            display: block;

            margin-bottom: 8px;

            color: #243447;

            font-size: 13px;

            font-weight: 500;

        }


        input {

            width: 100%;

            padding: 13px 14px;

            margin-bottom: 20px;

            border: 1px solid #d7cec2;

            border-radius: 4px;

            background: #fffdf9;

            color: #243447;

            font-family: 'Poppins', sans-serif;

            font-size: 13px;

            outline: none;

        }


        input:focus {

            border-color: #c6a15b;

            box-shadow:
                0 0 0 2px rgba(198,161,91,0.12);

        }


        input::placeholder {

            color: #9a9a9a;

        }


        /* =================================
           LOGIN BUTTON
           ================================= */

        .login-btn {

            width: 100%;

            padding: 13px;

            margin-top: 5px;

            border: none;

            border-radius: 4px;

            background: #243447;

            color: #f3d99b;

            font-family: 'Poppins', sans-serif;

            font-size: 13px;

            font-weight: 600;

            letter-spacing: 0.4px;

            cursor: pointer;

        }


        .login-btn:hover {

            background: #c6a15b;

            color: #243447;

        }


        /* =================================
           HOME BUTTON
           ================================= */

        .home-btn {

            display: inline-block;

            margin-top: 25px;

            padding: 10px 20px;

            border: 1px solid #c6a15b;

            background: transparent;

            color: #6b5142;

            text-decoration: none;

            border-radius: 4px;

            font-size: 12px;

            font-weight: 500;

        }


        .home-btn:hover {

            background: #c6a15b;

            color: #243447;

        }


        /* =================================
           FOOTER
           ================================= */

        .login-footer {

            margin-top: 25px;

            padding-top: 18px;

            border-top: 1px solid #e1d9cf;

            color: #8a8f94;

            font-size: 10px;

            letter-spacing: 0.5px;

        }


        /* =================================
           MOBILE
           ================================= */

        @media (max-width: 500px) {

            .login-container {

                width: calc(100% - 30px);

                padding: 35px 25px;

            }

        }

    </style>

</head>


<body>


<div class="login-container">


    <!-- ADMIN ICON -->

    <div class="admin-icon">
        ♙
    </div>


    <!-- TITLE -->

    <h1>
        Admin Login
    </h1>


    <p>
        Digital Internship Management System
    </p>


    <!-- ERROR MESSAGE -->

    <?php if ($message != "") { ?>

        <div class="login-message">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php } ?>


    <!-- LOGIN FORM -->

    <form method="POST">


        <label>
            Username
        </label>


        <input
            type="text"
            name="username"
            placeholder="Enter admin username"
            autocomplete="username"
            required
        >


        <label>
            Password
        </label>


        <input
            type="password"
            name="password"
            placeholder="Enter admin password"
            autocomplete="current-password"
            required
        >


        <button
            type="submit"
            class="login-btn"
        >
            Login
        </button>


    </form>


    <!-- BACK TO HOME -->

    <a
        href="index.php"
        class="home-btn"
    >
        ← Back to Home
    </a>


    <!-- FOOTER -->

    <div class="login-footer">

        Admin Panel • Internship Management System

    </div>


</div>


</body>

</html>