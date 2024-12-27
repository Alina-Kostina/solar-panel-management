<?php
// Підключаємо конфігураційний файл, контролер показників і перевіряємо авторизацію
require_once '../../config/config.php';
require_once '../../controllers/MetricController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Ініціалізація контролера показників
$metricController = new MetricController($connection);

// Перевірка, чи передано ID показника для видалення
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $metric_id = intval($_GET['id']);

  // Видалення показника через контролер
  if ($metricController->deleteMetric($metric_id)) {
    $_SESSION['message'] = 'Показник успішно видалено.';
  } else {
    $_SESSION['message'] = 'Помилка при видаленні показника. Спробуйте ще раз.';
  }
} else {
  $_SESSION['message'] = 'ID показника не вказано або невірне.';
}

// Перенаправлення на сторінку з показниками після видалення
header('Location: view_metrics.php');
exit;
