<?php
// Include db.php to establish a database connection
include '../db.php';

// Initialize variables and error messages
$titleErr = $descriptionErr = $userErr = $categoryErr = $imageErr = '';
$photoID = isset($_POST['PhotoID']) ? intval($_POST['PhotoID']) : (isset($_GET['PhotoID']) ? intval($_GET['PhotoID']) : null);
$title = $description = $userID = $categoryID = $imagePath = '';

// Fetch existing portfolio data if PhotoID is provided
if ($photoID) {
    $sql = "SELECT Title, Description, UserID, CatID, ImagePath FROM portfolio WHERE PhotoID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $photoID);
        $stmt->execute();
        $stmt->bind_result($title, $description, $userID, $categoryID, $imagePath);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form values, prioritizing POST data
    $title = $_POST["title"] ?? $title;
    $description = $_POST["description"] ?? $description;
    $userID = isset($_POST["userID"]) ? intval($_POST["userID"]) : $userID;
    $categoryID = isset($_POST["categoryID"]) ? intval($_POST["categoryID"]) : $categoryID;

    // Validate form values
    if (empty($title)) {
        $titleErr = "Title is required";
    }

    if (empty($description)) {
        $descriptionErr = "Description is required";
    }

    if (empty($userID)) {
        $userErr = "User selection is required";
    }

    if (empty($categoryID)) {
        $categoryErr = "Category selection is required";
    }

    // Handle file upload and validation
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Allowed file extensions
        $allowedExtensions = ['jpeg', 'jpg', 'png'];

        // Validate file extension
        if (!in_array($fileExt, $allowedExtensions)) {
            $imageErr = "Only JPEG and PNG files are allowed.";
        }

        // Validate file size (limit to 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            $imageErr = 'File size must be less than 5MB.';
        }

        // Validate and handle file upload
        if (empty($imageErr)) {
            $destinationDirectory = '../images/portfolio/';
            $uniqueFileName = uniqid() . '.' . $fileExt;
            $imagePath = $destinationDirectory . $uniqueFileName;

            if (move_uploaded_file($fileTmp, $imagePath)) {
                echo "File uploaded successfully.<br>";
            } else {
                $imageErr = 'Failed to move uploaded file.';
            }
        }
    }

    // Update the portfolio in the database if there are no errors
    if (empty($titleErr) && empty($descriptionErr) && empty($userErr) && empty($categoryErr) && empty($imageErr)) {
        $updateSQL = "UPDATE portfolio SET Title = ?, Description = ?, UserID = ?, CatID = ?, ImagePath = ? WHERE PhotoID = ?";

        $stmt = $conn->prepare($updateSQL);
        if ($stmt) {
            // Bind parameters based on whether a new image path is available
            if ($imagePath !== '') {
                $stmt->bind_param("ssiis", $title, $description, $userID, $categoryID, $imagePath, $photoID);
            } else {
                // No new image path, use existing path
                $stmt->bind_param("ssiii", $title, $description, $userID, $categoryID, $photoID);
            }

            // Execute the statement
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                // Redirect to portfolio management page
                header("Location: portfoliomanagement.php");
                exit();
            } else {
                echo "Error updating portfolio: " . $conn->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            die("Error preparing statement: " . $conn->error);
        }
    }
}

// Fetch admin users and categories for the form
$users = [];
$categories = [];
$sql = "SELECT UserID, Username FROM users WHERE UserType = 'admin'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[$row['UserID']] = $row['Username'];
    }
}

$sql = "SELECT catid, catname FROM category";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[$row['catid']] = $row['catname'];
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
    <title>Edit Portfolio</title>
    <link rel="icon" type="image/x-icon" href="/images/Camera_Moto_30013.ico">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        /* Same styles as addcategory.php and addportfolio.php */
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
        /* Styling for the image preview */
        .image-preview {
            display: block;
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
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
                        <a class="nav-link" href="#">Edit Portfolio</a>
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
            <h1 class="display-4">Edit Portfolio</h1>
            <p class="lead">Edit the selected portfolio entry.</p>
        </div>
    </header>

    <!-- Form Section -->
    <section class="container content-section">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title"><span style="color: #ffffff;">Title</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
                        <span class="error"><?php echo $titleErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="description"><span style="color: #ffffff;">Description</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $description; ?></textarea>
                        <span class="error"><?php echo $descriptionErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="userID"><span style="color: #ffffff;">User</span></label>
                        <select class="form-control" id="userID" name="userID" required>
                            <option value="" disabled selected>Select a user</option>
                            <?php foreach ($users as $id => $username) : ?>
                                <option value="<?php echo $id; ?>" <?php if ($id == $userID) echo 'selected'; ?>><?php echo $username; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error"><?php echo $userErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="categoryID"><span style="color: #ffffff;">Category</span></label>
                        <select class="form-control" id="categoryID" name="categoryID" required>
                            <option value="" disabled selected>Select a category</option>
                            <?php foreach ($categories as $id => $name) : ?>
                                <option value="<?php echo $id; ?>" <?php if ($id == $categoryID) echo 'selected'; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error"><?php echo $categoryErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="image"><span style="color: #ffffff;">Image</span></label>
                        <input type="file" class="form-control-file" id="image" name="image">
                        <span class="error"><?php echo $imageErr; ?></span>

                        <!-- Add an image preview under the file input -->
                        <?php if ($imagePath): ?>
                            <img src="<?php echo $imagePath; ?>" alt="Image Preview" class="image-preview">
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Portfolio</button>
                </form>
            </div>
        </div>
    </section>
    <div style="height: 100px;"></div>
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