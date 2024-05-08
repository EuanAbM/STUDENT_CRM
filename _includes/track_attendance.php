<?php
include 'dbconnect.inc';

$studentId = $_POST['studentid'];
$absent = $_POST['absent'];
$present = $_POST['present'];
$medical = $_POST['medical'];

// Prepare the SQL statement to insert or update the attendance record
$stmt = $conn->prepare("INSERT INTO attendance (studentid, absent, present, medical) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE absent=?, present=?, medical=?");
$stmt->bind_param("iiiiiii", $studentId, $absent, $present, $medical, $absent, $present, $medical);

if ($stmt->execute()) {
    echo "Attendance tracked successfully.";
} else {
    echo "Error tracking attendance: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
