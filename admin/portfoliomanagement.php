<?php
session_start(); // Start session if not already started

// Include database configuration
include '../db.php';

// Function to delete category
function deleteCategory($catid, $conn)
{
    // Use a prepared statement to delete the category
    $sql = "DELETE FROM category WHERE catid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $catid);

    // Check if the statement was executed successfully
    if ($stmt->execute()) {
        return true; // Deletion successful
    } else {
        return false; // Deletion failed
    }
}


// Handle category deletion if requested
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['catid'])) {
    $catid = intval($_GET['catid']); // Convert to integer
    if (deleteCategory($catid, $conn)) {
        // If deletion is successful, redirect to the same page without the deletion parameters
        header("Location: portfoliomanagement.php");
        exit;
    } else {
        echo "Failed to delete category.";
    }
}

// Function to delete a portfolio
function deletePortfolio($photoID, $conn)
{
    // Use a prepared statement to delete the portfolio
    $sql = "DELETE FROM portfolio WHERE PhotoID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $photoID);

    // Check if the statement was executed successfully
    if ($stmt->execute()) {
        return true; // Deletion successful
    } else {
        return false; // Deletion failed
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['photoID'])) {
    $photoID = intval($_GET['photoID']); // Convert to integer
    if (deletePortfolio($photoID, $conn)) {
        // If deletion is successful, redirect to the same page without the deletion parameters
        header("Location: portfoliomanagement.php");
        exit;
    } else {
        echo "Failed to delete portfolio.";
    }
}


// Fetch categories
$categories = [];
$sql = "SELECT * FROM category";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch users
$users = [];
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[$row['UserID']] = $row['Username']; // Store usernames in an associative array
    }
}

// Fetch portfolios with user and category names
$portfolios = [];
$sql = "SELECT p.*, u.Username AS UserName, c.catname AS CategoryName 
        FROM portfolio p 
        INNER JOIN users u ON p.UserID = u.UserID 
        INNER JOIN category c ON p.Catid = c.catid";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $portfolios[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Management - Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="/images/Camera_Moto_30013.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            background-image: url('/images/stock/outdoor-photography.png');
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        .navbar {
            background-color: #343a40;
            color: white;
        }

        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
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

        .portfolio-image {
            max-width: 150px;
            max-height: 150px;
        }
    </style>
</head>

<body>

    <!-- First Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/admin/admindashboard.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/usermanagement.php">User Management</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Portfolio Management</a>
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

    <!-- First Table (Categories) -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Categories</h5>
                    </div>
                    <div class="card-body">
                        <!-- Add search input for categories -->
                        <input type="text" class="form-control mb-3" id="categorySearch" placeholder="Search Categories...">
                        <table class="table" id="categoryTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category) : ?>
                                    <tr class='table-row'>
                                        <td><?php echo $category['catid']; ?></td>
                                        <td><?php echo $category['catname']; ?></td>
                                        <td><?php echo $category['price'] . " £"; ?></td>
                                        <td><?php echo $category['catdesc']; ?></td>
                                        <td>
                                            <!-- Edit button with a link to the editcategory.php page with the category ID as a URL parameter -->
                                            <a class='btn btn-sm btn-primary' href="editcategory.php?catid=<?php echo $category['catid']; ?>">Edit</a>

                                            <!-- Remove button with an event handler that calls a function to confirm deletion -->
                                            <button class='btn btn-sm btn-danger' onclick='confirmDeleteCategory(<?php echo $category["catid"]; ?>)'>Remove</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="/admin/addcategory.php" class="btn btn-primary">Add Category</a>
                    </div>
                </div>
            </div>
            <!-- Second Table (Portfolios) -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Portfolios</h5>
                    </div>
                    <div class="card-body">
                        <!-- Add search input for portfolios -->
                        <input type="text" class="form-control mb-3" id="portfolioSearch" placeholder="Search Portfolios...">
                        <table class="table" id="portfolioTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Category</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($portfolios as $portfolio) : ?>
                                    <tr class='table-row'>
                                        <td><?php echo $portfolio['PhotoID']; ?></td>
                                        <td><?php echo $portfolio['UserName']; ?></td>
                                        <td><?php echo $portfolio['CategoryName']; ?></td>
                                        <td><?php echo $portfolio['Title']; ?></td>
                                        <!-- Display image -->
                                        <td><img class="portfolio-image" src="<?php echo $portfolio['ImagePath']; ?>" alt="Portfolio Image" class="portfolio-image"></td>
                                        <td>
                                            <a class='btn btn-sm btn-primary' href="editportfolio.php?PhotoID=<?php echo $portfolio["PhotoID"]; ?>">Edit</a>
                                            <!-- Display remove button -->
                                            <button class='btn btn-sm btn-danger' onclick='confirmDeletePortfolio(<?php echo $portfolio["PhotoID"]; ?>)'>Remove</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="/admin/addportfolio.php" class="btn btn-primary">Add Portfolio</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="container">
            &copy; 2024 Admin Dashboard. All Rights Reserved.
        </div>
    </footer>

    <!-- Include necessary scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Function to filter table rows based on search input for categories
        document.getElementById("categorySearch").addEventListener("input", function() {
            filterTable(this.value, "categoryTable");
        });

        // Function to filter table rows based on search input for portfolios
        document.getElementById("portfolioSearch").addEventListener("input", function() {
            filterTable(this.value, "portfolioTable");
        });

        function filterTable(searchTerm, tableId) {
            const rows = document.querySelectorAll("#" + tableId + " tbody tr");
            searchTerm = searchTerm.toLowerCase();
            rows.forEach(row => {
                let cellIndex = 1; // Start from 1 to skip the ID column
                let found = false;
                row.querySelectorAll("td").forEach(cell => {
                    const cellText = cell.textContent.toLowerCase();
                    if (cellText.includes(searchTerm)) {
                        found = true;
                    }
                });
                if (found) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        // Function to confirm deletion of a category
        function confirmDeleteCategory(catid) {
            // Show a confirmation dialog
            if (confirm("Are you sure you want to delete this category?")) {
                // If the user confirms, redirect to the deletion URL
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?action=delete&catid=" + catid;
            }
        }
        // Function to confirm deletion of a portfolio
        function confirmDeletePortfolio(photoID) {
            // Show a confirmation dialog
            if (confirm("Are you sure you want to delete this portfolio?")) {
                // If the user confirms, redirect to the deletion URL
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?action=delete&photoID=" + photoID;
            }
        }
    </script>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>