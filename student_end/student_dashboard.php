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
                    


                <div class="student-photo">
    <!-- Student Image -->
    <?php 
    if (isset($student['image_path'])): 
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/_includes/uploads/' . $student['image_path'];
        if (file_exists($image_path)): ?>
            <img src="<?php echo '/_includes/uploads/' . $student['image_path']; ?>" alt="Student Image">
        <?php else: ?>
            <p>Image not found</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Image not found</p>
    <?php endif; ?>
</div>



                    <h4>Welcome, <?php echo $student['firstname']; ?>!</h4>
                    <a href="logout.php" class="btn btn-sm btn-danger float-right">Logout</a>
                </div>
                <div class="card-body">
                    <!-- Student Information -->
                    <h5>Student Information</h5>
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
                    <h5>Recent Grades</h5>
                    <p>No grades available.</p>

                    <hr>

                    <h5>Upcoming Assignments</h5>
                    <p>No upcoming assignments.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery (place before </body>) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
