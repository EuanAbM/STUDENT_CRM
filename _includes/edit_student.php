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
    $updateStmt = $conn->prepare("UPDATE student SET firstname=?, lastname=?, dob=?, house=?, town=?, county=?, country=?, postcode=?, image=? WHERE studentid=?");
    $updateStmt->bind_param("sssssssssi", $firstname, $lastname, $dob, $house, $town, $county, $country, $postcode, $image, $studentId);
    $updateStmt->execute();

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $passwordStmt = $conn->prepare("UPDATE student SET password=? WHERE studentid=?");
        $passwordStmt->bind_param("si", $password, $studentId);
        $passwordStmt->execute();
    }

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
                    </form>
                    
                    <!-- Back Button -->
                    <a href="students.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Back to Students</a>
                    
                    <!-- Emergency Contacts Preview -->
                    <hr>
                    <h5>Emergency Contacts</h5>
                    <div class="row">
                        <?php foreach ($emergencyContacts as $contact) : ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo $contact['relation']; ?></h6>
                                        <p class="card-text"><?php echo $contact['firstname'] . ' ' . $contact['lastname']; ?></p>
                                        <p class="card-text"><?php echo $contact['phone']; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery (place before </body>) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // JavaScript for image preview
    $(document).ready(function() {
        $('#image').change(function() {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(input).closest('.form-group').find('.profile-image').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
</script>

</body>
</html>