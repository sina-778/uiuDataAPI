<?php
require_once 'dbcon.php';

// Log a message for debugging
error_log("Script is running");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get dataset ID from the query parameters
    $datasetID = isset($_GET['datasetID']) ? $_GET['datasetID'] : null;

    // Check if the dataset ID is provided
    if ($datasetID === null) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid request. Missing dataset ID."]);
        exit;
    }

    // Log a message for debugging
    error_log("Downloading dataset with ID: $datasetID");

    // Retrieve file path from the Datasets table
    $query = "SELECT FilePath FROM Datasets WHERE DatasetID = :datasetID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':datasetID', $datasetID);
    $stmt->execute();

    // Check if the dataset exists
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Dataset not found."]);
        exit;
    }

    // Fetch the file path
    $filePath = $stmt->fetchColumn();

    // Check if the file exists
    if (file_exists($filePath)) {
        // Set appropriate headers for file download
        header("Cache-Control: public");
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: binary/octet-stream");
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filePath));
        header('Content-Length: ' . filesize($filePath));

        // Output the file content
        readfile($filePath);
        echo json_encode(["error" => "Dataset not found."]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "File not found."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request method. Use GET."]);
}
?>
