<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Digital Internship Management System</title>

    <!-- Elegant Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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
        }


        /* =========================
           MAIN PAGE
        ========================= */

        .home-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }


        /* =========================
           TOP NAVIGATION
        ========================= */

        .top-bar {
            height: 72px;
            background: #243447;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 55px;
            border-bottom: 2px solid #c6a15b;
        }


        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }


        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #f7f3ec;
            color: #243447;
            border: 2px solid #c6a15b;

            display: flex;
            align-items: center;
            justify-content: center;

            font-family: 'Cinzel', serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
        }


        .brand-name {
            color: #f3d99b;
            font-family: 'Cinzel', serif;
            font-size: 16px;
            letter-spacing: 1px;
        }


        .portal-label {
            color: #e5e9ed;
            font-size: 12px;
            letter-spacing: 1.5px;
        }


        /* =========================
           HERO SECTION
        ========================= */

        .hero-section {
            width: 90%;
            max-width: 1100px;
            margin: auto;

            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 70px;

            padding: 65px 0;
        }


        /* =========================
           HERO LEFT
        ========================= */

        .hero-content {
            padding: 10px;
        }


        .small-heading {
            color: #b08a46;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }


        .hero-content h1 {
            margin: 0;

            font-family: 'Cinzel', serif;
            font-size: 42px;
            line-height: 1.25;
            font-weight: 600;

            color: #243447;
        }


        .hero-content h1 span {
            color: #b08a46;
        }


        .hero-divider {
            width: 85px;
            height: 3px;
            background: #c6a15b;
            margin: 25px 0;
        }


        .hero-content p {
            margin: 0;

            max-width: 570px;

            color: #69727c;
            font-size: 15px;
            line-height: 1.9;
        }


        /* =========================
           FEATURE ROW
        ========================= */

        .features {
            display: flex;
            gap: 28px;
            margin-top: 32px;
        }


        .feature {
            display: flex;
            align-items: center;
            gap: 9px;

            color: #4f5964;
            font-size: 12px;
        }


        .feature-icon {
            width: 27px;
            height: 27px;

            border-radius: 50%;

            background: #243447;
            color: #f3d99b;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 13px;
        }


        /* =========================
           STUDENT LOGIN CARD
        ========================= */

        .student-card {
            position: relative;

            background: #fffdf9;

            padding: 42px 38px;

            text-align: center;

            border: 1px solid #d8d0c4;
            border-radius: 6px;

            box-shadow: 0 15px 40px rgba(36,52,71,0.12);
        }


        .student-card::before {
            content: "";

            position: absolute;

            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;

            border: 1px solid #eee5d8;

            pointer-events: none;
        }


        /* =========================
           ICON
        ========================= */

        .student-icon {
            width: 78px;
            height: 78px;

            margin: 0 auto 20px;

            border-radius: 50%;

            background: #243447;
            color: white;

            border: 3px solid #c6a15b;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 34px;

            position: relative;
            z-index: 1;
        }


        .student-card h2 {
            margin: 5px 0 12px;

            font-family: 'Cinzel', serif;
            font-size: 25px;
            font-weight: 600;

            color: #243447;

            position: relative;
            z-index: 1;
        }


        .student-card .subtitle {
            color: #b08a46;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;

            position: relative;
            z-index: 1;
        }


        .student-card p {
            margin: 20px auto 28px;

            color: #69727c;

            font-size: 13px;
            line-height: 1.8;

            max-width: 330px;

            position: relative;
            z-index: 1;
        }


        /* =========================
           LOGIN BUTTON
        ========================= */

        .login-btn {
            display: inline-block;

            min-width: 190px;

            padding: 13px 28px;

            background: #243447;
            color: #ffffff !important;

            text-decoration: none;

            border: 1px solid #243447;
            border-radius: 3px;

            font-size: 14px;
            font-weight: 500;

            letter-spacing: 0.5px;

            position: relative;
            z-index: 1;

            transition: 0.3s ease;
        }


        .login-btn:hover {
            background: #c6a15b;
            border-color: #c6a15b;
            color: #243447 !important;
        }


        /* =========================
           REGISTER TEXT
        ========================= */

        .register-text {
            margin-top: 22px !important;
            margin-bottom: 0 !important;

            font-size: 12px !important;

            position: relative;
            z-index: 1;
        }


        .register-text a {
            color: #9a7538;
            font-weight: 600;
            text-decoration: none;
        }


        .register-text a:hover {
            text-decoration: underline;
        }


        /* =========================
           BOTTOM INFO
        ========================= */

        .info-section {
            width: 90%;
            max-width: 1100px;

            margin: 0 auto 45px;

            display: grid;
            grid-template-columns: repeat(3, 1fr);

            gap: 20px;
        }


        .info-box {
            background: #eee9e1;

            border: 1px solid #d8d0c4;

            padding: 18px 20px;

            text-align: center;

            border-radius: 4px;
        }


        .info-box strong {
            display: block;

            font-family: 'Cinzel', serif;

            color: #243447;

            font-size: 15px;

            margin-bottom: 5px;
        }


        .info-box span {
            color: #707780;

            font-size: 11px;
        }


        /* =========================
           FOOTER
        ========================= */

        .home-footer {
            margin-top: auto;

            text-align: center;

            padding: 20px 15px;

            color: #707780;

            font-size: 11px;

            line-height: 1.8;

            border-top: 1px solid #d8d0c4;

            background: #e9e4dc;

            letter-spacing: 0.4px;
        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 850px) {

            .top-bar {
                padding: 0 25px;
            }


            .portal-label {
                display: none;
            }


            .hero-section {
                grid-template-columns: 1fr;

                gap: 35px;

                max-width: 600px;

                padding: 50px 0;
            }


            .hero-content {
                text-align: center;
            }


            .hero-content h1 {
                font-size: 32px;
            }


            .hero-divider {
                margin: 22px auto;
            }


            .hero-content p {
                margin: auto;
            }


            .features {
                justify-content: center;
                flex-wrap: wrap;
            }


            .info-section {
                grid-template-columns: 1fr;

                max-width: 500px;
            }

        }


        @media (max-width: 500px) {

            .brand-name {
                font-size: 13px;
            }


            .hero-content h1 {
                font-size: 27px;
            }


            .student-card {
                padding: 35px 25px;
            }

        }

    </style>

</head>


<body>


<div class="home-container">


    <!-- =========================
         TOP BAR
    ========================= -->

    <div class="top-bar">

        <div class="brand">

            <div class="brand-logo">
                DIML
            </div>

            <div class="brand-name">
                Digital Internship
            </div>

        </div>


        <div class="portal-label">
            STUDENT INTERNSHIP PORTAL
        </div>

    </div>



    <!-- =========================
         HERO SECTION
    ========================= -->

    <section class="hero-section">


        <!-- LEFT SIDE -->

        <div class="hero-content">

            <div class="small-heading">
                Internship Management Portal
            </div>


            <h1>

                Build Your
                <span>Professional</span>
                Journey

            </h1>


            <div class="hero-divider"></div>


            <p>

                A digital platform designed to help students
                manage their internship journey — from
                application and progress tracking to reports,
                attendance and certification.

            </p>


            <div class="features">

                <div class="feature">

                    <div class="feature-icon">
                        ✓
                    </div>

                    Easy Application

                </div>


                <div class="feature">

                    <div class="feature-icon">
                        ✓
                    </div>

                    Track Progress

                </div>


                <div class="feature">

                    <div class="feature-icon">
                        ✓
                    </div>

                    Digital Certificate

                </div>

            </div>

        </div>



        <!-- RIGHT SIDE : STUDENT ONLY -->

        <div class="student-card">


            <div class="student-icon">
                👩‍🎓
            </div>


            <div class="subtitle">
                Student Portal
            </div>


            <h2>
                Welcome, Student
            </h2>


            <p>

                Login to access internship opportunities,
                submit applications, track your internship
                progress, attend meetings and manage reports.

            </p>


            <a href="login.php"
               class="login-btn">

                Student Login

            </a>


            <p class="register-text">

                New student?
                <a href="student_register.php">
                    Register Here
                </a>

            </p>


        </div>


    </section>



    <!-- =========================
         INFORMATION BOXES
    ========================= -->

    <section class="info-section">


        <div class="info-box">

            <strong>
                Internship
            </strong>

            <span>
                Explore and apply for opportunities
            </span>

        </div>


        <div class="info-box">

            <strong>
                Progress
            </strong>

            <span>
                Monitor your internship activities
            </span>

        </div>


        <div class="info-box">

            <strong>
                Certification
            </strong>

            <span>
                Access your internship certificate
            </span>

        </div>


    </section>



    <!-- =========================
         FOOTER
    ========================= -->

    <footer class="home-footer">

        © 2026 Digital Internship Management System

        <br>

        Academic Internship Management Portal

    </footer>


</div>


</body>

</html>