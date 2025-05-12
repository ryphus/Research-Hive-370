<?php
// ekhane database er shathe connect korse
include 'db_connect.php';

// session start korlam
session_start();

// log in naki ta check 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


$error = "";
$success = "";

// user_id  oflogged-in user , fetch kora
$username = $_SESSION['username'];
$sql_user = "SELECT user_id FROM users WHERE user_name = '$username'";
$result_user = $conn->query($sql_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $user_id = $user['user_id'];

    // upload
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $tags = mysqli_real_escape_string($conn, $_POST['tags']);
        $file = $_FILES['file'];

        // file upload er kaj gula 
        
        $upload_dir = __DIR__ . "/uploads/"; // uploads directory er absolute path
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // directory na thakle create kora
        }
        $file_path = $upload_dir . basename($file["name"]);

        if (move_uploaded_file($file["tmp_name"], $file_path)) {
            // database er relative path 
            $relative_file_path = "uploads/" . basename($file["name"]);
            $sql = "INSERT INTO repository (user_id, title, description, tags, file_path) VALUES 
                    ($user_id, '$title', '$description', '$tags', '$relative_file_path')";
            if ($conn->query($sql) === TRUE) {
                $success = "File uploaded successfully!";
            } else {
                $error = "Error saving file details: " . $conn->error;
            }
        } else {
            $error = "Error uploading file.";
        }
    }

    // file deletion
    if (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $sql_get_file = "SELECT file_path FROM repository WHERE id = $delete_id AND user_id = $user_id";
        $result_get_file = $conn->query($sql_get_file);

        if ($result_get_file && $result_get_file->num_rows > 0) {
            $file = $result_get_file->fetch_assoc();
            $file_path = $file['file_path'];

            // ekdom server theke file deletion
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // databse er theke file record delete
            $sql_delete = "DELETE FROM repository WHERE id = $delete_id AND user_id = $user_id";
            if ($conn->query($sql_delete) === TRUE) {
                $success = "File deleted successfully!";
            } else {
                $error = "Error deleting file: " . $conn->error;
            }
        } else {
            $error = "File not found or you don't have permission to delete it.";
        }
    }
} else {
    $error = "User not found.";
}

// database er theke files fetch kora
$sql_files = "SELECT * FROM repository WHERE user_id = $user_id ORDER BY id DESC";
$result_files = $conn->query($sql_files);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repository - Research Hive</title>
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
            position: relative;
        }

        header h1 {
            margin: 0;
            text-align: center;
        }

        .dashboard-button {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .dashboard-button:hover {
            background-color: #1e7e34;
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

        form input, form textarea, form button {
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

        .file-list {
            margin-top: 20px;
        }

        .file-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .file-list th, .file-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .file-list th {
            background-color: #007bff;
            color: #fff;
        }

        .file-list a {
            color: #007bff;
            text-decoration: none;
        }

        .file-list a:hover {
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
    </style>
</head>
<body>
    <header>
        <h1>Repository</h1>
        <a href="dashboard.php" class="dashboard-button">Dashboard</a>
    </header>
    <div class="container">
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="File Title" required>
            <textarea name="description" placeholder="File Description" rows="4"></textarea>
            <input type="text" name="tags" placeholder="Tags (comma-separated)">
            <input type="file" name="file" required>
            <button type="submit">Upload File</button>
        </form>
        <div class="file-list">
            <h2>Uploaded Files</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Tags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_files && $result_files->num_rows > 0): ?>
                        <?php while ($file = $result_files->fetch_assoc()): ?>
                            <tr>
                                <td><a href="<?php echo $file['file_path']; ?>" target="_blank"><?php echo $file['title']; ?></a></td>
                                <td><?php echo $file['description']; ?></td>
                                <td><?php echo $file['tags']; ?></td>
                                <td>
                                    <a href="<?php echo $file['file_path']; ?>" download>Download</a> |
                                    <a href="?delete_id=<?php echo $file['id']; ?>" onclick="return confirm('Are you sure you want to delete this file?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No files uploaded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>