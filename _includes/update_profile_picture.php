<?php
// update_profile_picture.php

session_start();
require '../_includes/dbconnect.inc'; // Assuming the same database connection logic

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($file_info, $_FILES['profile_picture']['tmp_name']);

        if (in_array($file_type, $allowed_types) && $_FILES['profile_picture']['size'] <= 2000000) { // 2MB max size
            $target_directory = 'uploads/';
            $target_file = $target_directory . basename($_FILES['profile_picture']['name']);

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare('UPDATE users SET profile_picture = ? WHERE id = ?');
                $stmt->execute([$target_file, $_SESSION['user_id']]);
                echo 'Profile picture updated successfully.';
            } else {
                echo 'There was an error uploading your file.';
            }
        } else {
            echo 'Invalid file type or size too large.';
        }

        finfo_close($file_info);
    } else {
        echo 'No file uploaded.';
    }
}
?>