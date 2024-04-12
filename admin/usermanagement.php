<?php
session_start();
// Include database configuration
include '../db.php';

// Function to delete user
function deleteUser($userID, $conn)
{
    $sql = "DELETE FROM users WHERE UserID = $userID";
    if ($conn->query($sql) === TRUE) {
        return true; // Deletion successful
    } else {
        return false; // Deletion failed
    }
}


// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $userID = $_GET['id'];
    // Check if admin is trying to delete their own account
    if (isset($_SESSION['userID']) && $userID == $_SESSION['userID']) {
        // Redirect to index.php if admin is trying to delete their own account
        header("Location: ../index.php");
        exit();
    }
    if (deleteUser($userID, $conn)) {
        // Redirect back to the same page after successful deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch admin accounts
$adminAccounts = [];
$sql = "SELECT * FROM users WHERE UserType = 'Admin'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $adminAccounts[] = $row;
    }
}

// Fetch regular user accounts
$userAccounts = [];
$sql = "SELECT * FROM users WHERE UserType = 'Regular User'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userAccounts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
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
                        <a class="nav-link" href="#">User Management</a>
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

    <!-- Page Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Admin Accounts</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <input type="text" class="form-control mb-3" id="adminSearch" placeholder="Search...">
                        </form>
                        <table class="table" id="adminTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($adminAccounts as $admin) : ?>
                                    <tr class='table-row'>
                                        <td><?php echo $admin['UserID']; ?></td>
                                        <td><?php echo $admin['Username']; ?></td>
                                        <td><?php echo $admin['Email']; ?></td>
                                        <td>
                                            <a class='btn btn-sm btn-primary' href="editusers.php?id=<?php echo $admin["UserID"]; ?>">Edit</a>
                                            <button class='btn btn-sm btn-danger' onclick='confirmDelete(<?php echo $admin["UserID"]; ?>)'>Remove</button>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-secondary" onclick="resetTable('admin')">Reset</button>
                        <a href="add_admin.php" class="btn btn-primary">Add Admin</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">User Accounts</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <input type="text" class="form-control mb-3" id="userSearch" placeholder="Search...">
                        </form>
                        <table class="table" id="userTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userAccounts as $user) : ?>
                                    <tr class='table-row'>
                                        <td><?php echo $user['UserID']; ?></td>
                                        <td><?php echo $user['Username']; ?></td>
                                        <td><?php echo $user['Email']; ?></td>
                                        <td>
                                            <a href='editusers.php?id=<?php echo $user["UserID"]; ?>' class='btn btn-sm btn-primary'>Edit</a>
                                            <button class='btn btn-sm btn-danger' onclick='confirmDelete(<?php echo $user["UserID"]; ?>)'>Remove</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-secondary" onclick="resetTable('user')">Reset</button>
                        <a href="add_user.php" class="btn btn-primary">Add User</a>
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
        // Function to filter table rows based on search input
        function filterTable(input, table) {
            var filter, tableRows, tableData, i, txtValue;
            filter = input.value.toUpperCase();
            tableRows = table.getElementsByTagName("tr");
            for (i = 0; i < tableRows.length; i++) {
                tableData = tableRows[i].getElementsByTagName("td")[1]; // Assuming username column is the second column
                if (tableData) {
                    txtValue = tableData.textContent || tableData.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tableRows[i].style.display = "";
                    } else {
                        tableRows[i].style.display = "none";
                    }
                }
            }
        }

        // Function to reset table to its original state
        function resetTable(section) {
            if (section === 'admin') {
                document.getElementById("adminSearch").value = "";
                var adminTable = document.getElementById("adminTable");
                var adminRows = adminTable.getElementsByTagName("tr");
                for (var i = 0; i < adminRows.length; i++) {
                    adminRows[i].style.display = "";
                }
            } else if (section === 'user') {
                document.getElementById("userSearch").value = "";
                var userTable = document.getElementById("userTable");
                var userRows = userTable.getElementsByTagName("tr");
                for (var j = 0; j < userRows.length; j++) {
                    userRows[j].style.display = "";
                }
            }
        }

        // Add event listeners to search input fields
        document.getElementById("adminSearch").addEventListener("input", function() {
            filterTable(this, document.getElementById("adminTable"));
        });

        document.getElementById("userSearch").addEventListener("input", function() {
            filterTable(this, document.getElementById("userTable"));
        });

        // Function to confirm deletion
        function confirmDelete(userID) {
            if (confirm("Are you sure you want to delete this user?")) {
                window.location.href = "<?php echo $_SERVER['PHP_SELF'] ?>?action=delete&id=" + userID;
            }
        }
    </script>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>