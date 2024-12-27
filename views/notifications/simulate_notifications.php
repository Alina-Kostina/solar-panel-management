<?php
// Підключення конфігурації та необхідних файлів
require_once '../../config/config.php';
require_once '../../controllers/NotificationController.php';

$notificationController = new NotificationController($connection);

// Функція для отримання всіх наявних панелей
function getPanelIds($connection) {
  $panelIds = [];
  $stmt = $connection->prepare("SELECT panel_id FROM solar_panels");
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $panelIds[] = $row['panel_id'];
  }

  $stmt->close();
  return $panelIds;
}

// Можливі повідомлення для сповіщень
$messages = [
  "Температура панелі занадто висока",
  "Потужність нижче допустимого рівня",
  "Інсоляція низька",
  "Виявлено затінення",
  "Чистота панелі потребує обслуговування",
  "Висока вологість навколишнього середовища",
  "Низька ефективність модуля"
];

// Функція для додавання випадкового повідомлення до БД
function insertRandomNotification($panelIds, $messages, $connection) {
  if (empty($panelIds)) {
    echo "Не знайдено жодної панелі у базі даних.\n";
    return;
  }

  $panelId = $panelIds[array_rand($panelIds)];  // Випадковий вибір панелі
  $message = $messages[array_rand($messages)];   // Випадковий вибір повідомлення
  $notificationDate = date("Y-m-d H:i:s");       // Поточна дата і час
  $isResolved = rand(0, 1) ? TRUE : FALSE;       // Випадкове позначення "вирішено" або "невирішено"

  $stmt = $connection->prepare("
        INSERT INTO notifications (panel_id, message, notification_date, is_resolved) 
        VALUES (?, ?, ?, ?)
    ");
  $stmt->bind_param("issi", $panelId, $message, $notificationDate, $isResolved);

  if ($stmt->execute()) {
    echo "Додано сповіщення для панелі ID {$panelId}: {$message}\n";
  } else {
    echo "Не вдалося додати сповіщення.\n";
  }

  $stmt->close();
}

// Отримуємо панелі з бази і додаємо випадкове повідомлення
$panelIds = getPanelIds($connection);
insertRandomNotification($panelIds, $messages, $connection);

echo "Імітація завершена.\n";
?>
