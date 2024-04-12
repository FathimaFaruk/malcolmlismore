<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="icon" type="image/x-icon" href="/images/Camera_Moto_30013.ico">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-image: url('/images/stock/outdoor-photography.png');
      background-size: cover;
      background-repeat: no-repeat;
      min-height: 100vh;
      overflow-x: hidden;
      /* Hide horizontal scrollbar */
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

    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      /* Ensure the navbar appears above other content */
    }

    footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 20px 0;
      position: fixed;
      bottom: 0;
      width: 100%;
    }

    .center-content {
      padding-top: 100px;
      /* Adjust based on the height of the navbar */
      padding-bottom: 70px;
      /* Adjust based on the height of the footer */
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .jumbotron {
      position: relative;
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      text-align: center;
      padding: 100px 0;
      margin-bottom: 0;
      /* Ensure the jumbotron doesn't add extra space */
    }

    .jumbotron h1,
    .jumbotron p {
      margin-bottom: 0;
      position: relative;
      z-index: 1;
      /* Ensure the text appears above the background */
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
    }

    .image-container img {
      width: 100%;
      height: auto;
      display: block;
    }

    .image-container:hover {
      transform: scale(1.1);
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

    /* Glass effect for the submit button */
    .btn-secondary,
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

    .btn-secondary,
    .btn-primary span {
      position: relative;
      z-index: 1;
    }
  </style>
</head>

<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">Admin Dashboard</a>
      <a class="navbar-brand" href="../index.php">Home Page</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="/admin/usermanagement.php">User Management</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/portfoliomanagement.php">Portfolio Management</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/messageview.php">Messages</a>
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
    <div class="container">
      <h1 class="display-4">Welcome to Admin Dashboard</h1>
      <p class="lead">Manage your portfolio and user accounts.</p>
    </div>
  </header>

  <!-- Page Content -->
  <div class="center-content">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="image-container">
            <img src="/images/stock/image.png" alt="User Managment">
            <div class="image-caption">
              <h5 class="card-title">User Management</h5>
              <p class="card-text">Manage users and their roles.</p>
              <a href="/admin/usermanagement.php" class="btn btn-primary">Go to User Management</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="image-container">
            <img src="/images/stock/image2.png" alt="Portfolio Managment">
            <div class="image-caption">
              <h5 class="card-title">Portfolio Management</h5>
              <p class="card-text">Manage portfolio items.</p>
              <a href="/admin/portfoliomanagement.php" class="btn btn-primary">Go to Portfolio Management</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="image-container">
            <img src="/images/stock/image3.png" alt="Messages">
            <div class="image-caption">
              <h5 class="card-title">Messages</h5>
              <p class="card-text">View and manage messages.</p>
              <a href="/admin/messageview.php" class="btn btn-primary">Go to Messages</a>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-md-12 text-center">
          <a href="../index.php" class="btn btn-secondary">Go back to Home Page</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Section -->
  <footer>
    <div class="container">
      &copy; 2024 Malcolm Lismore Photography. All Rights Reserved.
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>