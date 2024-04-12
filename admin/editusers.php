<?php
// Include db.php to establish database connection
include '../db.php';

// Initialize variables for form fields and error messages
$username = $email = $firstName = $lastName = '';
$usernameErr = $emailErr = $firstNameErr = $lastNameErr = '';

// Check if user ID is provided in the URL
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch user data from the database
    $sql = "SELECT * FROM users WHERE UserID = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['Username'];
        $email = $row['Email'];
        $firstName = $row['FirstName'];
        $lastName = $row['LastName'];
        $userType = $row['UserType'];
    } else {
        echo "User not found.";
        exit();
    }
} else {
    echo "User ID not provided.";
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = $_POST["username"];
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["email"];
    }

    // Validate first name
    if (empty($_POST["firstName"])) {
        $firstNameErr = "First name is required";
    } else {
        $firstName = $_POST["firstName"];
    }

    // Validate last name
    if (empty($_POST["lastName"])) {
        $lastNameErr = "Last name is required";
    } else {
        $lastName = $_POST["lastName"];
    }

    // If no errors, proceed with updating user information
    if (empty($usernameErr) && empty($emailErr) && empty($firstNameErr) && empty($lastNameErr)) {
        // Update user data in the database
        $newUsername = $_POST['username'];
        $newEmail = $_POST['email'];
        $newFirstName = $_POST['firstName'];
        $newLastName = $_POST['lastName'];
        $newUserType = $_POST['userType'];

        $sql = "UPDATE users SET Username = '$newUsername', Email = '$newEmail', FirstName = '$newFirstName', LastName = '$newLastName', UserType = '$newUserType' WHERE UserID = '$id'";
        if ($conn->query($sql) === TRUE) {
            // Redirect to user management page
            header("Location: usermanagement.php");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="icon" type="image/x-icon" href="/images/Camera_Moto_30013.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Edit Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usermanagement.php">User Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Portfolio Management</a>
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
            <h1 class="display-4">Edit Account</h1>
            <p class="lead">Update account information.</p>
        </div>
    </header>
    
    <section class="container content-section">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" method="POST">
                    <div class="form-group">
                        <label for="firstName"><x style="color: #ffffff;">First Name</x></label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $firstName; ?>" required>
                        <span class="error"><?php echo $firstNameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="lastName"><x style="color: #ffffff;">Last Name</x></label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastName; ?>" required>
                        <span class="error"><?php echo $lastNameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="username"><x style="color: #ffffff;">Username</x></label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
                        <span class="error"><?php echo $usernameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="email"><x style="color: #ffffff;">Email</x></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                        <span class="error"><?php echo $emailErr; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="userType"><x style="color: #ffffff;">User Type</x></label>
                        <select class="form-control" id="userType" name="userType">
                            <option value="Regular User" <?php if($userType == 'Regular User') echo 'selected'; ?>>Regular User</option>
                            <option value="Admin" <?php if($userType == 'Admin') echo 'selected'; ?>>Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Account</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            &copy; <?php echo date('Y'); ?> Malcolm Lismore Photography. All Rights Reserved.
        </div>
    </footer>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
