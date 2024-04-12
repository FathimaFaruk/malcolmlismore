<?php
// Include db.php to establish database connection
include '../db.php';

// Initialize variables for form fields
$catid = '';
$categoryName = '';
$price = '';
$description = '';
$imagePath = '';
$categoryNameErr = '';
$priceErr = '';
$descriptionErr = '';
$imageErr = '';

// Check if catid is provided in the URL
if (isset($_GET['catid'])) {
    $catid = intval($_GET['catid']); // Convert catid to an integer
    
    // Fetch category data from the database
    $sql = "SELECT * FROM category WHERE catid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $catid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $category = $result->fetch_assoc();
        $categoryName = $category['catname'];
        $price = $category['price'];
        $description = $category['catdesc'];
        $imagePath = $category['catimgpath'];
    } else {
        echo "Category not found.";
        exit;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve catid from POST data
    $catid = intval($_POST['catid']);
    
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
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
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
        } elseif (!is_writable($destinationDirectory)) {
            $imageErr = 'Destination directory is not writable.';
        }

        // If there are no errors, copy the file to the images folder
        if (empty($imageErr)) {
            $uniqueFileName = uniqid() . '.' . $fileExt;
            $imagePath = $destinationDirectory . $uniqueFileName;

            // Use the move_uploaded_file function to move the file from temporary location to the images folder
            if (move_uploaded_file($fileTmp, $imagePath)) {
                // File moved successfully
            } else {
                $imageErr = 'Failed to upload image file.';
            }
        }
    } else {
        $imagePath = $_POST['imagePath'];
    }

    // If there are no errors, proceed with updating the category
    if (empty($categoryNameErr) && empty($priceErr) && empty($descriptionErr) && empty($imageErr)) {
        // Update category data in the database
        $sql = "UPDATE category SET catname = ?, price = ?, catdesc = ?, catimgpath = ? WHERE catid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sdssi', $categoryName, $price, $description, $imagePath, $catid);
        
        if ($stmt->execute()) {
            // Redirect to portfolio management page after successful update
            header("Location: portfoliomanagement.php");
            exit();
        } else {
            echo "Error updating category: " . $stmt->error;
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
    <title>Edit Category</title>
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
                        <a class="nav-link" href="#">Edit Category</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usermanagement.php">User Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfoliomanagement.php">Portfolio Management</a>
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
            <h1 class="display-4">Edit Category</h1>
            <p class="lead">Edit the category details below.</p>
        </div>
    </header>

    <!-- Form Section -->
    <section class="container content-section">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="catid" value="<?php echo $catid; ?>">
                    <input type="hidden" name="imagePath" value="<?php echo $imagePath; ?>">
                    
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
                        <input type="file" class="form-control-file" id="image" name="image">
                        <span class="error"><?php echo $imageErr; ?></span>
                        <?php if ($imagePath): ?>
                            <img src="<?php echo $imagePath; ?>" alt="Category Image" style="width: 100px; height: 100px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Category</button>
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
