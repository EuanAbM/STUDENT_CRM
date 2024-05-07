<?php
require '../_includes/dbconnect.inc';

if(isset($_POST['studentId'])) {
    $studentId = $conn->real_escape_string($_POST['studentId']);
    $relation = $conn->real_escape_string($_POST['relation']);
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $phone = $conn->real_escape_string($_POST['phone']);

    $sql = "UPDATE student_emergency SET relation = ?, first_name = ?, last_name = ?, phone = ? WHERE studentid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $relation, $firstName, $lastName, $phone, $studentId);

    if ($stmt->execute()) {
        echo "Emergency contact updated successfully.";
    } else {
        echo "Error updating emergency contact: " . $conn->error;
    }
} else {
    echo "No studentId provided.";
}

$conn->close();
?>