<?php
// Include db.php to establish database connection
include '../db.php';

// Start session
session_start();

// Check if the user is logged in
$loggedInUser = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$loggedIn = !empty($loggedInUser);

// Ensure only authorized users can access the page
if (!$loggedIn) {
  header("Location: signin.php");
  exit();
}

// Set the default status filter
$statusFilter = isset($_GET['status']) && ($_GET['status'] == 'completed') ? 'Completed' : 'Pending';

// Fetch messages based on the selected status
$sql = "SELECT EnquiryID, Name, Email, Phone FROM enquiries WHERE Status = ? ORDER BY EventDate DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $statusFilter);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Messages</title>
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

    .container-fluid {
      padding-top: 70px;
      /* Adjust based on the height of the navbar */
      padding-bottom: 70px;
      /* Adjust based on the height of the footer */
    }

    .card-header {
      background-color: #343a40;
      color: white;
    }

    .card {
      margin-bottom: 20px;
    }

    .btn-primary,
    .btn-secondary {
      background-color: #343a40;
      border-color: #343a40;
    }

    .btn-primary:hover,
    .btn-secondary:hover {
      background-color: #23272b;
      border-color: #1d2124;
    }

    .table {
      background-color: #fff;
    }

    .table th,
    .table td {
      border-color: #dee2e6;
    }

    .table thead th {
      background-color: #343a40;
      color: white;
      border-color: #343a40;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.2);
    }

    .card-header {
      position: relative;
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      text-align: center;
      padding: 10px 0;
      margin-bottom: 0;
      /* Ensure the jumbotron doesn't add extra space */
    }

    .table-row {
      background-color: rgba(0, 0, 0, 0.2);
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
      <a class="navbar-brand" href="#">Admin Dashboard</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="usermanagement.php">User Management</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="portfoliomanagement.php">Portfolio Management</a>
          </li>
          <li class="nav-item active">
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
      <h1 class="display-4">View Messages</h1>
      <p class="lead">Review and manage user messages.</p>
    </div>
  </header>

  <!-- Content Section -->
  <section class="container content-section">
    <!-- Toggle button for filtering messages -->
    <div class="btn-group mb-4">
      <a href="?status=pending" class="btn btn-<?php echo ($statusFilter == 'Pending') ? 'primary' : 'light'; ?>">Pending</a>
      <a href="?status=completed" class="btn btn-<?php echo ($statusFilter == 'Completed') ? 'primary' : 'light'; ?>">Completed</a>
    </div>

    <!-- Display messages in a table -->
    <?php if ($result->num_rows > 0) : ?>
      <div class="container-fluid">
        <div class="row">
          <div class="card" style="width: 100%;">
            <div class="card-header">
              <h5 class="card-title">View Messages</h5>
            </div>
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th>Enquiry ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr class='table-row'>
                      <td><?php echo htmlspecialchars($row['EnquiryID']); ?></td>
                      <td><?php echo htmlspecialchars($row['Name']); ?></td>
                      <td><?php echo htmlspecialchars($row['Email']); ?></td>
                      <td><?php echo htmlspecialchars($row['Phone']); ?></td>
                      <td>
                        <!-- View More button -->
                        <a href="messagedetails.php?EnquiryID=<?php echo htmlspecialchars($row['EnquiryID']); ?>" class='btn btn-sm btn-primary'">View More</a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
          </div>
        </div>
      </div>
    <?php else : ?>
      <p class="text-center" style="color: #ffffff;">No messages found.</p>
    <?php endif; ?>

    <?php
    // Free the result set
    if ($result) {
      $result->free();
    }
    ?>
  </section>

  <!-- Footer Section -->
  <footer>
    <div class=" container">
                          &copy; 2024 Malcolm Lismore Photography. All Rights Reserved.
            </div>
            </footer>

            <!-- Bootstrap JS and jQuery -->
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>