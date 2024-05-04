<?php
require 'dbconnect.inc';

if(isset($_POST['present']) && isset($_POST['absent']) && isset($_POST['medical']) && isset($_POST['studentId'])) {
    $present = (int)$_POST['present'];
    $absent = (int)$_POST['absent'];
    $medical = (int)$_POST['medical'];
    $studentId = $conn->real_escape_string($_POST['studentId']); // Sanitize the studentId

    error_log("Received data: present = $present, absent = $absent, medical = $medical, studentId = $studentId");

    // Check if studentId exists in the database
    $checkSql = "SELECT COUNT(*) FROM attendance WHERE studentid = '$studentId'";
    $result = $conn->query($checkSql);
    $count = $result->fetch_row()[0];

    if ($count == 0) {
        echo "No student found with ID: $studentId. Please check the student ID.";
        exit;
    }

    $sql = "UPDATE attendance SET present = $present, absent = $absent, medical = $medical WHERE studentid = '$studentId'";
    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            error_log("Attendance data updated successfully for studentId = $studentId");
            echo "Attendance data updated successfully";
        } else {
            error_log("No rows updated for studentId = $studentId");
            echo "No rows updated. Check if the studentId is correct.";
        }
    } else {
        error_log("Error updating attendance data: " . $conn->error);
        echo "Error updating attendance data: " . $conn->error;
    }
} else {
    error_log("Error: POST data is not set or studentId is not in the POST data");
    echo "Error: POST data is not set or studentId is not in the POST data";
}

// Close the database connection
$conn->close();
?>
