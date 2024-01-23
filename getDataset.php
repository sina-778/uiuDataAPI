<?php
require_once 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT * FROM Datasets";
    $params = array();

    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $sql .= " WHERE Category = :category";
        $params[':category'] = $_GET['category'];
    }

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = "%" . $_GET['search'] . "%";
        $sql .= (empty($params) ? " WHERE" : " AND") . " (Title LIKE :searchTerm OR Description LIKE :searchTerm)";
        $params[':searchTerm'] = $searchTerm;
    }

    if (isset($_GET['approvalStatus']) && !empty($_GET['approvalStatus'])) {
        $approvalStatus = $_GET['approvalStatus'];
        $sql .= (empty($params) ? " WHERE" : " AND") . " ApprovalStatus = :approvalStatus";
        $params[':approvalStatus'] = $approvalStatus;
    }

    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute($params);
        $datasets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($datasets);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error fetching datasets: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method. Use GET.']);
}
?>
