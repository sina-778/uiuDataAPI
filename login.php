<?php
require_once 'dbcon.php';
session_unset();
// Retrieve raw input
$input = file_get_contents('php://input');

// Decode JSON data
$data = json_decode($input, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user login data from the decoded JSON
    $userID = isset($data['userID']) ? $data['userID'] : null;
    $password = isset($data['password']) ? $data['password'] : null;

    // Check if any required parameter is null
    if ($userID === null || $password === null) {
        // Invalid request. Missing required parameters.
        http_response_code(400);
        echo json_encode(["error" => "Invalid request. Missing required parameters."]);
        exit;
    }

    // Retrieve user data from the database
    $getUserQuery = "SELECT UserID, Password, UserRole FROM Users WHERE UserID = :userID";
    $getUserStmt = $conn->prepare($getUserQuery);
    $getUserStmt->bindParam(':userID', $userID);
    $getUserStmt->execute();
    $user = $getUserStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // User not found
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
        exit;
    }

    // Verify the password
    if (password_verify($password, $user['Password'])) {
        session_start(); 
        $_SESSION['userID'] = $user['UserID'];
        // Password is correct, return user information
        $response = [
            "message" => "Login successful",
            "user_id" => $user['UserID'],
            "user_role" => $user['UserRole']
        ];
        http_response_code(200);
        echo json_encode($response);
        exit;
    } else {
        // Password verification failed
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
        exit;
    }
} else {
    // Invalid request method. Use POST.
    http_response_code(400);
    echo json_encode(["error" => "Invalid request method. Use POST."]);
}
?>
