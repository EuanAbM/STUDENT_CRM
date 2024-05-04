<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['studentId'])) {
    header("Location: student_login.php");
    exit();
}

// Database connection
require '../_includes/dbconnect.inc';
$studentId = $_SESSION['studentId'];

// Fetch student information
$sql = "SELECT * FROM student WHERE studentid = '$studentId'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);





// Fetch student information
$sql = "SELECT * FROM student WHERE studentid = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$image_name = $student['image'];

// Now you have the image name, you can create the path to the image
$image_path = "/PHP_STUDENT_CRM/STUDENT_CRM/_includes/" . $image_name;



// Fetch emergency details for the student
$emergencyDetails = [];
$getDetailsSql = "SELECT * FROM emergency_details WHERE studentid = ? ORDER BY contact_order ASC";
$detailsStmt = $conn->prepare($getDetailsSql);

if ($detailsStmt === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

$detailsStmt->bind_param("i", $studentId);
$detailsStmt->execute();
$detailsResult = $detailsStmt->get_result();

while ($detail = $detailsResult->fetch_assoc()) {
    $emergencyDetails[] = $detail;
}








// Fetch emergency details for the student
$emergencyDetails = [];
$getDetailsSql = "SELECT * FROM emergency_details WHERE studentid = '$studentId' ORDER BY contact_order ASC";
$detailsResult = mysqli_query($conn, $getDetailsSql);
while ($detail = mysqli_fetch_assoc($detailsResult)) {
    $emergencyDetails[] = $detail;
}

// Update emergency details if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emergency'])) {
    // Clear existing emergency details
    $deleteSql = "DELETE FROM emergency_details WHERE studentid = '$studentId'";
    mysqli_query($conn, $deleteSql);

    // Insert new emergency details
    for ($i = 0; $i < 3; $i++) {
        if (!empty($_POST['relation'][$i]) && !empty($_POST['firstname'][$i]) && !empty($_POST['lastname'][$i])) {
            $relation = mysqli_real_escape_string($conn, $_POST['relation'][$i]);
            $firstname = mysqli_real_escape_string($conn, $_POST['firstname'][$i]);
            $lastname = mysqli_real_escape_string($conn, $_POST['lastname'][$i]);
            $insertSql = "INSERT INTO emergency_details (studentid, relation, firstname, lastname, contact_order) VALUES ('$studentId', '$relation', '$firstname', '$lastname', '$i')";
            mysqli_query($conn, $insertSql);
        }
    }

    // Refresh emergency details
    $emergencyDetails = [];
    $detailsResult = mysqli_query($conn, $getDetailsSql);
    while ($detail = mysqli_fetch_assoc($detailsResult)) {
        $emergencyDetails[] = $detail;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Add custom styles here */
        .emergency-contact {
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 15px;
        }
        .emergency-contact .edit-btn {
            margin-top: 10px;
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
                    

                



                    <h4>Welcome, <?php echo $student['firstname']; ?>!</h4>
                    <a href="logout.php" class="btn btn-sm btn-danger float-right">Logout</a>
                </div>
                <div class="card-body">
                    <!-- Student Information -->
                    <h5>Student Information</h5>
                    <?php
echo "<img src='{$image_path}' alt='Student Images' style='border-radius: 50%; float: right; width: 150px; height: 150px;'>";
?>
                    <p><strong>Student ID:</strong> <?php echo $student['studentid']; ?></p>
                    <p><strong>Name:</strong> <?php echo $student['firstname'] . ' ' . $student['lastname']; ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo $student['dob']; ?></p>
                    <p><strong>Address:</strong> <?php echo $student['house'] . ', ' . $student['town'] . ', ' . $student['county'] . ', ' . $student['postcode'] . ', ' . $student['country']; ?></p>
                    
                    <hr>

                    <!-- Update Password Form -->
                    <h5>Update Password</h5>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>

                    <hr>

                    <!-- Emergency Contacts -->
                    <h5>Emergency Contacts</h5>
                    <div class="row">
                        <?php foreach ($emergencyDetails as $index => $detail): ?>
                            <div class="col-md-4">
                                <div class="emergency-contact">
                                    <h6>Emergency Contact <?php echo $index + 1; ?></h6>
                                    <p><strong>Relation:</strong> <?php echo ucfirst($detail['relation']); ?></p>
                                    <p><strong>Name:</strong> <?php echo $detail['firstname'] . ' ' . $detail['lastname']; ?></p>
                                    <button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModal<?php echo $index; ?>">Edit</button>
                                </div>

                                <!-- Edit Emergency Contact Modal -->
                                <div class="modal fade" id="editModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $index; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel<?php echo $index; ?>">Edit Emergency Contact</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="">
                                                    <div class="form-group">
                                                        <label for="relation">Relation</label>
                                                        <input type="text" class="form-control" id="relation" name="relation[<?php echo $index; ?>]" value="<?php echo $detail['relation']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="firstname">First Name</label>
                                                        <input type="text" class="form-control" id="firstname" name="firstname[<?php echo $index; ?>]" value="<?php echo $detail['firstname']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="lastname">Last Name</label>
                                                        <input type="text" class="form-control" id="lastname" name="lastname[<?php echo $index; ?>]" value="<?php echo $detail['lastname']; ?>">
                                                    </div>
                                                    <!-- Add phone number input if needed -->
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <!-- Recent Grades and Assignments Display -->
                    
               



                    <h5>Attendance</h5>
<?php
    // Assuming you have a connection to your database in a variable called $conn
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

<div class="row mt-5">
    <div class="col-md-6">
    <h4>Attendance Record</h4>
<div class="form-group">
    <label for="present">Present</label>
    <p id="present"><?php echo $present; ?></p>
</div>
<div class="form-group">
    <label for="absent">Absent</label>
    <p id="absent"><?php echo $absent; ?></p>
</div>
<div class="form-group">
    <label for="medical">Medical</label>
    <p id="medical"><?php echo $medical; ?></p>
</div>
<p id="attendancePercentage">Your Attendance is <?php echo round(($present / ($present + $absent + $medical)) * 100); ?>%</p>
</div>
<div class="col-md-6">
    <canvas id="attendanceChart" style="max-width: 300px;"></canvas>
</div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
            title: {
                display: true,
                text: 'Attendance Record'
            }
        }
    });
</script>









                    <hr>

                    <h5>Upcoming Assignments</h5>
                    <p>No upcoming assignments.</p>
                </div>
            </div>
        </div>
    </div>
</div>


