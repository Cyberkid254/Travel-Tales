<?php
header("Content-Type: application/json");

$guides = [
    ["name" => "Amalfi Coast", "description" => "Breathtaking coastal views, charming towns, and delicious cuisine."],
    ["name" => "Rome", "description" => "Explore ancient ruins, Vatican City, and vibrant street life."],
    ["name" => "Venice", "description" => "Romantic canals, historic architecture, and unique culture."],
];

echo json_encode($guides);
?>
