// db.php - Database connection
<?php
$host = 'localhost';
$dbname = 'travel_db';
$username = 'root'; // Change if needed
$password = ''; // Change if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

// get_destinations.php - Fetch destinations
<?php
include 'db.php';

$sql = "SELECT * FROM destinations";
$stmt = $pdo->query($sql);
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($destinations);
?>

// add_destination.php - Add new destination
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    
    $sql = "INSERT INTO destinations (name, description, image_url) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $description, $image_url]);
    
    echo json_encode(['message' => 'Destination added successfully']);
}
?>

/* destinations.sql - Database Schema
CREATE DATABASE travel_db;
USE travel_db;

CREATE TABLE destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(512) NOT NULL
);
*/
