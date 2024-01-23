<?php
require_once 'dbcon.php';

// Retrieve raw input
$input = file_get_contents('php://input');

// Decode JSON data
$data = json_decode($input, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user registration data from the decoded JSON
    $studentID = isset($data['studentID']) ? $data['studentID'] : null;
    $username = isset($data['username']) ? $data['username'] : null;
    $password = isset($data['password']) ? $data['password'] : null;
    $email = isset($data['email']) ? $data['email'] : null;

    // Check if any required parameter is null
    if ($studentID === null || $username === null || $password === null || $email === null) {
        // Invalid request. Missing required parameters.
        http_response_code(400);
        echo json_encode(["error" => "Invalid request. Missing required parameters."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid email format."]);
        exit;
    }

    // Check if user already exists
    $checkUserQuery = "SELECT COUNT(*) FROM Users WHERE UserID = :studentID OR Email = :email";
    $checkUserStmt = $conn->prepare($checkUserQuery);
    $checkUserStmt->bindParam(':studentID', $studentID);
    $checkUserStmt->bindParam(':email', $email);
    $checkUserStmt->execute();

    if ($checkUserStmt->fetchColumn() > 0) {
        // User already exists
        http_response_code(409);
        echo json_encode(["error" => "User already exists."]);

        exit;
    }

    // Hash the password (use appropriate password hashing methods)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the Users table
    $insertUserQuery = "INSERT INTO Users (UserID, Username, Password, Email, RegistrationDate, UserRole)
                        VALUES (:studentID, :username, :password, :email, GETDATE(), 'User')";

    $insertUserStmt = $conn->prepare($insertUserQuery);

    // Bind parameters
    $insertUserStmt->bindParam(':studentID', $studentID);
    $insertUserStmt->bindParam(':username', $username);
    $insertUserStmt->bindParam(':password', $hashedPassword);
    $insertUserStmt->bindParam(':email', $email);

    try {
        $insertUserStmt->execute();
        // Set session after successful registration
        http_response_code(200);
        echo json_encode(["message" => "Registration successful", "user_id" => $studentID]);
    } catch (PDOException $e) {
        // User registration failed

        http_response_code(500);
        echo json_encode(["error" => "User registration failed:  (" . $stmt->errno . ") " . $stmt->error]);

    }
} else {
    // Invalid request method. Use POST.
    
    http_response_code(400);
    echo json_encode(["error" => "Invalid request method. Use POST."]);

}


?>
