<?php
// Підключаємо конфігураційний файл для з'єднання з базою даних
require_once '../../config/config.php';
require_once '../../controllers/NotificationController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Перевірка, чи передано ID сповіщення через параметр URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  echo json_encode(['status' => 'error', 'message' => 'ID сповіщення не вказано']);
  exit;
}

$notification_id = intval($_GET['id']); // Отримуємо ID сповіщення

// Ініціалізація контролера сповіщень
$notificationController = new NotificationController($connection);

// Видаляємо сповіщення
if ($notificationController->deleteNotification($notification_id)) {
  echo json_encode(['status' => 'success', 'message' => 'Сповіщення успішно видалено']);

  header('Location: view_notifications.php');
  exit;
} else {
  echo json_encode(['status' => 'error', 'message' => 'Не вдалося видалити сповіщення']);
}
?>
