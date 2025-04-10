<?php
session_start();
require_once "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["error" => "Please login to access profile"]);
    exit();
}

function validateProfileData($data) {
    $errors = [];
    if (isset($data["email"]) && !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (isset($data["new_password"]) && strlen($data["new_password"]) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    return $errors;
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        // Get user profile
        try {
            $stmt = $pdo->prepare("
                SELECT u.id, u.username, u.email, u.created_at,
                       COUNT(DISTINCT b.id) as total_bookings,
                       COUNT(DISTINCT r.id) as total_reviews
                FROM users u
                LEFT JOIN bookings b ON u.id = b.user_id
                LEFT JOIN reviews r ON u.id = r.user_id
                WHERE u.id = ?
                GROUP BY u.id
            ");
            $stmt->execute([$_SESSION["id"]]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get recent bookings
            $stmt = $pdo->prepare("
                SELECT b.*, d.name as destination_name
                FROM bookings b
                JOIN destinations d ON b.destination_id = d.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$_SESSION["id"]]);
            $recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get recent reviews
            $stmt = $pdo->prepare("
                SELECT r.*, d.name as destination_name
                FROM reviews r
                JOIN destinations d ON r.destination_id = d.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$_SESSION["id"]]);
            $recent_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "success" => true,
                "profile" => $profile,
                "recent_bookings" => $recent_bookings,
                "recent_reviews" => $recent_reviews
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        }
        break;
        
    case "PUT":
        // Update profile
        $data = json_decode(file_get_contents("php://input"), true);
        $errors = validateProfileData($data);
        
        if (!empty($errors)) {
            echo json_encode(["error" => $errors]);
            exit();
        }
        
        try {
            // Update email if provided
            if (isset($data["email"])) {
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([trim($data["email"]), $_SESSION["id"]]);
            }
            
            // Update password if provided
            if (isset($data["new_password"])) {
                if (!isset($data["current_password"])) {
                    echo json_encode(["error" => "Current password is required"]);
                    exit();
                }
                
                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$_SESSION["id"]]);
                $user = $stmt->fetch();
                
                if (!password_verify($data["current_password"], $user["password"])) {
                    echo json_encode(["error" => "Current password is incorrect"]);
                    exit();
                }
                
                // Update password
                $new_password_hash = password_hash($data["new_password"], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$new_password_hash, $_SESSION["id"]]);
            }
            
            echo json_encode([
                "success" => true,
                "message" => "Profile updated successfully"
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        }
        break;
}
?> 