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
$isAdmin = ($loggedIn && isset($userType) && $userType === 'Admin');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malcolm Lismore Photography</title>
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

        /* Custom CSS for Image Showcase Section */
        .image-container {
            border-radius: 10px;
            /* Adjust border radius as needed */
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            /* Add shadow effect */
            transition: transform 0.3s ease-in-out;
            filter: grayscale(100%);
        }

        .image-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        .image-container:hover {
            transform: scale(1.1);
            /* Scale up on hover */
            filter: grayscale(0);
        }

        .image-caption {
            text-align: center;
            color: white;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .image-caption h3 {
            margin-bottom: 5px;
        }

        .image-caption p {
            margin-bottom: 0;
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
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
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
            <h1 class="display-4">Welcome to Malcolm Lismore Photography</h1>
            <p class="lead">Capturing the beauty of nature through the lens</p>
        </div>
    </header>

    <!-- Image Showcase Section -->
    <section class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="image-container">
                    <img src="/images/stock/winter_dawn_glencoe_scotland-wallpaper-1920x1080.jpg" alt="Glencoe Valley">
                    <div class="image-caption">
                        <h3>Landscape Photography</h3>
                        <p>Glen Coe is a glen of volcanic origins, in the Highlands of Scotland. It lies in the north of the county of Argyll, close to the border with the historic province of Lochaber, within the modern council area of Highland.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="image-container">
                    <img src="/images/stock/2128.jpg" alt="red deer at the Highland Wildlife Park">
                    <div class="image-caption">
                        <h3>Wildlife Photography</h3>
                        <p>The Scottish red deer is a subspecies of red deer, which is native to Great Britain. Like the red deer of Ireland, it migrated from continental Europe sometime in the Stone Age. The Scottish red deer is farmed for meat.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="image-container">
                    <img src="/images/stock/2081587.jpg" alt="Common eider">
                    <div class="image-caption">
                        <h3>Costal Birds Photography</h3>
                        <p>Eider is a widely distributed breeding species around Scotland's shores. Outside the breeding season, it is mainly found in sheltered coastal waters, sometimes in flocks of several hundred birds.</p>
                    </div>
                </div>
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