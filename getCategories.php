<?php
// Include database connection code (modify as per your configuration)
require_once 'dbcon.php';

// Fetch all dataset categories from the Database
$getAllCategoriesQuery = "SELECT CategoryName FROM DatasetCategories";
$categories = [];

try {
    $stmt = $conn->query($getAllCategoriesQuery);

    // Fetch all rows from the result set
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row;
    }

    // Send a JSON response with the dataset categories
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($categories);
} catch (PDOException $e) {
    // Handle database error
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
