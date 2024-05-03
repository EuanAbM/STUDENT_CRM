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