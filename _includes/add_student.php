<?php
require 'dbconnect.inc';

// Generate a new student ID
$result = mysqli_query($conn, "SELECT MAX(studentid) AS max_id FROM student");
$row = mysqli_fetch_assoc($result);
$max_id = $row['max_id'];
$new_id = str_pad($max_id + 1, 6, '0', STR_PAD_LEFT);

// Check if form is submitted for student addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentid = mysqli_real_escape_string($conn, $_POST['studentid']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $house = mysqli_real_escape_string($conn, $_POST['house']);
    $town = mysqli_real_escape_string($conn, $_POST['town']);
    $county = mysqli_real_escape_string($conn, $_POST['county']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $postcode = mysqli_real_escape_string($conn, $_POST['postcode']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<div class="alert alert-danger mt-3">Invalid email format.</div>';
        exit;
    }

    // Validate password pattern
    $password_pattern = "/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,}$/";
    if (!preg_match($password_pattern, $password)) {
        echo '<div class="alert alert-danger mt-3">Invalid password format.</div>';
        exit;
    }

    // Upload image file
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        echo '<div class="alert alert-danger mt-3">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>';
        exit;
    }
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    $insertSql = "INSERT INTO student (studentid, firstname, lastname, dob, house, town, county, country, postcode, password, image) VALUES ('$studentid', '$firstname', '$lastname', '$dob', '$house', '$town', '$county', '$country', '$postcode', '$password', '$image')";

    if (mysqli_query($conn, $insertSql)) {
        echo '<div class="alert alert-success mt-3">Student added successfully.</div>';
    } else {
        echo '<div class="alert alert-danger mt-3">Error adding student: ' . mysqli_error($conn) . '</div>';
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Other form data processing...

    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = md5($password);

    $sql = "INSERT INTO students (password, ...) VALUES ('$hashed_password', ...)";
    // Execute the query...

    // Other form data processing...
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Autocomplete address using getAddress.io API
            $('#address').on('input', function() {
                var searchTerm = $(this).val().trim();
                var apiKey = 'QHE6QrL6FEygXV7STMb8Kw26663';
                var apiUrl = `https://api.getAddress.io/autocomplete/${searchTerm}?api-key=${apiKey}`;

                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    success: function(response) {
                        var suggestions = response.suggestions;

                        // Display autocomplete suggestions
                        $('#address-suggestions').empty();
                        suggestions.forEach(function(suggestion) {
                            var option = $('<option>').text(suggestion.description);
                            $('#address-suggestions').append(option);
                        });
                    },
                    error: function(err) {
                        console.error('Error fetching address suggestions:', err);
                    }
                });
            });

            // Handle address selection from autocomplete
            $('#address-suggestions').on('change', function() {
                var selectedAddress = $(this).val();

                // Fill address field with selected address
                $('#address').val(selectedAddress);

                // Use getAddress.io API to retrieve full address details
                var apiKey = 'QHE6QrL6FEygXV7STMb8Kw26663';
                var apiUrl = `https://api.getAddress.io/find/${selectedAddress}?api-key=${apiKey}`;

                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    success: function(response) {
                        var address = response.address;

                        // Fill other address fields
                        $('#town').val(address.town);
                        $('#county').val(address.county);
                        $('#country').val(address.country);
                        $('#postcode').val(address.postal_code);
                    },
                    error: function(err) {
                        console.error('Error fetching full address details:', err);
                    }
                });
            });

            // Regenerate student ID
            $('#regenerateId').on('click', function() {
                var maxId = parseInt($('#studentid').val());
                var newId = String(maxId + 1).padStart(6, '0');
                $('#studentid').val(newId);
            });

            // Preview uploaded image
            $('#image').on('change', function(event) {
                var reader = new FileReader();
                reader.onload = function(){
                    $('#image-preview').attr('src', reader.result).show();
                };
                reader.readAsDataURL(event.target.files[0]);
            });
        });
    </script>
</head>
<body>
<div class="container mt-5">
    <form method="post" action="" enctype="multipart/form-data">
   
    <div class="form-group">
    <label for="studentid">Student ID</label>
    <input type="text" class="form-control" id="studentid" name="studentid" value="<?php echo $new_id; ?>" required readonly>
</div>

        <div class="form-group">
            <label for="firstname">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Last Name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob" required>
        </div>
        <div class="form-group">
            <label for="house">House</label>
            <input type="text" class="form-control" id="house" name="house" required>
        </div>
        <div class="form-group">
            <label for="town">Town</label>
            <input type="text" class="form-control" id="town" name="town" required>
        </div>
        <div class="form-group">
            <label for="county">County</label>
            <input type="text" class="form-control" id="county" name="county" required>
        </div>
        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" class="form-control" id="country" name="country" required>
        </div>
        <div class="form-group">
            <label for="postcode">Postcode</label>
            <input type="text" class="form-control" id="postcode" name="postcode" required>
    </div>
    <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="image">Student Image</label>
            <input type="file" class="form-control" id="image" name="image" required>
            <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 200px; margin-top: 10px;">
        </div>
       
        <button type="submit" class="btn btn-primary">Add Student</button>
        <button type="button" class="btn btn-primary" onclick="window.location.href='students.php'">Back to Students</button>
        </form>
        </div>
        </body>
        
        </html>