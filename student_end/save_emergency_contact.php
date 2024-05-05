<?php
// Include database connection
require '../_includes/dbconnect.inc';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    if(isset($_POST['emergencyContacts']) && isset($_POST['studentId'])) {
        $emergencyContacts = $_POST['emergencyContacts'];
        $studentId = $conn->real_escape_string($_POST['studentId']); // Sanitize the studentId

        // Attempt to insert or update the record
        foreach($emergencyContacts as $contact) {
            $sql = "INSERT INTO emergency_contacts (studentid, contact) VALUES ('$studentId', '$contact')
                    ON DUPLICATE KEY UPDATE contact = VALUES(contact);";

            if ($conn->query($sql) !== TRUE) {
                throw new Exception("Error updating emergency contact data: " . $conn->error);
            }
        }
        echo "Emergency contact data inserted or updated successfully.";
    } else {
        throw new Exception("Error: POST data is not set or studentId is not in the POST data");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo $e->getMessage();
}

// Close the database connection
$conn->close();
?>