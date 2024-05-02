<?php
require 'dbconnect.inc';

if (isset($_GET['id'])) {
    $studentId = intval($_GET['id']); // Sanitize input as integer to prevent SQL injection

    // Delete related records from the emergency_details table
    $deleteEmergencyDetailsSql = "DELETE FROM emergency_details WHERE studentid = $studentId";
    mysqli_query($conn, $deleteEmergencyDetailsSql);

    // Now you can delete the record from the student table
    $deleteStudentSql = "DELETE FROM student WHERE studentid = $studentId";

    if(mysqli_query($conn, $deleteStudentSql)) {
        echo '<div class="alert alert-success mt-3">Record deleted successfully.</div>';
    } else {
        echo '<div class="alert alert-danger mt-3">Error deleting record: ' . mysqli_error($conn) . '</div>';
    }

    mysqli_close($conn);
    header('Location: students.php'); // Redirect back to the student list page after deletion
    exit; // Terminate script execution after redirection
} else {
    // If no 'id' parameter is provided or invalid request
    echo '<div class="alert alert-warning mt-3">Invalid request. No student ID provided.</div>';
}
?>