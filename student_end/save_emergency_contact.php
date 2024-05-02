<?php
session_start();

// Include database connection
require '../_includes/dbconnect.inc';
$studentId = $_SESSION['studentId'];

// Verify if student exists with the provided studentId
$checkStudentSql = "SELECT * FROM student WHERE studentid = '$studentId'";
$studentResult = mysqli_query($conn, $checkStudentSql);

if (mysqli_num_rows($studentResult) == 0) {
    die("Error: Student with ID '$studentId' not found.");
}

// Verify if emergency_contacts array exists and is not empty
if (isset($_POST['emergency_contacts']) && is_array($_POST['emergency_contacts'])) {
    $emergencyContacts = $_POST['emergency_contacts'];

    // Iterate through emergency contacts and insert into database
    foreach ($emergencyContacts as $contact) {
        $contactType = mysqli_real_escape_string($conn, $contact['type']);
        $firstName = mysqli_real_escape_string($conn, $contact['first_name']);
        $lastName = mysqli_real_escape_string($conn, $contact['last_name']);
        $phoneNumber = mysqli_real_escape_string($conn, $contact['phone']);

        // Construct and execute SQL query to insert emergency contact
        $insertContactSql = "INSERT INTO emergency_contacts (studentid, type, first_name, last_name, phone_number)
                             VALUES ('$studentId', '$contactType', '$firstName', '$lastName', '$phoneNumber')";

        $result = mysqli_query($conn, $insertContactSql);

        if (!$result) {
            die("Error inserting emergency contact: " . mysqli_error($conn));
        }
    }
} else {
    echo "No emergency contacts data received.";
}


$studentId = $_POST['studentId'];


// Redirect back to student_dashboard.php after processing
header("Location: student_dashboard.php");
exit();
?>
