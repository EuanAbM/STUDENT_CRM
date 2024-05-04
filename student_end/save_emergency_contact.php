<?php
session_start();

var_dump($_POST);

// Include database connection
require 'dbconnect.inc';
$studentId = $_SESSION['studentId'];


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Verify if student exists with the provided studentId
$checkStudentSql = "SELECT * FROM student WHERE studentid = '$studentId'";
$studentResult = mysqli_query($conn, $checkStudentSql);

if (mysqli_num_rows($studentResult) == 0) {
    die("Error: Student with ID '$studentId' not found.");
}

// Verify if emergency_contacts array exists and is not empty
if (isset($_POST['emergency_contacts']) && is_array($_POST['emergency_contacts'])) {
    $emergencyContacts = $_POST['emergency_contacts'];

    // Iterate through emergency contacts and update in database
    foreach ($emergencyContacts as $index => $contact) {
        $contactId = mysqli_real_escape_string($conn, $contact['id']);
        $contactType = mysqli_real_escape_string($conn, $contact['type']);
        $firstName = mysqli_real_escape_string($conn, $contact['first_name']);
        $lastName = mysqli_real_escape_string($conn, $contact['last_name']);
        $phoneNumber = mysqli_real_escape_string($conn, $contact['phone']);

        // Update the emergency contact
        $updateContactSql = "UPDATE emergency_contacts SET type='$contactType', first_name='$firstName', last_name='$lastName', phone_number='$phoneNumber' WHERE id='$contactId' AND studentid='$studentId'";
        $result = mysqli_query($conn, $updateContactSql);

        if (!$result) {
            die("Error updating emergency contact: " + mysqli_error($conn));
        }
    }
    echo "No emergency contacts data received.";
}


$studentId = $_POST['studentId'];


// Redirect back to student_dashboard.php after processing
header("Location: student_dashboard.php");
exit();
?>
