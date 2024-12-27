<?php
require_once '../../config/config.php';

// Уникаємо дублювання сесії
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

header('Content-Type: application/json');

try {
  // Перевірка авторизації користувача
  if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
  }

  // Перевірка підключення до бази даних
  if (!$connection) {
    throw new Exception("Не вдалося підключитися до бази даних");
  }

  // Виконання SQL-запиту для підрахунку нових сповіщень
  $query = "SELECT COUNT(*) AS new_notifications FROM notifications WHERE is_seen = 0";
  $result = $connection->query($query);

  if (!$result) {
    throw new Exception("Помилка виконання запиту: " . $connection->error);
  }

  $row = $result->fetch_assoc();
  echo json_encode([
    'status' => 'success',
    'new_notifications' => (int)$row['new_notifications']
  ]);
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
