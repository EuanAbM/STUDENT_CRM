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
            margin-left: 10px;
        }
        .student-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto;
            display: block;
            cursor: pointer;
        }
        #fullScreenImageModal .modal-dialog {
            max-width: 100vw;
            margin: 0;
        }
        #fullScreenImageModal .modal-content {
            background-color: transparent;
            border: none;
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
    <form id="deleteForm" method="post" action="delete.php">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select_all" /></th>
                        <th>ID</th>
                        <th>Image</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>DOB</th>
                        <th>Address</th>
                        <th>Country</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableData">
                    <?php
                    require 'dbconnect.inc'; // Ensure the dbconnect.inc file path is correct

                    $searchTerm = $_GET['search'] ?? '';
                    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);

                    $records_per_page = 15;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $records_per_page;

                    $sql = "SELECT * FROM student WHERE studentid LIKE '%$searchTerm%' OR firstname LIKE '%$searchTerm%' OR lastname LIKE '%$searchTerm%' OR postcode LIKE '%$searchTerm%' LIMIT $records_per_page OFFSET $offset";
                    $result = mysqli_query($conn, $sql);

                    $total_pages_sql = "SELECT COUNT(*) FROM student WHERE studentid LIKE '%$searchTerm%' OR firstname LIKE '%$searchTerm%' OR lastname LIKE '%$searchTerm%' OR postcode LIKE '%$searchTerm%'";
                    $total_rows_result = mysqli_query($conn, $total_pages_sql);
                    $total_rows = mysqli_fetch_array($total_rows_result)[0];
                    $total_pages = ceil($total_rows / $records_per_page);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td><input type="checkbox" name="delete_ids[]" value="' . $row['studentid'] . '"></td>';
                            echo '<td>' . $row['studentid'] . '</td>';
                            echo '<td><img src="uploads/' . $row['image'] . '" alt="Student Image" class="student-image" data-toggle="modal" data-target="#fullScreenImageModal" data-fullscreen-image="' . $row['image'] . '"></td>';
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
                            echo '<a href="edit_student.php?id=' . $row['studentid'] . '" class="btn btn-primary btn-sm mr-2">Edit</a>';
                            echo '<a href="delete.php?id=' . $row['studentid'] . '" class="btn btn-danger btn-sm">Delete</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="12" class="text-center">No records found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-danger mb-3" onclick="confirmDelete()">Delete Selected</button>
    </form>
    <div class="clearfix">
        <div class="float-left">
            <strong>Total Records: <?php echo $total_rows; ?></strong>
        </div>
        <ul class="pagination float-right">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $searchTerm; ?>">Previous</a></li>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $searchTerm; ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                Please confirm that you would like to remove the selected records from the database. These cannot be restored.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitDelete()">DELETE</button>
            </div>
        </div>
    </div>
</div>

<!-- Include the JavaScript libraries -->
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

        $('#select_all').on('click', function() {
            $(':checkbox').prop('checked', this.checked);
        });

        $('.student-image').on('click', function() {
            var imageUrl = $(this).data('fullscreen-image');
            $('#fullScreenImage').attr('src', imageUrl);
            $('#fullScreenImageModal').modal('show');
        });
    });

    function confirmDelete() {
        var checkedNum = $('input[name="delete_ids[]"]:checked').length;
        if(checkedNum > 0) {
            $('#confirmDeleteModal').modal('show');
        } else {
            alert('Please select records to delete.');
        }
    }

    function submitDelete() {
        $('#deleteForm').submit();
    }
</script>

</body>
</html>
