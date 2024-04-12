<?php
// Include db.php to establish database connection
include 'db.php';

// Start session
session_start();

// Dummy user data (replace with actual user authentication logic)
$loggedInUser = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Check if the user is logged in
$loggedIn = !empty($loggedInUser);

// Fetch all categories from the database
$sql = "SELECT * FROM category";
$result = $conn->query($sql);

// Check if the user is an admin
$isAdmin = ($loggedIn && isset($_SESSION['UserType']) && $_SESSION['UserType'] === 'admin');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - Malcolm Lismore Photography</title>
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

        /* Custom CSS for Image Showcase Section */
        .image-container {
            border-radius: 10px;
            /* Adjust border radius as needed */
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            /* Add shadow effect */
            transition: transform 0.3s ease-in-out;
            /* filter: grayscale(100%); */
        }

        .image-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        .image-container:hover {
            transform: scale(1.1);
            /* Scale up on hover */
            /* filter: grayscale(0); */
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
                    <li class="nav-item active">
                        <a class="nav-link" href="pricing.php">Pricing <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($loggedIn) { ?>
                        <?php if ($isAdmin) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin.php">Admin</a>
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
            <h1 class="display-4">Pricing</h1>
            <p class="lead">Find the perfect pricing plan for your photography needs.</p>
        </div>
    </header>

    <!-- Pricing Section -->
    <section class="container content-section">
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="col-md-4">
                        <div class="image-container">
                          <img src="' . $row['catimgpath'] . '" alt="' . $row['catname'] . '">
                          <div class="image-caption">
                                <h5 class="card-title">' . $row['catname'] . '</h5>
                                <p class="card-text">' . $row['catdesc'] . '</p>
                                <p class="card-text">$' . $row['price'] . ' / per picture</p>';
                    // if ($loggedIn && !$isAdmin) {
                    //     echo '<a href="#" class="btn btn-primary">Sign Up</a>';
                    // }
                    echo '
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col text-center">No pricing information available.</div>';
            }
            ?>
        </div>
    </section>
            <div class="md-5" style="height: 40px;"></div>
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