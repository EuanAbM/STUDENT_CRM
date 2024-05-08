<?php
session_start();
require '../_includes/dbconnect.inc'; 

// Handle POST request to add student information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insert new student info logic here...

    // Handling file upload for profile image
    if (isset($_FILES['image']['name']) && $_FILES['image']['size'] > 0) {
        $allowedTypes = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $fileType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES['image']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            if ($_FILES['image']['size'] < 2000000) { // Limit file size to under 2MB
                $targetDir = "uploads/"; // Ensure this directory exists and is writable
                $fileName = time() . basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    // Insert image path into database, along with other student info
                    // Example SQL: INSERT INTO student (firstname, lastname, dob, image) VALUES (?, ?, ?, ?)
                } else {
                    echo '<div class="alert alert-danger">Error uploading file.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">File size too large. Must be less than 2MB.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Invalid file type. Only JPG, PNG, and GIF are allowed.</div>';
        }
    }


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add New Student</div>
                <div class="card-body">
                    <!-- Student Information Form -->
                    <form method="post" action="" enctype="multipart/form-data">
                        <input type="file" name="profile_picture" id="profile_picture" onchange="previewImage();">
                        <img id="imagePreview" src="#" alt="Image Preview" style="display: none; max-width: 150px; height: auto;">
                        <script>
                        function previewImage() {
                            var file = document.getElementById('profile_picture').files[0];
                            var reader = new FileReader();
                            
                            reader.onloadend = function() {
                                let preview = document.getElementById('imagePreview');
                                preview.src = reader.result;
                                preview.style.display = 'block';
                            }
                            
                            if (file) {
                                reader.readAsDataURL(file);
                            } else {
                                document.getElementById('imagePreview').src = "";
                                document.getElementById('imagePreview').style.display = 'none';
                            }
                        }
                        </script>

                        <!-- Student Personal Details -->
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname">
                        </div>
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob">
                        </div>
                        <div class="form-group">
                            <label for="house">House</label>
                            <input type="text" class="form-control" id="house" name="house">
                        </div>
                        <div class="form-group">
                            <label for="town">Town</label>
                            <input type="text" class="form-control" id="town" name="town">
                        </div>
                        <div class="form-group">
                            <label for="county">County</label>
                            <input type="text" class="form-control" id="county" name="county">
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country">
                        </div>
                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" class="form-control" id="postcode" name="postcode">
                        </div>
                        <div class="form-group">
                            <label for="password">Set Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
