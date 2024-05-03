<?php
require 'dbconnect.inc';

$studentId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$conn->set_charset("utf8mb4");

try {
    $stmt = $conn->prepare("SELECT * FROM student WHERE studentid = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    // Fetch emergency contacts
    $emergencyContacts = [];
    $emergencyStmt = $conn->prepare("SELECT * FROM emergency_details WHERE studentid = ?");
    $emergencyStmt->bind_param("i", $studentId);
    $emergencyStmt->execute();
    $emergencyResult = $emergencyStmt->get_result();
    while ($contact = $emergencyResult->fetch_assoc()) {
        $emergencyContacts[] = $contact;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING) ?: $student['firstname'];
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING) ?: $student['lastname'];
        $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING) ?: $student['dob'];
        $house = filter_input(INPUT_POST, 'house', FILTER_SANITIZE_STRING) ?: $student['house'];
        $town = filter_input(INPUT_POST, 'town', FILTER_SANITIZE_STRING) ?: $student['town'];
        $county = filter_input(INPUT_POST, 'county', FILTER_SANITIZE_STRING) ?: $student['county'];
        $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING) ?: $student['country'];
        $postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_STRING) ?: $student['postcode'];
        $image = $student['image']; // Default to current image

        // Handle image upload
        if ($_FILES['image']['error'] == 0) {
            $tempImage = 'uploads/' . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $tempImage)) {
                $image = $tempImage;
            }
        }

        $updateStmt = $conn->prepare("UPDATE student SET firstname=?, lastname=?, dob=?, house=?, town=?, county=?, country=?, postcode=?, image=? WHERE studentid=?");
        $updateStmt->bind_param("sssssssssi", $firstname, $lastname, $dob, $house, $town, $county, $country, $postcode, $image, $studentId);
        $updateStmt->execute();

        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $passwordStmt = $conn->prepare("UPDATE student SET password=? WHERE studentid=?");
            $passwordStmt->bind_param("si", $passwordHash, $studentId);
            $passwordStmt->execute();
        }

        header('Location: students.php');
        exit;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile - <?php echo $studentId; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .profile-image {
            width: 100%; 
            height: 100px; 
            object-fit: cover; 
            border-radius: 0;
        }
        .header-area, .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .content-area {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .back-to-students {
            text-align: right;
            padding: 10px;
        }
        .student-image {
            object-fit: contain;
            width: 100%;
            height: auto;
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
        <div class="col-md-4 content-area">
            <div class="text-center">
                <?php if (!empty($student['image'])) : ?>
                    <img src="<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image" class="student-image mb-3">
                <?php else : ?>
                    <img src="placeholder.jpg" alt="Student Image" class="profile-image mb-3">
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-8 content-area">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($student['firstname']); ?>">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($student['lastname']); ?>">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
                    <label for="password">Update Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password or leave blank for no change">
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <div class="row">
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
                        <button type="submit" the "btn btn-primary">Update Address</button>
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
        <?php foreach ($emergencyContacts as $contact) : ?>
            <div class="col-md-4 content-area">
                <form method="post" action="save_emergency_contact.php">
                    <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="relation">Relation</label>
                                <input type="text" class="form-control" name="relation" value="<?php echo htmlspecialchars($contact['relation']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="firstname">First Name</label>
                                <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($contact['firstname']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="lastname">Last Name</label>
                                <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($contact['lastname']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($contact['phone']); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Contact</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
                </div>
                <button type="button" class="btn btn-primary" id="saveAttendance">Save Attendance</button>
                <p id="attendancePercentage">The Student's attendance is currently 0%</p>
            </form>
        </div>
        <div class="col-md-6">
            <canvas id="attendanceChart" style="max-width: 300px;"></canvas>
        </div>
    </div>

    <!-- JavaScript for Chart and Attendance -->
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
</div>
</body>
</html>
