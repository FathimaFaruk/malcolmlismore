<?php
// Include db.php to establish database connection
include 'db.php';

// Start session
session_start();

// Dummy user data (replace with actual user authentication logic)
$loggedInUser = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Check if the user is logged in
$loggedIn = !empty($loggedInUser);

// Initialize variables for form data
$name = $email = $phone = $location = $eventDate = $message = $status = "";
$status = "Pending";
$userID = null;

// Define variables and initialize with empty values
$nameErr = $emailErr = $phoneErr = $locationErr = $eventDateErr = $messageErr = "";
$successMessage = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        // Check if email address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate phone number
    if (empty($_POST["phone"])) {
        $phoneErr = "Phone number is required";
    } else {
        $phone = test_input($_POST["phone"]);
        // Check if phone number is valid
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            $phoneErr = "Invalid phone number";
        }
    }

    // Validate location
    if (empty($_POST["location"])) {
        $locationErr = "Location is required";
    } else {
        $location = test_input($_POST["location"]);
    }

    // Validate event date
    if (empty($_POST["eventDate"])) {
        $eventDateErr = "Event date is required";
    } else {
        $eventDate = test_input($_POST["eventDate"]);
    }

    // Validate message
    if (empty($_POST["message"])) {
        $messageErr = "Message is required";
    } else {
        $message = test_input($_POST["message"]);
    }

    // If no errors, insert data into database
    if (empty($nameErr) && empty($emailErr) && empty($phoneErr) && empty($locationErr) && empty($eventDateErr) && empty($messageErr)) {
        // Get user ID if logged in
        if ($loggedIn) {
            $sql = "SELECT UserID FROM users WHERE Username = '$loggedInUser'";
            $result = $conn->query($sql);
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $userID = $row['UserID'];
            }
        }

        // Insert data into enquiries table
        $sql = "INSERT INTO enquiries (UserID, Name, Email, Phone, Location, EventDate, Message, Status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $userID, $name, $email, $phone, $location, $eventDate, $message, $status);
        if ($stmt->execute()) {
            $successMessage = "Your enquiry has been submitted successfully.";
            // Reset form fields
            $name = $email = $phone = $location = $eventDate = $message = "";
        } else {
            $successMessage = "Error: Unable to submit your enquiry.";
        }
        $stmt->close();
    }
}

// Function to sanitize form data
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Malcolm Lismore Photography</title>
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
            /* Adjust opacity as needed */
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
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Contact <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($loggedIn) { ?>
                        <li class="nav-item">
                            <span class="navbar-text">Welcome, <?php echo $loggedInUser; ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="signin.php">Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="signup.php">Sign Up</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <header class="jumbotron jumbotron-fluid">
        <div class="container text-center">
            <h1 class="display-4">Contact Us</h1>
            <p class="lead">Get in touch with us for inquiries, bookings, or any other questions.</p>
        </div>
    </header>

    <!-- Contact Form Section -->
    <section class="container content-section">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if (!$loggedIn) { ?>
                    <p class="text-center" style="color: #ffffff;">Please <a href="signin.php">sign in</a> to submit an enquiry.</p>
                <?php } else { ?>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="name">
                                <x style="color: #ffffff;">Name:</x>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
                            <span class="error"><?php echo $nameErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="email">
                                <x style="color: #ffffff;">Email:</x>
                            </label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                            <span class="error"><?php echo $emailErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="phone">
                                <x style="color: #ffffff;">Phone:</x>
                            </label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>">
                            <span class="error"><?php echo $phoneErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="location">
                                <x style="color: #ffffff;">Address:</x>
                            </label>
                            <input type="text" class="form-control" id="location" name="location" value="<?php echo $location; ?>">
                            <span class="error"><?php echo $locationErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="eventDate">
                                <x style="color: #ffffff;">Event Date:</x>
                            </label>
                            <input type="date" class="form-control" id="eventDate" name="eventDate" value="<?php echo $eventDate; ?>">
                            <span class="error"><?php echo $eventDateErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="message">
                                <x style="color: #ffffff;">Message:</x>
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="4"><?php echo $message; ?></textarea>
                            <span class="error"><?php echo $messageErr; ?></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <span class="success"><?php echo $successMessage; ?></span>
                    </form>
                <?php } ?>
            </div>
        </div>
    </section>
    <div style="height: 100px"></div>
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