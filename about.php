<?php
// Include db.php to establish database connection
include 'db.php';

// Start session
session_start();

// Dummy user data (replace with actual user authentication logic)
$loggedInUser = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Check if the user is logged in
$loggedIn = !empty($loggedInUser);

// Fetch user details from database based on username
if ($loggedIn) {
    $sql = "SELECT * FROM users WHERE Username = '$loggedInUser'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userType = $row['UserType'];
    }
}

// Check if the user is an admin
$isAdmin = ($loggedIn && isset($userType) && $userType === 'admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Malcolm Lismore Photography</title>
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
    </style>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                    <li class="nav-item active">
                        <a class="nav-link" href="#">About <span class="sr-only">(current)</span></a>
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
                    <?php if ($loggedIn) { ?>
                        <?php if ($isAdmin) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/admindashboard.php">Admin</a>
                            </li>
                        <?php } ?>
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
            <h1 class="display-4">About Malcolm Lismore Photography</h1>
            <p class="lead">Malcolm Lismore is a freelance photographer based on the North West coast of Scotland. His biggest passion in photography is for the natural world.</p>
            <p class="lead">He sells many images of the rugged Scottish landscape, its natural wildlife, and coastal birds. However, it’s not just landscape and wildlife photography he is interested in, like many photographers he can also be hired for weddings, portraits, and special events.</p>
        </div>
    </header>
    <div class="container">
    <div class="row">
        <div class="col-md-8">
            <h2 style="color: white;">Malcolm Lismore Photography</h2>
            <p style="color: white;">Malcolm Lismore is a freelance photographer based on the North West coast of Scotland. His biggest passion in photography is for the natural world.</p>
            <p style="color: white;">He sells many images of the rugged Scottish landscape, its natural wildlife, and coastal birds. However, it’s not just landscape and wildlife photography he is interested in, like many photographers he can also be hired for weddings, portraits, and special events.</p>
        </div>
        <div class="col-md-4">
            <!-- Embed Google Maps iframe here -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d788.2676169525379!2d-3.1866396587672745!3d55.94318805192517!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4887c783a549bb2f%3A0x8e6da0a8a18e2cdc!2sParking%20lot%2C%20Buccleuch%20Pl%2C%20Edinburgh%2C%20UK!5e0!3m2!1sen!2slk!4v1712648173732!5m2!1sen!2slk" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>
<div style="height: 100px"></div>
    <!-- Footer Section -->
    <footer>
        <div class="container">
            &copy; <?php echo date("Y"); ?> Malcolm Lismore Photography. All Rights Reserved.
        </div>
    </footer>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
