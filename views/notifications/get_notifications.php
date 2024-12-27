<?php
require_once '../../config/config.php';
require_once '../../controllers/NotificationController.php';

header('Content-Type: application/json');
$query = "SELECT n.*, p.panel_name FROM notifications n 
          LEFT JOIN solar_panels p ON n.panel_id = p.panel_id
          ORDER BY n.notification_date DESC";
$result = $connection->query($query);

$notifications = [];
while ($row = $result->fetch_assoc()) {
  $notifications[] = $row;
}

echo json_encode(['status' => 'success', 'notifications' => $notifications]);
?>
