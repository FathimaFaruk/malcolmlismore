<?php
// Include db.php to establish a database connection
include '../db.php';

// Initialize variables for form fields and error messages
$title = $description = $userID = $categoryID = '';
$titleErr = $descriptionErr = $userErr = $categoryErr = $imageErr = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty($_POST["title"])) {
        $titleErr = "Title is required";
    } else {
        $title = $_POST["title"];
    }

    // Validate description
    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = $_POST["description"];
    }

    // Validate user ID (from dropdown)
    if (empty($_POST["userID"])) {
        $userErr = "User selection is required";
    } else {
        $userID = intval($_POST["userID"]);
    }

    // Validate category ID (from dropdown)
    if (empty($_POST["categoryID"])) {
        $categoryErr = "Category selection is required";
    } else {
        $categoryID = intval($_POST["categoryID"]);
    }

    // File upload and image validation
    if (isset($_FILES['image'])) {
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
        $destinationDirectory = '../images/portfolio/';
        if (!is_dir($destinationDirectory)) {
            $imageErr = 'Destination directory does not exist.';
        } elseif (!is_writable($destinationDirectory)) {
            $imageErr = 'Destination directory is not writable.';
        }

        // If there are no errors, save the image
        if (empty($imageErr)) {
            $uniqueFileName = uniqid() . '.' . $fileExt;
            $imagePath = $destinationDirectory . $uniqueFileName;

            // Use the copy function to copy the file from the temporary location to the images folder
            if (copy($fileTmp, $imagePath)) {
                echo "File uploaded successfully.<br>";
            } else {
                $imageErr = 'Failed to copy image file.';
            }
        }
    } else {
        $imageErr = 'Image file is required.';
    }

    // If there are no errors, proceed with adding the portfolio
    if (empty($titleErr) && empty($descriptionErr) && empty($userErr) && empty($categoryErr) && empty($imageErr)) {
        // Insert portfolio data into the database
        $sql = "INSERT INTO portfolio (Title, Description, UserID, Catid, ImagePath) VALUES ('$title', '$description', $userID, $categoryID, '$imagePath')";
        if ($conn->query($sql) === TRUE) {
            // Redirect to portfolio management page
            header("Location: portfoliomanagement.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch admin users and categories for the form
$users = [];
$categories = [];
$sql = "SELECT UserID, Username FROM users WHERE UserType = 'admin'"; // Select only admin users
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
    <title>Add Portfolio</title>
    <link rel="icon" type="image/x-icon" href="/images/Camera_Moto_30013.ico">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        /* Same styles as addcategory.php */
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
                        <a class="nav-link" href="#">Add Portfolio</a>
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
            <h1 class="display-4">Add Portfolio</h1>
            <p class="lead">Add a new portfolio to the management system.</p>
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
                            <option value="" disabled selected>Select an admin user</option>
                            <?php foreach ($users as $id => $name): ?>
                                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error"><?php echo $userErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="categoryID"><span style="color: #ffffff;">Category</span></label>
                        <select class="form-control" id="categoryID" name="categoryID" required>
                            <option value="" disabled selected>Select a category</option>
                            <?php foreach ($categories as $id => $name): ?>
                                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error"><?php echo $categoryErr; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="image"><span style="color: #ffffff;">Image</span></label>
                        <input type="file" class="form-control-file" id="image" name="image" required>
                        <span class="error"><?php echo $imageErr; ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Portfolio</button>
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
