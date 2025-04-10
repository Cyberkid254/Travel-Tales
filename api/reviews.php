<?php
session_start();
require_once "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["error" => "Please login to manage reviews"]);
    exit();
}

function validateReview($data) {
    $errors = [];
    if (!isset($data["destination_id"])) {
        $errors[] = "Destination ID is required";
    }
    if (!isset($data["rating"]) || $data["rating"] < 1 || $data["rating"] > 5) {
        $errors[] = "Rating must be between 1 and 5";
    }
    if (!isset($data["comment"]) || empty(trim($data["comment"]))) {
        $errors[] = "Comment is required";
    }
    return $errors;
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        // Create review
        $data = json_decode(file_get_contents("php://input"), true);
        $errors = validateReview($data);
        
        if (!empty($errors)) {
            echo json_encode(["error" => $errors]);
            exit();
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO reviews (user_id, destination_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION["id"],
                $data["destination_id"],
                $data["rating"],
                trim($data["comment"])
            ]);
            
            echo json_encode([
                "success" => true,
                "message" => "Review added successfully",
                "review_id" => $pdo->lastInsertId()
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        }
        break;
        
    case "GET":
        // Get reviews for a destination
        $destination_id = isset($_GET["destination_id"]) ? $_GET["destination_id"] : null;
        
        try {
            $sql = "SELECT r.*, u.username 
                   FROM reviews r 
                   JOIN users u ON r.user_id = u.id 
                   WHERE r.destination_id = ? 
                   ORDER BY r.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$destination_id]);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "success" => true,
                "reviews" => $reviews
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        }
        break;
        
    case "PUT":
        // Update review
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["review_id"])) {
            echo json_encode(["error" => "Review ID is required"]);
            exit();
        }
        
        try {
            // Check if user owns the review
            $stmt = $pdo->prepare("SELECT user_id FROM reviews WHERE id = ?");
            $stmt->execute([$data["review_id"]]);
            $review = $stmt->fetch();
            
            if (!$review || $review["user_id"] != $_SESSION["id"]) {
                echo json_encode(["error" => "Unauthorized to edit this review"]);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE id = ?");
            $stmt->execute([
                $data["rating"],
                trim($data["comment"]),
                $data["review_id"]
            ]);
            
            echo json_encode([
                "success" => true,
                "message" => "Review updated successfully"
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        }
        break;
        
    case "DELETE":
        // Delete review
        $review_id = isset($_GET["review_id"]) ? $_GET["review_id"] : null;
        
        try {
            // Check if user owns the review
            $stmt = $pdo->prepare("SELECT user_id FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            $review = $stmt->fetch();
            
            if (!$review || $review["user_id"] != $_SESSION["id"]) {
                echo json_encode(["error" => "Unauthorized to delete this review"]);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            
            echo json_encode([
                "success" => true,
                "message" => "Review deleted successfully"
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        }
        break;
}
?> 