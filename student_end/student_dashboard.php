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



// ...

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

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
                





                    
                    <?php

echo "<h4>Student Emergency Contact</h4>";
echo "In the event of an emergency, these are the contact details we will contact if needed.";
echo "<br>"; 

if (!isset($_SESSION['studentId'])) {
    die("Student ID is not set.");
}
$studentId = $_SESSION['studentId'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM student_emergency WHERE studentid = ?");
$stmt->bind_param("s", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$emergencyDetails = $result->fetch_assoc();

if (!$emergencyDetails) {
    echo "<div class='alert alert-danger' role='alert'>
            <i class='fas fa-exclamation-triangle'></i>
            <strong> Warning!</strong> No emergency details found. Please add them urgently.
          </div>";
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $relation = $_POST['relation'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Simple phone number validation
    if (!preg_match("/^\+?[1-9]\d{1,14}$/", $phone)) {
        echo "<div class='alert alert-warning' role='alert'>Invalid phone number format.</div>";
    } else {
        if (!$emergencyDetails) {
            // Insert new emergency detail
            $stmt = $conn->prepare("INSERT INTO student_emergency (studentid, relation, first_name, last_name, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $studentId, $relation, $first_name, $last_name, $phone);
        } else {
            // Update existing emergency detail
            $stmt = $conn->prepare("UPDATE student_emergency SET relation = ?, first_name = ?, last_name = ?, phone = ? WHERE studentid = ?");
            $stmt->bind_param("sssss", $relation, $first_name, $last_name, $phone, $studentId);
        }

        if (!$stmt->execute()) {
            echo "Error updating record: " . htmlspecialchars($stmt->error);
        } else {
            // Reload the page to reflect changes
            echo "<script>window.location = window.location.href;</script>";
        }
        $stmt->close();
    }
}

// Form for updating or adding details
echo "<form method='POST' action='' class='mb-3'>
    <div class='mb-3'>
        <label for='relation' class='form-label'>Relation:</label>
        <input type='text' id='relation' name='relation' value='" . ($emergencyDetails['relation'] ?? '') . "' class='form-control'>
    </div>
    <div class='row mb-3'>
        <div class='col'>
            <label for='first_name' class='form-label'>First Name:</label>
            <input type='text' id='first_name' name='first_name' value='" . ($emergencyDetails['first_name'] ?? '') . "' class='form-control'>
        </div>
        <div class='col'>
            <label for='last_name' class='form-label'>Last Name:</label>
            <input type='text' id='last_name' name='last_name' value='" . ($emergencyDetails['last_name'] ?? '') . "' class='form-control'>
        </div>
    </div>
    <div class='mb-3'>
        <label for='phone' class='form-label'>Phone:</label>
        <input type='tel' id='phone' name='phone' value='" . ($emergencyDetails['phone'] ?? '') . "' class='form-control'>
    </div>
    <button type='submit' class='btn btn-primary'>Update</button>
</form>";

?>

<script>
    // Initialize international telephone input with flags
    var input = document.querySelector("#phone");
    window.intlTelInput(input, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });
</script>


<hr>



<!-- Attendance -->
<?php
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

<style>
    #attendanceChart {
        aspect-ratio: 1; /* Ensures the chart is round */
        max-width: 300px; /* Adjust width as needed */
        margin: auto; /* Centers the chart */
    }
</style>

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
                    <td><?php echo $present . '%'; ?></td>
                </tr>
                <tr>
                    <td>Absent</td>
                    <td><?php echo $absent . '%'; ?></td>
                </tr>
                <tr>
                    <td>Medical</td>
                    <td><?php echo $medical . '%'; ?></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary">
            Your Attendance Is: <span class="badge text-bg-secondary"><?php echo round(($present / ($present + $absent + $medical)) * 100) . '%'; ?></span>
        </button>
    </div>
    <div class="col-md-6">
        <canvas id="attendanceChart"></canvas>
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
            maintainAspectRatio: true, // This maintains the aspect ratio
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


<?php


// Attempt to query attendance data
if(isset($studentId)) {
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
        $attendanceMessage = "Your Attendance: Present $present days, Absent $absent days, Medical $medical days.";
    } else {
        $attendanceMessage = "No attendance data available.";
    }
} else {
    $attendanceMessage = "Student ID not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div aria-live="polite" aria-atomic="true" class="position-fixed" style="top: 0; right: 0; z-index: 1050;">
    <div class="toast-container p-3">
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Attendance Update</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?php echo $attendanceMessage; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap and jQuery scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('.toast').toast('show');
});
</script>

</body>
</html>















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scrollable Auto Close Sticky Popup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for the sticky top-right popup */
        .modal-dialog-top-right {
            position: fixed;
            top: 10px;
            right: 10px;
            margin: 0;
            width: 300px;
        }
        .progress {
            height: 20px;
        }
        .progress-bar {
            background-color: green;
        }
        /* Override Bootstrap modal open style to allow page scrolling */
        .modal-open {
            overflow: visible;
        }
    </style>
</head>
<body>
<?php
    // Initialize the attendance variables
    $present = $absent = $medical = 0;
    if(isset($studentId)) {
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
        }
    }
    $totalAbsences = $absent + $medical;
    $attendancePercentage = round(($present / ($present + $totalAbsences)) * 100);
?>

<!-- The Modal -->
<div class="modal" id="autoClosePopup" data-bs-backdrop="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top-right">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Your Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo "You have <strong>$totalAbsences</strong> days of absences (including authorised medical days)."; ?>
                <!-- Progress bar -->
                <div class="progress mt-3">
                    <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var myModal = new bootstrap.Modal(document.getElementById('autoClosePopup'), {
            keyboard: false
        });
        myModal.show();

        // Progress bar animation
        var progressBar = document.querySelector('.progress-bar');
        var time = 5000; // 5 seconds
        var interval = 50;
        var step = 100 / (time / interval); // percentage increase per step

        var width = 100;
        var progressInterval = setInterval(function () {
            width -= step;
            if (width < 0) {
                clearInterval(progressInterval);
                myModal.hide();
            } else {
                progressBar.style.width = width + '%';
            }
        }, interval);

        // Option to manually close
        document.querySelector('.btn-primary').addEventListener('click', function () {
            clearInterval(progressInterval);
            myModal.hide();
        });
    });
</script>
</body>
</html>
