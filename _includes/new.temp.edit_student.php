<?php
session_start();
require '../_includes/dbconnect.inc'; 


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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Student Profile</div>
                <div class="card-body">
                    <!-- Student Information Form -->
                    <form method="post" action="" enctype="multipart/form-data">
                        <!-- Profile Image -->
                        <div class="form-group text-center">
                            <label for="image">Profile Image</label><br>
                            <?php if (!empty($student['image'])) : ?>
                                <img src="<?php echo $student['image']; ?>" alt="Student Image" class="profile-image mb-3">
                            <?php else : ?>
                                <img src="placeholder.jpg" alt="Student Image" class="profile-image mb-3">
                            <?php endif; ?>
                            <input type="file" class="form-control-file" id="image" name="image">
                        </div>
                        
                        <!-- Student Personal Details -->
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $student['firstname']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $student['lastname']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $student['dob']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="house">House</label>
                            <input type="text" class="form-control" id="house" name="house" value="<?php echo $student['house']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="town">Town</label>
                            <input type="text" class="form-control" id="town" name="town" value="<?php echo $student['town']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="county">County</label>
                            <input type="text" class="form-control" id="county" name="county" value="<?php echo $student['county']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" value="<?php echo $student['country']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" class="form-control" id="postcode" name="postcode" value="<?php echo $student['postcode']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Update Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password or leave blank for no change">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>


                        <script>
        // JavaScript to handle page reload on button click
        document.getElementById('updateProfileButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission behavior
            window.location.reload(true); // Force reload from the server, not cache
        });
    </script>
                    </form>
                    
                    <!-- Back Button -->
                    <a href="students.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Back to Students</a>







    <hr>


    <h2>Edit Emergency Contact</h2>
    <?php
$fetchEmergencyContactsSql = "SELECT *, DATE_FORMAT(last_updated, '%d-%m-%Y %H:%i:%s') as formatted_last_updated FROM student_emergency WHERE studentid = ?";
$fetchEmergencyStmt = $conn->prepare($fetchEmergencyContactsSql);
$fetchEmergencyStmt->bind_param("s", $studentId);
$fetchEmergencyStmt->execute();
$emergencyResult = $fetchEmergencyStmt->get_result();
$emergencyContacts = $emergencyResult->fetch_assoc(); // Fetch only one record assuming a student has one emergency contact
?>

<div class="container mt-5">
    <form method="POST" action="" class="form">
        <input type="hidden" name="contact_id" value="<?php echo htmlspecialchars($emergencyContacts['id']); ?>">

        <div class="mb-3">
            <label for="relation" class="form-label">Relationship</label>
            <input type="text" id="relation" name="relation" class="form-control" value="<?php echo htmlspecialchars($emergencyContacts['relation']); ?>">
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($emergencyContacts['first_name']); ?>">
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($emergencyContacts['last_name']); ?>">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($emergencyContacts['phone']); ?>">
        </div>

        <div class="text-muted mb-3">
            This emergency contact was last updated on: <?php echo $emergencyContacts['formatted_last_updated']; ?>
        </div>

        <button type="submit" name="update_emergency" class="btn btn-primary">Update</button>
    </form>
</div>
<hr>



<?php
// Establish connection or start session
// Initialize your database connection here

$message = ""; // Initialize message variable

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have a connection variable named $conn
    $sql = "UPDATE attendance SET present = ?, absent = ?, medical = ? WHERE studentid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $_POST['present'], $_POST['absent'], $_POST['medical'], $studentId);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        Record updated successfully.
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>";
    } else {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        Error updating record: " . htmlspecialchars($conn->error) . "
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>";
    }
}

// Fetch existing data
if(isset($studentId)) { // Check if studentId is set
    $sql = "SELECT * FROM attendance WHERE studentid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $present = $row["present"];
        $absent = $row["absent"];
        $medical = $row["medical"];
    } else {
        echo "<p>No attendance data available.</p>";
    }
} else {
    echo "<p>Student ID not found.</p>";
}
?>









<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and prepare data
    $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
    $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
    $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
    $house = filter_input(INPUT_POST, 'house', FILTER_SANITIZE_STRING);
    $town = filter_input(INPUT_POST, 'town', FILTER_SANITIZE_STRING);
    $county = filter_input(INPUT_POST, 'county', FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $image = $student['image']; // Default to current image


    // Prepare and execute update statement
    $updateStmt = $conn->prepare("UPDATE student SET firstname=?, lastname=?, dob=?, house=?, town=?, county=?, country=?, postcode=?, image=? WHERE studentid=?");
    $updateStmt->bind_param("sssssssssi", $firstname, $lastname, $dob, $house, $town, $county, $country, $postcode, $image, $studentId);
    $updateStmt->execute();

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $passwordStmt = $conn->prepare("UPDATE student SET password=? WHERE studentid=?");
        $passwordStmt->bind_param("si", $password, $studentId);
        $passwordStmt->execute();
    }



}

?>











<!-- Display alerts -->
<?= $message ?>

<!-- Attendance update form -->
<form method="post">
    <div class="row mt-5">
        <div class="col-md-6">
            <h4>Attendance Record</h4>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Present</td>
                        <td><input type="number" class="form-control" name="present" value="<?php echo htmlspecialchars($present ?? 0); ?>"></td>
                    </tr>
                    <tr>
                        <td>Absent</td>
                        <td><input type="number" class="form-control" name="absent" value="<?php echo htmlspecialchars($absent ?? 0); ?>"></td>
                    </tr>
                    <tr>
                        <td>Medical</td>
                        <td><input type="number" class="form-control" name="medical" value="<?php echo htmlspecialchars($medical ?? 0); ?>"></td>
                    </tr>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Update Attendance</button>
        </div>
        <div class="col-md-6">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
</form>

<!-- Scripting for Chart.js, Bootstrap, and Alert Close -->
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
            maintainAspectRatio: true,
            title: {
                display: true,
                text: 'Attendance Record'
            }
        }
    });

    //

    <!-- Chart.js -->
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


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
