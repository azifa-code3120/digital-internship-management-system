<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Student Registration | Digital Internship Management System</title>

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
           MAIN PAGE
        ========================= */

        .register-page {

            min-height: calc(100vh - 7px);

            display: flex;

            flex-direction: column;
        }


        /* =========================
           PAGE HEADER
        ========================= */

        .page-header {

            text-align: center;

            padding-top: 42px;

            padding-bottom: 22px;
        }


        .small-logo {

            width: 62px;

            height: 62px;

            margin: 0 auto 16px;

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

            margin: 9px 0 0;

            color: #6d747c;

            font-size: 14px;

            letter-spacing: 0.5px;
        }


        /* =========================
           DIVIDER
        ========================= */

        .divider {

            width: 180px;

            margin: 14px auto 0;

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
           REGISTRATION BOX
        ========================= */

        .register-wrapper {

            width: 90%;

            max-width: 900px;

            margin: 18px auto 40px;

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

        .register-info {

            background: #243447;

            color: white;

            padding: 40px 38px;

            display: flex;

            flex-direction: column;

            justify-content: center;

            position: relative;
        }


        .register-info::before {

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

            margin-bottom: 18px;

            position: relative;

            z-index: 1;
        }


        .register-info h2 {

            margin: 0 0 15px;

            font-family: 'Cinzel', serif;

            font-size: 24px;

            font-weight: 600;

            color: #f3d99b;

            position: relative;

            z-index: 1;
        }


        .info-line {

            width: 60px;

            height: 2px;

            background: #c6a15b;

            margin: 20px 0;

            position: relative;

            z-index: 1;
        }


        .register-info p {

            margin: 0;

            color: #e0e5e9;

            font-size: 13px;

            line-height: 1.9;

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
           FORM SECTION
        ========================= */

        .register-form-section {

            padding: 38px 45px 35px;

            background: #fffdf9;
        }


        .form-title {

            margin: 0 0 7px;

            font-family: 'Cinzel', serif;

            font-size: 23px;

            color: #243447;

            text-align: center;
        }


        .form-subtitle {

            text-align: center;

            margin: 0 0 24px;

            color: #7a8086;

            font-size: 13px;
        }


        /* =========================
           FORM GROUP
        ========================= */

        .form-group {

            margin-bottom: 16px;
        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

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

            padding: 11px 14px;

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
           PASSWORD EYE
        ========================= */

        .password-wrapper {

            position: relative;

            width: 100%;
        }


        .password-wrapper input {

            padding-right: 45px;
        }


        .toggle-password {

            position: absolute;

            right: 12px;

            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            color: #6d747c;

            font-size: 17px;

            cursor: pointer;

            padding: 0;

            line-height: 1;
        }


        .toggle-password:hover {

            color: #243447;
        }


        /* =========================
           REGISTER BUTTON
        ========================= */

        .register-button {

            width: 100%;

            padding: 12px;

            margin-top: 4px;

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


        .register-button:hover {

            background: #c6a15b;

            border-color: #c6a15b;

            color: #243447;
        }


        /* =========================
           LOGIN AREA
        ========================= */

        .login-area {

            text-align: center;

            margin-top: 20px;

            padding-top: 17px;

            border-top: 1px solid #e5ddd2;

            font-size: 13px;

            color: #777;
        }


        .login-area a {

            color: #243447;

            font-weight: 600;

            text-decoration: none;
        }


        .login-area a:hover {

            color: #b28b42;

            text-decoration: underline;
        }


        /* =========================
           BACK HOME
        ========================= */

        .back-home {

            text-align: center;

            margin-top: 15px;
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

        .register-footer {

            margin-top: auto;

            padding: 17px;

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

            .register-wrapper {

                grid-template-columns: 1fr;

                max-width: 500px;
            }


            .register-info {

                padding: 35px;

                text-align: center;
            }


            .info-line {

                margin: 20px auto;
            }


            .register-form-section {

                padding: 35px 30px;
            }


            .page-header h1 {

                font-size: 27px;
            }
        }

    </style>

</head>


<body>


<div class="register-page">


    <!-- TOP LINE -->

    <div class="top-line"></div>


    <!-- HEADER -->

    <header class="page-header">


        <div class="small-logo">

            DIML

        </div>


        <h1>

            Student Registration

        </h1>


        <div class="divider">

            <span>◆</span>

        </div>


        <p>

            Create your Digital Internship Portal account

        </p>


    </header>


    <!-- REGISTRATION BOX -->

    <div class="register-wrapper">


        <!-- LEFT INFORMATION -->

        <div class="register-info">


            <div class="info-icon">

                🎓

            </div>


            <h2>

                Student Portal

            </h2>


            <div class="info-line"></div>


            <p>

                Create your account to access
                the Digital Internship Management System.

            </p>


            <br>


            <div class="info-text">

                Register to explore internship opportunities,
                submit applications, monitor your internship
                progress, submit reports and access your certificate.

            </div>


        </div>


        <!-- RIGHT FORM -->

        <div class="register-form-section">


            <h2 class="form-title">

                Create Account

            </h2>


            <p class="form-subtitle">

                Enter your details to get started

            </p>


            <form action="register_process.php"
                  method="POST">


                <!-- STUDENT NAME -->

                <div class="form-group">

                    <label>

                        Student Name

                    </label>

                    <input
                        type="text"
                        name="student_name"
                        placeholder="Enter your name"
                        required
                    >

                </div>


                <!-- USERNAME -->

                <div class="form-group">

                    <label>

                        Username

                    </label>

                    <input
                        type="text"
                        name="username"
                        placeholder="Create a username"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label>

                        Email

                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <!-- DEPARTMENT -->

                <div class="form-group">

                    <label>

                        Department

                    </label>

                    <input
                        type="text"
                        name="department"
                        placeholder="Enter your department"
                        required
                    >

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label>

                        Password

                    </label>


                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Create a password"
                            required
                        >


                        <button
                            type="button"
                            class="toggle-password"
                            onclick="togglePassword()"
                            aria-label="Show password">

                            👁️

                        </button>

                    </div>

                </div>


                <!-- REGISTER -->

                <button
                    type="submit"
                    class="register-button">

                    Create Student Account

                </button>


            </form>


            <!-- LOGIN -->

            <div class="login-area">

                Already have an account?

                <a href="login.php">

                    Login Here

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

    <footer class="register-footer">

        © 2026 Digital Internship Management System
        | Academic Internship Management Portal

    </footer>


</div>


<!-- PASSWORD SHOW / HIDE -->

<script>

function togglePassword() {

    const password = document.getElementById("password");

    const eye = document.querySelector(".toggle-password");


    if (password.type === "password") {

        password.type = "text";

        eye.textContent = "🙈";

        eye.setAttribute(
            "aria-label",
            "Hide password"
        );

    } else {

        password.type = "password";

        eye.textContent = "👁️";

        eye.setAttribute(
            "aria-label",
            "Show password"
        );

    }

}

</script>


</body>

</html>