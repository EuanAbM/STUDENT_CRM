<?php
require 'dbconnect.inc';

$studentId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute select statement
$stmt = $conn->prepare("SELECT * FROM student WHERE studentid = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Fetch emergency contacts for the student
$emergencyContacts = [];
$emergencyStmt = $conn->prepare("SELECT * FROM emergency_details WHERE studentid = ?");
$emergencyStmt->bind_param("i", $studentId);
$emergencyStmt->execute();
$emergencyResult = $emergencyStmt->get_result();
while ($contact = $emergencyResult->fetch_assoc()) {
    $emergencyContacts[] = $contact;
}

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

    // Handle image upload
    if ($_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Prepare and execute update statement
    
// Prepare and execute update statement
$updateStmt = $conn->prepare("UPDATE student SET firstname=?, lastname=?, dob=?, house=?, town=?, county=?, country=?, postcode=?, image=?, email=?, phone=? WHERE studentid=?");
$updateStmt->bind_param("sssssssssssi", $firstname, $lastname, $dob, $house, $town, $county, $country, $postcode, $image, $email, $phone, $studentId);
$updateStmt->execute();

// ...

$sql = "UPDATE students SET ";
$types = '';
$values = [];

if (!empty($_POST['lastname'])) {
    $sql .= "lastname = ?, ";
    $types .= 's';
    $values[] = $_POST['lastname'];
}
if (!empty($_POST['email'])) {
    $sql .= "email = ?, ";
    $types .= 's';
    $values[] = $_POST['email'];
}
// ... repeat for other fields ...
$sql = rtrim($sql, ', ');  // remove trailing comma
$sql .= " WHERE id = ?";

$types .= 'i';
$values[] = $_POST['id'];  // Assuming the form also includes a hidden field with the student ID

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$values);
$stmt->execute();  // Execute the statement

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $passwordStmt = $conn->prepare("UPDATE student SET password=? WHERE studentid=?");
        $passwordStmt->bind_param("si", $password, $studentId);
        $passwordStmt->execute();
    }





}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile - <?php echo $studentId; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .profile-image {
            width: 100%; /* Fill the width */
            height: 100px; /* Fixed height */
            object-fit: cover; /* Cover to maintain aspect ratio */
            border-radius: 0; /* Square corners */
        }
        .header-area, .btn-primary {
            background-color: #007bff; /* Bootstrap primary blue */
            color: white;
        }
        .content-area {
            background-color: #f8f9fa; /* Bootstrap light background */
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .back-to-students {
            text-align: right;
            padding: 10px;
        }
        .student-image {
            object-fit: contain; /* This will make sure the image is scaled while maintaining its aspect ratio */
            width: 100%; /* This will make the image take the full width of its container */
            height: auto; /* This will make the image height adjust automatically based on its width */
        }
        .emergency-tile {
            margin-top: 20px;
            padding: 10px;
            background-color: #efefef;
            border: 1px solid #ddd;
            text-align: center;
        }
        .emergency-tile.empty {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="back-to-students">
        <a href="students.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Students</a>
    </div>
    <div class="header-area p-2">
        <h2>Edit Student Profile - <?php echo $studentId; ?></h2>
    </div>

    <div class="row">
        <!-- Student Details -->
        <div class="col-md-4 content-area">
            <div class="text-center">
                <!-- Profile Image -->
                <?php if (!empty($student['image'])) : ?>
                    <img src="<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image" class="student-image  mb-3">
                <?php else : ?>
                    <img src="placeholder.jpg" alt="Student Image" class="profile-image mb-3">
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-8 content-area">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Student ID + Course Name</label>
                    <p>Placeholder text</p>
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($student['firstname']); ?>">
                    <div class="form-group">
    <label for="lastname">Last Name</label>
    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($student['lastname']); ?>">
</div>
<div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">
</div>
            
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
                    <label for="password">Update Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password or leave blank for no change">
                    <label for="image">Upload Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Address Update Section -->
        <div class="col-md-12 header-area">
            <h4>Address Section + Update</h4>
        </div>
        <div class="col-md-12 content-area">
            <form method="post" action="">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="house">House</label>
                        <input type="text" class="form-control" id="house" name="house" value="<?php echo htmlspecialchars($student['house']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="town">Town</label>
                        <input type="text" class="form-control" id="town" name="town" value="<?php echo htmlspecialchars($student['town']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="county">County</label>
                        <input type="text" class="form-control" id="county" name="county" value="<?php echo htmlspecialchars($student['county']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($student['country']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="postcode">Postcode</label>
                        <input type="text" class="form-control" id="postcode" name="postcode" value="<?php echo htmlspecialchars($student['postcode']); ?>">
                    </div>
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-primary">Update Address</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Emergency Contacts Section -->
    <div class="row">
        <div class="col-md-12 header-area">
            <h4>Emergency Contacts</h4>
        </div>
        <?php
        $maxContacts = 3;
        $count = count($emergencyContacts);
        for ($i = 0; $i < $maxContacts; $i++) :
            if (isset($emergencyContacts[$i])) :
                $contact = $emergencyContacts[$i];
                ?>
                <div class="col-md-4 content-area">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($contact['relation']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($contact['firstname'] . ' ' . $contact['lastname']); ?></p>
                            <p class="card-text"><?php echo htmlspecialchars($contact['phone']); ?></p>
                            <a href="edit_emergency_contact.php?contactid=<?php echo $contact['id']; ?>" class="btn btn-primary">Edit Contact</a>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="col-md-4 content-area">
                    <div class="emergency-tile empty">
                        <p>No contact here. <a href="add_emergency_contact.php?studentid=<?php echo $studentId; ?>">Add one?</a></p>
                    </div>
                </div>
            <?php endif;
        endfor;
        ?>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>



<!-- Attendance Record Section -->


<div class="row mt-5">
    <div class="col-md-6">
        <h4>Attendance Record</h4>
        <form>
            <div class="form-group">
                <label for="present">Present</label>
                <input type="text" class="form-control" id="present" name="present">
            </div>
            <div class="form-group">
                <label for="absent">Absent</label>
                <input type="text" class="form-control" id="absent" name="absent">
            </div>
            <div class="form-group">
                <label for="medical">Medical</label>
                <input type="text" class="form-control" id="medical" name="medical">
            </div>
            <button type="button" class="btn btn-primary" id="saveAttendance">Save Attendance</button>
            <p id="attendancePercentage">The Student's attendance is currently 0%</p>
        </form>
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
                data: [0, 0, 0], // Initialize with 0, you'll update these values dynamically
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

    // Add event listeners to the input fields
    document.getElementById('present').addEventListener('input', updateChart);
    document.getElementById('absent').addEventListener('input', updateChart);
    document.getElementById('medical').addEventListener('input', updateChart);

    function updateChart() {
        // Update the chart's data
        var present = document.getElementById('present').value || 0;
        var absent = document.getElementById('absent').value || 0;
        var medical = document.getElementById('medical').value || 0;
        chart.data.datasets[0].data = [present, absent, medical];

        // Redraw the chart
        chart.update();

        // Update the attendance percentage
        var total = parseInt(present) + parseInt(absent) + parseInt(medical);
        var percentage = total > 0 ? Math.round((present / total) * 100) : 0;
        document.getElementById('attendancePercentage').textContent = "The Student's attendance is currently " + percentage + "%";
    }
</script>
</body>
</html>



</html>
