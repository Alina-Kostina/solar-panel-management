<?php
require_once '../../config/config.php';
require_once '../../controllers/MetricController.php';

$metricController = new MetricController($connection);

// Перевіряємо, чи потрібно зберегти дані в базу
$saveToDb = isset($_GET['save']) && $_GET['save'] == '1';

// Отримуємо показники з бази або оновлюємо, якщо активний режим
$metrics = $metricController->getMetricsForRealTime($saveToDb);

// Виводимо дані у форматі JSON
echo json_encode($metrics);
