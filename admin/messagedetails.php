<?php
// Include the database connection
include '../db.php';

// Check if an EnquiryID is provided in the URL
$enquiryId = isset($_GET['EnquiryID']) ? intval($_GET['EnquiryID']) : 0;

// Fetch the details of the specified message from the database
$sql = "SELECT * FROM enquiries WHERE EnquiryID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $enquiryId);
$stmt->execute();
$result = $stmt->get_result();

// If the message is found, fetch its details
if ($result->num_rows > 0) {
    $message = $result->fetch_assoc();
} else {
    echo "No message found with EnquiryID: " . $enquiryId;
    exit();
}

// Check if the form has been submitted to toggle the status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggleStatus'])) {
    $newStatus = $message['Status'] === 'Pending' ? 'Completed' : 'Pending';

    // Update the message status in the database
    $updateSql = "UPDATE enquiries SET Status = ? WHERE EnquiryID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $newStatus, $enquiryId);
    $updateStmt->execute();

    // Refresh the page to show the updated status
    header("Location: messageDetails.php?EnquiryID=" . $enquiryId);
    exit();
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
    <title>Message Details</title>
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/admin/admindashboard.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                  <li class="nav-item active">
                        <a class="nav-link" href="#">Message Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usermanagement.php">User Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfoliomanagement.php">Portfolio Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messageview.php">Messages</a>
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
            <h1 class="display-4">Message Details</h1>
            <p class="lead">Review and manage message details.</p>
        </div>
    </header>

    <!-- Content Section -->
    <section class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Message Details</h5>
            </div>
            <div class="card-body">
                <h6>Enquiry ID: <?php echo htmlspecialchars($message['EnquiryID']); ?></h6>
                <h6>Name: <?php echo htmlspecialchars($message['Name']); ?></h6>
                <h6>Email: <?php echo htmlspecialchars($message['Email']); ?></h6>
                <h6>Phone: <?php echo htmlspecialchars($message['Phone']); ?></h6>
                <h6>Message: <?php echo htmlspecialchars($message['Message']); ?></h6>
                <h6>Status: <?php echo htmlspecialchars($message['Status']); ?></h6>

                <!-- Button to toggle message status -->
                <form method="POST">
                    <button type="submit" name="toggleStatus" class="btn btn-primary">
                        <?php echo $message['Status'] === 'Pending' ? 'Mark as Completed' : 'Mark as Pending'; ?>
                    </button>
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
