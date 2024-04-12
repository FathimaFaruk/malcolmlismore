<?php
// Include db.php to establish database connection
include 'db.php';

// Initialize variables for form fields and error messages
$username = $email = $password = $firstName = $lastName = '';
$usernameErr = $emailErr = $passwordErr = $firstNameErr = $lastNameErr = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = $_POST["username"];
        // Check if username already exists in the database
        $sql = "SELECT * FROM users WHERE Username = '$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $usernameErr = "Username already exists";
        }
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["email"];
        // Check if email already exists in the database
        $sql = "SELECT * FROM users WHERE Email = '$email'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $emailErr = "Email already exists";
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];
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

    // If no errors, proceed with registration
    if (empty($usernameErr) && empty($emailErr) && empty($passwordErr) && empty($firstNameErr) && empty($lastNameErr)) {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user data into the database
        $sql = "INSERT INTO users (Username, Email, Password, FirstName, LastName, UserType) VALUES ('$username', '$email', '$hashedPassword', '$firstName', '$lastName', 'Regular User')";
        if ($conn->query($sql) === TRUE) {
            // Redirect to a success page or homepage
            header("Location: index.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
            background-color: rgba(0, 0, 0, 0.5); /* Adjust opacity as needed */
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
        /* Glass effect for the submit button */
    .btn-primary {
        background-color: rgba(255, 255, 255, 0.1);
        border: 2px solid #ffffff;
        color: #ffffff;
        padding: 10px 20px;
        font-size: 16px;
        position: relative;
        overflow: hidden;
        transition: color 0.4s, border-color 0.4s;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        background-color: rgba(255, 255, 255, 0.1);
        width: 300%;
        height: 300%;
        border-radius: 50%;
        transition: width 0.4s, height 0.4s, top 0.4s, left 0.4s;
        z-index: 0;
        transform: translate(-50%, -50%);
    }

    .btn-primary:hover::before {
        width: 0;
        height: 0;
        top: 50%;
        left: 50%;
    }

    .btn-primary span {
        position: relative;
        z-index: 1;
    }
    </style>
</head>
<body>
    <!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Malcolm Lismore Photography</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="portfolio.php">Portfolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pricing.php">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="signin.php">Sign In</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">Sign Up <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Header Section -->
    <header class="jumbotron jumbotron-fluid">
        <div class="container text-center">
            <h1 class="display-4">Sign Up</h1>
            <p class="lead">Get registered your account to access to our platform.</p>
        </div>
    </header>
    
    <section class="container content-section">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group">
                        <label for="firstName"><x style="color: #ffffff;">First Name</x></label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $firstName; ?>" required>
                        <span class="text-danger"><?php echo $firstNameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="lastName"><x style="color: #ffffff;">Last Name</x></label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastName; ?>" required>
                        <span class="text-danger"><?php echo $lastNameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="username"><x style="color: #ffffff;">Username</x></label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
                        <span class="text-danger"><?php echo $usernameErr; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="email"><x style="color: #ffffff;">Email</x></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                        <span class="text-danger"><?php echo $emailErr; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password"><x style="color: #ffffff;">Password</x></label>
                        <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
                        <span class="text-danger"><?php echo $passwordErr; ?></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Sign Up</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            &copy; 2024 Malcolm Lismore Photography. All Rights Reserved.
        </div>
    </footer>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
