<?php
require_once('../config/database.php');

// Check if service_id is provided
if (!isset($_GET['service_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Service ID is required']);
    exit();
}

$service_id = intval($_GET['service_id']);

// Fetch sub-services for the given service
$query = "SELECT id, name FROM sub_services WHERE service_id = ? ORDER BY name";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

$sub_services = [];
while ($row = $result->fetch_assoc()) {
    $sub_services[] = [
        'id' => $row['id'],
        'name' => $row['name']
    ];
}

// Return the sub-services as JSON
header('Content-Type: application/json');
echo json_encode($sub_services);
?> 