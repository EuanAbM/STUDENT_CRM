<?php
require 'dbconnect.inc';

$studentId = $_GET['id'];

// Initialize variables for form submission
$firstname = $lastname = $dob = $house = $town = $county = $country = $postcode = $password = '';
$image = '';

// Fetch student data
$sql = "SELECT * FROM student WHERE studentid = $studentId";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

// Fetch emergency contacts for the student
$emergencyContacts = [];
$emergencySql = "SELECT * FROM emergency_details WHERE studentid = $studentId";
$emergencyResult = mysqli_query($conn, $emergencySql);
while ($contact = mysqli_fetch_assoc($emergencyResult)) {
    $emergencyContacts[] = $contact;
}

// Check if form is submitted for updating student
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $house = mysqli_real_escape_string($conn, $_POST['house']);
    $town = mysqli_real_escape_string($conn, $_POST['town']);
    $county = mysqli_real_escape_string($conn, $_POST['county']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $postcode = mysqli_real_escape_string($conn, $_POST['postcode']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Handle image upload
    if ($_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    } else {
        $image = $student['image'];
    }

    // Update student data
    $sql = "UPDATE student SET firstname = '$firstname', lastname = '$lastname', dob = '$dob', house = '$house', town = '$town', county = '$county', country = '$country', postcode = '$postcode', image = '$image' WHERE studentid = $studentId";
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE student SET password = '$password' WHERE studentid = $studentId";
    }
    mysqli_query($conn, $sql);

    // Redirect back to students page
    header('Location: students.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Back to Students Button -->
        <div class="d-flex justify-content-end mt-2 mb-3">
            <a href="students.php" class="btn btn-info">Back to Students</a>
        </div>

        <!-- Student ID and Course Name -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">Edit (Student ID) Profile</div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Student Photo -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="image" class="form-label">Student Photo</label>
                                <input type="file" class="form-control" id="image" name="image" onchange="previewFile()">
                                <img id="preview" src="<?php echo $student['image']; ?>" alt="Student Photo" class="img-thumbnail mt-2">
                            </div>
                        </div>
                        <!-- Student Details -->
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $student['firstname']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $student['lastname']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $student['email']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $student['phone']; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Other sections to be updated in similar fashion -->
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function previewFile() {
            var preview = document.getElementById('preview');
            var file    = document.querySelector('input[type=file]').files[0];
            var reader  = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
            }
        }
    </script>
</body>
</html>
