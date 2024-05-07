<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sorry! No record can be found with that ID.</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <style>
        #main {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .border-right {
            border-right: 1px solid #dee2e6;
        }
        #desc {
            padding-left: 20px;
        }
        .search-box {
            margin-top: 20px;
            text-align: center;
        }
        .search-input {
            width: 60%; /* Adjust width of the input box */
            font-size: 16px; /* Increase font size */
            padding: 10px;
            border: 1px solid #ccc;
        }
        .search-button {
            padding: 10px 20px; /* Increase padding for bigger button */
            font-size: 18px; /* Larger text in the button */
            border-radius: 5px; /* Rounded corners for the button */
            border: 1px solid #007bff;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="main">
        <h1 class="mr-3 pr-3 align-top border-right inline-block align-content-center">404</h1>
        <div class="inline-block align-middle">
            <h2 class="font-weight-normal lead" id="desc">The page you requested was not found.</h2>
        </div>
    </div>
    <div class="container search-box">
        <form action="http://localhost/php_student_crm/student_crm/_includes/students.php" method="get">
            <input type="text" name="search" class="search-input" placeholder="Enter a search result" required>
            <button type="submit" class="search-button">Go</button>
        </form>
    </div>
</body>
</html>
