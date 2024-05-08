<?php
require '../_includes/dbconnect.inc'; // Assuming the same database connection logic

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve student ID from the URL
$studentId = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch the image from the database
$sql = "SELECT image FROM student WHERE studentId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$imageData = $result->fetch_assoc();

$image = $imageData['image'] ?? null;

// HTML and PHP to display and update the image
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Student Image</title>
</head>
<body>
    <?php if ($image): ?>
        <img src="data:image/jpeg;base64,<?=base64_encode($image)?>" alt="Student Image"/>
    <?php else: ?>
        <p>No image found.</p>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="newImage" accept="image/*">
        <button type="submit" name="upload" value="Upload Image">Upload New Image</button>
    </form>

    <?php
    // Check if the form was submitted
    if (isset($_POST['upload'])) {
        // Check if file is uploaded
        if (!empty($_FILES['newImage']['tmp_name'])) {
            // Read the image file
            $imageContent = file_get_contents($_FILES['newImage']['tmp_name']);
            $sql = "UPDATE student SET image = ? WHERE studentId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("bs", $imageContent, $studentId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<p>Image updated successfully.</p>";
                echo '<img src="data:image/jpeg;base64,' . base64_encode($imageContent) . '" alt="Updated Image"/>';
            } else {
                echo "<p>Failed to update image.</p>";
            }
        }
    }

    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
