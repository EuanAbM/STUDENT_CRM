<?php
require 'dbconnect.inc';
$studentId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Query to fetch existing attendance data if present
$attendanceQuery = $conn->prepare("SELECT present, absent, medical FROM attendance WHERE studentid = ?");
$attendanceQuery->bind_param("s", $studentId);
$attendanceQuery->execute();
$attendanceResult = $attendanceQuery->get_result();
$attendanceData = $attendanceResult->fetch_assoc();

// Default values if no data is found
$present = $attendanceData['present'] ?? 0;
$absent = $attendanceData['absent'] ?? 0;
$medical = $attendanceData['medical'] ?? 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['studentId']) || empty($_POST['studentId'])) {
        die('Student ID not provided');
    }
    $studentId = $_POST['studentId'];
    $present = $_POST['present'];
    $absent = $_POST['absent'];
    $medical = $_POST['medical'];

    // Insert or update the attendance data
    $updateQuery = $conn->prepare("INSERT INTO attendance (studentid, present, absent, medical) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE present = VALUES(present), absent = VALUES(absent), medical = VALUES(medical)");
    $updateQuery->bind_param("siii", $studentId, $present, $absent, $medical);
    $updateQuery->execute();

    if ($updateQuery->affected_rows > 0) {
        echo "Attendance data updated successfully.";
    } else {
        echo "No changes were made to the attendance data.";
    }
}

$conn->close();
?>

<form method="post" action="">
    <input type="hidden" name="studentId" value="<?php echo $studentId; ?>">
    <label for="present">Present:</label>
    <input type="number" id="present" name="present" value="<?php echo $present; ?>">
    <label for="absent">Absent:</label>
    <input type="number" id="absent" name="absent" value="<?php echo $absent; ?>">
    <label for="medical">Medical:</label>
    <input type="number" id="medical" name="medical" value="<?php echo $medical; ?>">
    <button type="submit">Save Attendance</button>
</form>
