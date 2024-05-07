<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Link to flag-icon-css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet">
    <style>
        .password {
            filter: blur(5px);
        }
        .password:hover {
            filter: none;
        }
        .copy-icon {
            cursor: pointer;
            margin-left: 10px; /* Add spacing */
        }
        .student-image {
            width: 100px; /* Adjust image width as needed */
            height: 100px; /* Adjust image height as needed */
            object-fit: cover;
            border-radius: 50%; /* Create circular shape */
            margin: 0 auto; /* Center the image horizontally */
            display: block;
            cursor: pointer; /* Add pointer cursor for interaction */
        }
        #fullScreenImageModal .modal-dialog {
            max-width: 100vw;
            margin: 0;
        }
        #fullScreenImageModal .modal-content {
            background-color: transparent;
            border: none;
        }
        #fullScreenImageModal .modal-body {
            padding: 0;
            text-align: center;
        }
        #fullScreenImageModal .modal-body img {
            max-width: 100%;
            max-height: 100vh;
            margin: auto;
        }
        .table-responsive {
            max-height: 75vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Students</h1>
        <a href="add_student.php" class="btn btn-success">Add Student</a>
    </div>
    <form method="get" action="">
        <div class="form-group">
            <input type="text" name="search" class="form-control" placeholder="Search by ID, first name, last name, or postcode">
        </div>
        <button type="submit" class="btn btn-primary mb-3">Search</button>
    </form>
</div>

<?php
require 'dbconnect.inc';

$searchTerm = $_GET['search'] ?? '';
$searchTerm = mysqli_real_escape_string($conn, $searchTerm);

// Check if form is submitted for deleting records
if(isset($_POST['delete_records'])) {
    $deleteIds = $_POST['delete_ids'];
    
    if(!empty($deleteIds)) {
        $deleteIdsStr = implode(',', $deleteIds);
        $deleteSql = "DELETE FROM student WHERE studentid IN ($deleteIdsStr)";
        if(mysqli_query($conn, $deleteSql)) {
            echo '<div class="alert alert-success mt-3">Selected records deleted successfully.</div>';
        } else {
            echo '<div class="alert alert-danger mt-3">Error deleting records: ' . mysqli_error($conn) . '</div>';
        }
    } else {
        echo '<div class="alert alert-warning mt-3">Please select records to delete.</div>';
    }
}

$records_per_page = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] - 1 : 0;
$offset = $page * $records_per_page;

$searchTerm = $_GET['search'] ?? '';
$searchTerm = mysqli_real_escape_string($conn, $searchTerm);

$sql = "SELECT * FROM student WHERE studentid LIKE '%$searchTerm%' OR firstname LIKE '%$searchTerm%' OR lastname LIKE '%$searchTerm%' OR postcode LIKE '%$searchTerm%' LIMIT $records_per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);

$total_pages_sql = "SELECT COUNT(*) FROM student WHERE studentid LIKE '%$searchTerm%' OR firstname LIKE '%$searchTerm%' OR lastname LIKE '%$searchTerm%' OR postcode LIKE '%$searchTerm%'";
$total_rows_result = mysqli_query($conn, the_total_pages_sql);
$total_rows = mysqli_fetch_array($total_rows_result)[0];
$total_pages = ceil($total_rows / $records_per_page);

while ($row = mysqli_fetch_array($result)) {
    echo '<tr>';
    echo '<td><input type="checkbox" name="delete_ids[]" value="' . $row['studentid'] . '"></td>';
    echo '<td>' . $row['studentid'] . '</td>';
    echo '<td><img src="' . $row['image'] . '" alt="Student Image" class="student-image" data-toggle="modal" data-target="#fullScreenImageModal" data-fullscreen-image="' . $row['image'] . '"></td>';
    echo '<td>' . $row['firstname'] . '</td>';
    echo '<td>' . $row['lastname'] . '</td>';
    echo '<td>' . $row['dob'] . '</td>';
    echo '<td>' . $row['house'] . ', ' . $row['town'] . ', ' . $row['county'] . ', ' . $row['postcode'] . '</td>';
    echo '<td>' . $row['country'];
    if ($row['country'] == 'Great Britain') {
        echo ' <span class="flag-icon flag-icon-gb"></span>';
    } else {
        echo ' <span class="flag-icon flag-icon-' . strtolower($row['country']) . '"></span>';
    }
    echo '</td>';
    echo '<td>' . $row['email'] . '</td>';
    echo '<td>' . $row['phone'] . '</td>';
    echo '<td class="password">' . $row['password'] . '<i class="fas fa-copy copy-icon" data-password="' . $row['password'] . '"></i></td>';
    echo '<td>';
    echo '<a href="edit_student.php?id=' . $row['studentid'] . '" class="btn btn-primary btn-sm mr-2">Edit</a>'; // Edit button
    echo '<a href="delete.php?id=' . $row['studentid'] . '" class="btn btn-danger btn-sm">Delete</a>'; // Delete button (separate link for deletion)
    echo '</td>';
    echo '</tr>';
}

            </table>
            <script>
                $(document).ready(function() {
                    $('#search').keyup(function() {
                        var searchText = $(this).val();
                        $.ajax({
                            url: '_includes/search.php',
                            method: 'POST',
                            data: {search: searchText},
                            success: function(data) {
                                $('#tableData').html(data);
                            }
                        });
                    });
                });
            </script>

        </div>
        <button type="submit" name="delete" class="btn btn-danger mb-3">Delete Selected</button>
    </form>
</div>

<!-- Bootstrap JS and jQuery (place before </body>) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('.copy-icon').on('click', function() {
            var password = $(this).data('password');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(password).select();
            document.execCommand("copy");
            $temp.remove();
        });

        // Select/Deselect all checkboxes when the Select All checkbox is clicked
        $('#select_all').on('click', function() {
            $(':checkbox').prop('checked', this.checked);
        });

        // Show full-screen image in modal on image click
        $('.student-image').on('click', function() {
            var imageUrl = $(this).data('fullscreen-image');
            $('#fullScreenImage').attr('src', imageUrl);
        });
    });
</script>

</body>
</html>
