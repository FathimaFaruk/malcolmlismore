<?php
// Include db.php to establish database connection
include '../db.php';

// Initialize variables for form fields and error messages
$categoryName = $price = $description = '';
$categoryNameErr = $priceErr = $descriptionErr = $imageErr = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate category name
    if (empty($_POST["categoryName"])) {
        $categoryNameErr = "Category name is required";
    } else {
        $categoryName = $_POST["categoryName"];
    }

    // Validate price
    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } else {
        $price = $_POST["price"];
    }

    // Validate description
    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = $_POST["description"];
    }

// File upload and image validation
if (isset($_FILES['image'])) {
    $errors = [];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileTmp = $_FILES['image']['tmp_name'];
    $fileType = $_FILES['image']['type'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Allowed file extensions
    $allowedExtensions = ['jpeg', 'jpg', 'png'];

    if (!in_array($fileExt, $allowedExtensions)) {
        $imageErr = "Only JPEG and PNG files are allowed.";
    }

    // Limit file size to 5MB
    if ($fileSize > 5 * 1024 * 1024) {
        $imageErr = 'File size must be less than 5MB.';
    }

    // Check if the destination directory exists and has the correct permissions
    $destinationDirectory = '../images/category/';
    if (!is_dir($destinationDirectory)) {
        $imageErr = 'Destination directory does not exist.';
        echo "Error: Destination directory '$destinationDirectory' does not exist.<br>";
    } elseif (!is_writable($destinationDirectory)) {
        $imageErr = 'Destination directory is not writable.';
        echo "Error: Destination directory '$destinationDirectory' is not writable.<br>";
    }

    // If there are no errors, copy the file to the images folder
    if (empty($imageErr)) {
        $uniqueFileName = uniqid() . '.' . $fileExt;
        $imagePath = $destinationDirectory . $uniqueFileName;

        // Use the copy function to copy the file from temporary location to the images folder
        if (copy($fileTmp, $imagePath)) {
            // File copied successfully
            echo "File copied successfully.<br>";
        } else {
            $imageErr = 'Failed to copy image file.';
            $errorInfo = error_get_last();
            echo "Error copying file: " . $errorInfo['message'] . "<br>";
            error_log("Error copying file: " . $errorInfo['message']);
        }
    }
} else {
    $imageErr = 'Image file is required.';
}


    // If there are no errors, proceed with adding the category
    if (empty($categoryNameErr) && empty($priceErr) && empty($descriptionErr) && empty($imageErr)) {
        // Insert category data into the database
        $sql = "INSERT INTO category (catname, price, catdesc, catimgpath) VALUES ('$categoryName', '$price', '$description', '$imagePath')";
        if ($conn->query($sql) === TRUE) {
            // Redirect to category management page
            header("Location: portfoliomanagement.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="icon" type="image/x-icon" href="/images/Camera_Moto_30013.ico">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-image: url('/images/stock/outdoor-photography.png');
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh;
            position: relative;
        }

        body::after {
            content: "";
            background-color: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .jumbotron {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/admin/admindashboard.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Add Category</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="usermanagement.php">User Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./portfoliomanagement.php">Portfolio Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Messages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <header class="jumbotron jumbotron-fluid">
        <div class="container text-center">
            <h1 class="display-4">Add Category</h1>
            <p class="lead">Add a new category to the portfolio management system.</p>
        </div>
    </header>
    
    <!-- Form Section -->
    <section class="container content-section">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="categoryName"><span style="color: #ffffff;">Category Name</span></label>
                        <input type="text" class="form-control" id="categoryName" name="categoryName" value="<?php echo $categoryName; ?>" required>
                        <span class="error"><?php echo $categoryNameErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="price"><span style="color: #ffffff;">Price</span></label>
                        <input type="number" class="form-control" id="price" name="price" value="<?php echo $price; ?>" required>
                        <span class="error"><?php echo $priceErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="description"><span style="color: #ffffff;">Description</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $description; ?></textarea>
                        <span class="error"><?php echo $descriptionErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="image"><span style="color: #ffffff;">Image</span></label>
                        <input type="file" class="form-control-file" id="image" name="image" required>
                        <span class="error"><?php echo $imageErr; ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            &copy; 2024 Your Photography. All Rights Reserved.
        </div>
    </footer>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
