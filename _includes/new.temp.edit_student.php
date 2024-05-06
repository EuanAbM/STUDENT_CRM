<?php
session_start();
require '../_includes/dbconnect.inc'; // Assuming the same database connection logic


// Get student ID from the URL
$studentId = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch student information
$student = [];
if ($studentId) {
    $stmt = $conn->prepare("SELECT * FROM student WHERE studentid = ?");
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        die('Student not found');
    }
} else {
    die('No student ID provided');
}

// Handle POST request to update student information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    // Collect all data from the form
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dob'];
    $house = $_POST['house'];
    $town = $_POST['town'];
    $county = $_POST['county'];
    $postcode = $_POST['postcode'];
    $country = $_POST['country'];

    // Prepare SQL statement to update student data
    $updateSql = "UPDATE student SET firstname = ?, lastname = ?, dob = ?, house = ?, town = ?, county = ?, postcode = ?, country = ? WHERE studentid = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssssssss", $firstname, $lastname, $dob, $house, $town, $county, $postcode, $country, $studentId);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        echo "<script>alert('Student information updated successfully.'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('No changes made to student information or update failed.');</script>";
    }
}

// Fetch emergency contact information







// Fetch and display attendance record information
$attendanceDetails = [];
if ($studentId) {
    $fetchAttendanceSql = "SELECT * FROM attendance WHERE studentid = ?";
    $fetchAttendanceStmt = $conn->prepare($fetchAttendanceSql);
    $fetchAttendanceStmt->bind_param("s", $studentId);
    $fetchAttendanceStmt->execute();
    $attendanceResult = $fetchAttendanceStmt->get_result();
    $attendanceDetails = $attendanceResult->fetch_assoc();
}

// Handle POST request to update attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_attendance'])) {
    $present = $_POST['present'];
    $absent = $_POST['absent'];
    $medical = $_POST['medical'];

    $updateAttendanceSql = "UPDATE attendance SET present = ?, absent = ?, medical = ? WHERE studentid = ?";
    $updateAttendanceStmt = $conn->prepare($updateAttendanceSql);
    $updateAttendanceStmt->bind_param("iiis", $present, $absent, $medical, $studentId);
    $updateAttendanceStmt->execute();

    if ($updateAttendanceStmt->affected_rows > 0) {
        echo "<script>alert('Attendance updated successfully.');</script>";
    } else {
        echo "<script>alert('Update failed or no changes made.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Information</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <style>
        /* Add custom styles here similar to student_dashboard.php */
        .emergency-contact {
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 15px;
        }
        .student-photo {
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Student Information</h4>
                </div>
                <div class="card-body">
                    <!-- Form for updating student details -->
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="firstname">First Name:</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($student['firstname']); ?>" required>
                        </div>
                        <!-- Further form elements -->
                        <button type="submit" name="update_student" class="btn btn-primary">Update Information</button>
                    </form>
                    <hr>
                    <!-- Emergency Contacts -->
                    <h4>Student Emergency Contact</h4>
                    <p>In the event of an emergency, these are the contact details we will contact if needed.</p>
                    <!-- Insert form for emergency contact details here -->
                    <hr>
                    <!-- Attendance Chart -->
                    <h4>Attendance Record</h4>
                    <canvas id="attendanceChart"></canvas>
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        var ctx = document.getElementById('attendanceChart').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ['Present', 'Absent', 'Medical'],
                                datasets: [{
                                    data: [<?php echo $present; ?>, <?php echo $absent; ?>, <?php echo $medical; ?>],
                                    backgroundColor: ['green', 'red', 'blue']
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true, // This maintains the aspect ratio
                                title: {
                                    display: true,
                                    text: 'Attendance Record'
                                }
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

