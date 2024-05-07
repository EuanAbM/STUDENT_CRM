<?php
require 'dbconnect.inc';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!empty($_POST['delete_ids'])) {
    echo "Deleting records: " . implode(", ", $_POST['delete_ids']) . "<br>";
    // Process each checked item to delete
    foreach ($_POST['delete_ids'] as $studentId) {
        $studentId = intval($studentId); // Ensure it's an integer to prevent SQL injection
        echo "Processing student ID: $studentId<br>";

        // Delete related records from the emergency_details table
        $deleteEmergencyDetailsSql = "DELETE FROM emergency_details WHERE studentid = $studentId";
        if (mysqli_query($conn, $deleteEmergencyDetailsSql)) {
            echo "Emergency details deleted for student ID: $studentId<br>";
        } else {
            echo "Error deleting emergency details: " . mysqli_error($conn) . "<br>";
        }

        // Delete the record from the student table
        $deleteStudentSql = "DELETE FROM student WHERE studentid = $studentId";
        if (mysqli_query($conn, $deleteStudentSql)) {
            echo "Student record deleted for student ID: $studentId<br>";
        } else {
            echo "Error deleting student record: " . mysqli_error($conn) . "<br>";
        }
    }
    echo "Redirecting to students.php<br>";
    // Redirect back to the student list
    // header('Location: students.php');
    // exit; // Terminate script execution after redirection
} else {
    echo '<div class="alert alert-danger mt-3">No records selected for deletion.</div>';
}
?>
