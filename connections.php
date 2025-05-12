<?php
// ekhane database er shathe connect korse
include 'db_connect.php';

// Start a session to manage user login
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

// Initialize variables
$error = "";
$success = "";

// Fetch the user_id of the logged-in user
$username = $_SESSION['username'];
$sql_user = "SELECT user_id FROM users WHERE user_name = '$username'";
$result_user = $conn->query($sql_user);

if ($result_user && $result_user->num_rows > 0) {
	$user = $result_user->fetch_assoc();
	$user_id = $user['user_id'];

	// Handle connection request
	if (isset($_GET['connect_user_id'])) {
    	$connect_user_id = intval($_GET['connect_user_id']);

    	// Check if the connection already exists
    	$sql_check_connection = "SELECT * FROM connections WHERE user_id = $user_id AND connected_user_id = $connect_user_id";
    	$result_check_connection = $conn->query($sql_check_connection);

    	if ($result_check_connection->num_rows == 0) {
        	// Insert the connection
        	$sql_connect = "INSERT INTO connections (user_id, connected_user_id) VALUES ($user_id, $connect_user_id)";
        	if ($conn->query($sql_connect) === TRUE) {
            	$success = "Connection added successfully!";
        	} else {
            	$error = "Error adding connection: " . $conn->error;
        	}
    	} else {
        	$error = "You are already connected with this researcher.";
    	}
	}

	// Handle connection removal
	if (isset($_GET['remove_user_id'])) {
    	$remove_user_id = intval($_GET['remove_user_id']);

    	// Delete the connection
    	$sql_remove_connection = "DELETE FROM connections 
                                  WHERE (user_id = $user_id AND connected_user_id = $remove_user_id) 
                                     OR (user_id = $remove_user_id AND connected_user_id = $user_id)";
    	if ($conn->query($sql_remove_connection) === TRUE) {
        	$success = "Connection removed successfully!";
    	} else {
        	$error = "Error removing connection: " . $conn->error;
    	}
	}

	// Handle search
	$search_results = [];
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    	$search_query = mysqli_real_escape_string($conn, $_POST['search_query']);
    	$sql_search = "SELECT user_id, user_name FROM users WHERE user_name LIKE '%$search_query%' AND user_id != $user_id";
    	$result_search = $conn->query($sql_search);

    	if ($result_search && $result_search->num_rows > 0) {
        	$search_results = $result_search->fetch_all(MYSQLI_ASSOC);
    	} else {
        	$error = "No researchers found.";
    	}
	}

	// Fetch connected researchers
	$sql_connections = "SELECT users.user_id, users.user_name FROM connections
                    	JOIN users ON connections.connected_user_id = users.user_id
                    	WHERE connections.user_id = $user_id";
	$result_connections = $conn->query($sql_connections);
} else {
	$error = "User not found.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Connections - Research Hive</title>
	<style>
    	body {
        	font-family: 'Arial', sans-serif;
        	background: #f9f9f9;
        	margin: 0;
        	padding: 0;
        	color: #333;
    	}

    	header {
        	background-color: #007bff;
        	color: #fff;
        	padding: 10px 20px;
        	text-align: center;
    	}

    	.container {
        	max-width: 800px;
        	margin: 20px auto;
        	padding: 20px;
        	background-color: #fff;
        	border-radius: 8px;
        	box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    	}

    	h1 {
        	text-align: center;
        	margin-bottom: 20px;
    	}

    	form {
        	margin-bottom: 30px;
    	}

    	form input, form button {
        	width: 100%;
        	margin-bottom: 15px;
        	padding: 10px;
        	font-size: 16px;
        	border: 1px solid #ddd;
        	border-radius: 5px;
    	}

    	form button {
        	background-color: #007bff;
        	color: #fff;
        	border: none;
        	cursor: pointer;
    	}

    	form button:hover {
        	background-color: #0056b3;
    	}

    	.list {
        	margin-top: 20px;
    	}

    	.list-item {
        	border: 1px solid #ddd;
        	padding: 15px;
        	margin-bottom: 10px;
        	border-radius: 5px;
        	display: flex;
        	justify-content: space-between;
        	align-items: center;
    	}

    	.list-item a {
        	color: #007bff;
        	text-decoration: none;
    	}

    	.list-item a:hover {
        	text-decoration: underline;
    	}

    	.error {
        	color: red;
        	margin-bottom: 10px;
    	}

    	.success {
        	color: green;
        	margin-bottom: 10px;
    	}

    	.home-button {
        	display: inline-block;
        	margin-top: 20px;
        	padding: 10px 20px;
        	background-color: #28a745;
        	color: #fff;
        	text-decoration: none;
        	border-radius: 5px;
    	}

    	.home-button:hover {
        	background-color: #1e7e34;
    	}
	</style>
</head>
<body>
	<header>
    	<h1>Connections</h1>
	</header>
	<div class="container">
    	<?php if ($error): ?>
        	<div class="error"><?php echo $error; ?></div>
    	<?php endif; ?>
    	<?php if ($success): ?>
        	<div class="success"><?php echo $success; ?></div>
    	<?php endif; ?>
    	<form method="POST">
        	<input type="text" name="search_query" placeholder="Search researchers by username..." required>
        	<button type="submit" name="search">Search</button>
    	</form>
    	<div class="list">
        	<h2>Search Results</h2>
        	<?php if (!empty($search_results)): ?>
            	<?php foreach ($search_results as $researcher): ?>
                	<div class="list-item">
                    	<span><?php echo $researcher['user_name']; ?></span>
                    	<a href="?connect_user_id=<?php echo $researcher['user_id']; ?>">Connect</a>
                	</div>
            	<?php endforeach; ?>
        	<?php else: ?>
            	<p>No researchers found.</p>
        	<?php endif; ?>
    	</div>
    	<div class="list">
        	<h2>Your Connections</h2>
        	<?php if ($result_connections && $result_connections->num_rows > 0): ?>
            	<?php while ($connection = $result_connections->fetch_assoc()): ?>
                	<div class="list-item">
                    	<span>
                        	<a href="profile.php?user_id=<?php echo $connection['user_id']; ?>">
                            	<?php echo $connection['user_name']; ?>
                        	</a>
                    	</span>
                    	<a href="?remove_user_id=<?php echo $connection['user_id']; ?>" 
                       		onclick="return confirm('Are you sure you want to remove this connection?');" 
                       		style="color: red;">Remove</a>
                	</div>
            	<?php endwhile; ?>
        	<?php else: ?>
            	<p>You have no connections yet.</p>
        	<?php endif; ?>
    	</div>
    	<a href="dashboard.php" class="home-button">Dashboard</a>
	</div>
</body>
</html>
