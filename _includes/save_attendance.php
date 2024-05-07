<?php
require '../_includes/dbconnect.inc';

if(isset($_POST['present']) && isset($_POST['absent']) && isset($_POST['medical']) && isset($_POST['studentId'])) {
    $present = (int)$_POST['present'];
    $absent = (int)$_POST['absent'];
    $medical = (int)$_POST['medical'];
    $studentId = $conn->real_escape_string($_POST['studentId']); // Sanitize the studentId

    // Attempt to insert or update the record
    $sql = "INSERT INTO attendance (studentid, present, absent, medical) VALUES ('$studentId', $present, $absent, $medical)
            ON DUPLICATE KEY UPDATE present = VALUES(present), absent = VALUES(absent), medical = VALUES(medical);";

    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            error_log("Attendance data inserted or updated successfully for studentId = $studentId");
            echo "Attendance data inserted or updated successfully.";
        } else {
            error_log("No rows inserted or updated for studentId = $studentId");
            echo "No rows inserted or updated. This might be due to no changes in the data.";
        }
    } else {
        error_log("Error in inserting/updating attendance data: " . $conn->error);
        echo "Error in inserting/updating attendance data: " . $conn->error;
    }
} else {
    error_log("Error: POST data is not set or studentId is not in the POST data");
    echo "Error: POST data is not set or studentId is not in the POST data";
}

if(isset($_POST['studentId'])) {
    $studentId = $conn->real_escape_string($_POST['studentId']); // Sanitize the studentId

    // Check if the student already has an attendance record
    $checkSql = "SELECT * FROM attendance WHERE studentid = '$studentId'";
    $checkResult = $conn->query($checkSql);
    if ($checkResult->num_rows == 0) {
        // If no attendance record exists, insert a new record with default values
        $insertSql = "INSERT INTO attendance (studentid, present, absent, medical) VALUES ('$studentId', 0, 0, 0)";
        if ($conn->query($insertSql) === TRUE) {
            echo "New attendance record created successfully for studentId = $studentId";
        } else {
            echo "Error creating new attendance record: " . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
