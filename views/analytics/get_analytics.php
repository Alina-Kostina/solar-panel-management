<?php
// Include configuration and controller
require_once '../../config/config.php';
require_once '../../controllers/AnalyticsController.php';

// Instantiate AnalyticsController
$analyticsController = new AnalyticsController($connection);

// Fetch analytics data
$analytics = $analyticsController->getAllAnalytics();  // Use getAllAnalytics instead of generateAnalytics

// Output data as JSON
header('Content-Type: application/json');
echo json_encode($analytics);
?>
