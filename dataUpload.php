<?php
session_start(); 
require_once 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get other dataset details from the request
    $userID = isset($_POST['userID']) ? $_POST['userID'] : null;
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $uploadDate = date('Y-m-d H:i:s'); // Current date and time
    $approvalStatus = 'Pending'; // Assuming new datasets are pending approval
    $category = isset($_POST['category']) ? $_POST['category'] : null;
    if (isset($_SESSION['userID'])) {
        $userID = $_SESSION['userID'];

        // Your code to handle the upload
        // Use $userID in your database queries

    }
    else {
        $userID = "admin";
    }
    // Check if any required parameter is null
    if ($userID === null || $title === null || $description === null || $category === null) {
        echo "Invalid request. Missing required parameters.";
        exit;
    }

    // Process file upload
    $uploadDir = 'DataSet/'; // Specify your upload directory
    $uploadFilePath = $uploadDir . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath)) {
        // Insert dataset information into the database
        $sql = "INSERT INTO Datasets (UserID, Title, Description, UploadDate, ApprovalStatus, Category, FilePath)
                VALUES (:userID, :title, :description, :uploadDate, :approvalStatus, :category, :filePath)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':uploadDate', $uploadDate);
        $stmt->bindParam(':approvalStatus', $approvalStatus);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':filePath', $uploadFilePath);

        try {
            $stmt->execute();
            echo "Dataset uploaded successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "File upload failed";
    }
} else {
    echo "Invalid request method. Use POST.";
}
?>
