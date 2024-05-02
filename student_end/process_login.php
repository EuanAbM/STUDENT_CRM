<?php
session_start();
require '../_includes/dbconnect.inc';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentId = $_POST['studentId'];
    $password = $_POST['password'];

    // Fetch student data based on student ID
    $sql = "SELECT * FROM student WHERE studentid = '$studentId'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);

        // Verify password using password_verify
        if (password_verify($password, $student['password'])) {
            // Password is correct, set session variables and redirect to dashboard
            $_SESSION['studentId'] = $student['studentid'];
            $_SESSION['firstname'] = $student['firstname'];
            header("Location: student_dashboard.php");
            exit();
        } else {
            // Password is incorrect
            $_SESSION['login_error'] = "Invalid student ID or password.";
            header("Location: student_login.php");
            exit();
        }
    } else {
        // Student ID not found
        $_SESSION['login_error'] = "Invalid student ID or password.";
        header("Location: student_login.php");
        exit();
    }
}
?>
