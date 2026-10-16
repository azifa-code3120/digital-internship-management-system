<?php
include "db_connect.php";

/* ==============================
   UPDATE INTERNSHIP STATUS
   ============================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $application_id = intval($_POST['application_id']);
    $status = $_POST['status'];

    $allowed_status = ["Applied", "Approved", "Completed", "Rejected"];

    if (in_array($status, $allowed_status)) {

        $sql = "UPDATE internship_applications
                SET status = ?
                WHERE application_id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $status,
                $application_id
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: admin_status.php");
    exit();
}


/* ==============================
   GET INTERNSHIP STATUS
   ============================== */

$sql = "SELECT
            ia.application_id,
            ia.reg_no,
            ia.status,
            s.student_name,
            i.company_name,
            i.internship_domain
        FROM internship_applications ia

        INNER JOIN student s
            ON ia.reg_no = s.reg_no

        INNER JOIN internships i
            ON ia.internship_id = i.internship_id

        ORDER BY ia.application_id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Internship Status</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .status-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .status-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            overflow-x: auto;
        }

        .status-title {
            text-align: center;
            color: #4b3a2f;
            margin-bottom: 30px;
        }

        .status-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .status-table th {
            background: #4b3a2f;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .status-table td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
        }

        .status-table tr:hover {
            background: #f8f3eb;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 20px;
            font-weight: bold;
            background: #f1d27a;
            color: #4b3a2f;
        }

        .update-btn {
            padding: 9px 16px;
            background: #6b5142;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .update-btn:hover {
            background: #4b3a2f;
        }

        .status-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .status-form select {
            padding: 8px;
            border: 1px solid #b8a99d;
            border-radius: 6px;
            background: white;
            color: #4b3a2f;
        }

        .save-btn {
            padding: 8px 14px;
            background: #4b3a2f;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .save-btn:hover {
            background: #6b5142;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            background: #4b3a2f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

    </style>

</head>


<body>


<div class="status-container">

    <div class="status-card">

        <h1 class="status-title">
            Internship Status
        </h1>


        <?php if ($result && mysqli_num_rows($result) > 0) { ?>


        <table class="status-table">

            <thead>

                <tr>

                    <th>S.No</th>

                    <th>Student Name</th>

                    <th>Register No</th>

                    <th>Company</th>

                    <th>Domain</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>


            <?php

            $count = 1;

            while ($row = mysqli_fetch_assoc($result)) {

            ?>

                <tr>

                    <td>
                        <?php echo $count; ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars($row['student_name']);
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars($row['reg_no']);
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars($row['company_name']);
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars($row['internship_domain']);
                        ?>
                    </td>


                    <td>

                        <span class="status-badge">

                            <?php
                            echo htmlspecialchars($row['status']);
                            ?>

                        </span>

                    </td>


                    <td>

                        <form
                            method="POST"
                            class="status-form"
                        >

                            <input
                                type="hidden"
                                name="application_id"
                                value="<?php echo $row['application_id']; ?>"
                            >


                            <select name="status">

                                <option value="Applied"
                                    <?php
                                    if ($row['status'] == "Applied")
                                        echo "selected";
                                    ?>
                                >
                                    Applied
                                </option>


                                <option value="Approved"
                                    <?php
                                    if ($row['status'] == "Approved")
                                        echo "selected";
                                    ?>
                                >
                                    Approved
                                </option>


                                <option value="Completed"
                                    <?php
                                    if ($row['status'] == "Completed")
                                        echo "selected";
                                    ?>
                                >
                                    Completed
                                </option>


                                <option value="Rejected"
                                    <?php
                                    if ($row['status'] == "Rejected")
                                        echo "selected";
                                    ?>
                                >
                                    Rejected
                                </option>

                            </select>


                            <button
                                type="submit"
                                class="save-btn"
                            >
                                Update
                            </button>

                        </form>

                    </td>

                </tr>


            <?php

                $count++;

            }

            ?>


            </tbody>

        </table>


        <?php } else { ?>


            <div class="no-data">

                <h3>No Applications Found</h3>

                <p>
                    No students have applied for internships yet.
                </p>

            </div>


        <?php } ?>


        <a
            href="admin_dashboard.php"
            class="back-btn"
        >
            ← Back to Admin Dashboard
        </a>


    </div>

</div>


</body>

</html>