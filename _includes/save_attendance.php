<?php
require 'dbconnect.inc';

if(isset($_POST['present']) && isset($_POST['absent']) && isset($_POST['medical']) && isset($_POST['studentId'])) {
    $present = $_POST['present'];
    $absent = $_POST['absent'];
    $medical = $_POST['medical'];
    $studentId = $_POST['studentId']; // Get the student ID from the POST data

    error_log("Received data: present = $present, absent = $absent, medical = $medical, studentId = $studentId");

    $sql = "UPDATE attendance SET present = '$present', absent = '$absent', medical = '$medical' WHERE studentid = '$studentId'";
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