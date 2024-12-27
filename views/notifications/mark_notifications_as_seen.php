<?php
require_once '../../config/config.php';
require_once '../../controllers/NotificationController.php';

$notificationController = new NotificationController($connection);
$notificationController->markNotificationsAsSeen();
?>
