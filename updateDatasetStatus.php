<?php
require_once 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['datasetId']) && isset($_POST['status'])) {
        $datasetId = $_POST['datasetId'];
        $status = $_POST['status'];

        $sql = "UPDATE Datasets SET ApprovalStatus = :status WHERE DatasetID = :datasetId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':datasetId', $datasetId, PDO::PARAM_INT);

        try {
            $stmt->execute();
            echo json_encode(['success' => 'Dataset status updated successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error updating dataset status: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Missing datasetId or status']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method. Use POST.']);
}
?>
