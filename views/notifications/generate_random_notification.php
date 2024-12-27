<?php
// Підключення конфігурації та необхідних файлів
require_once '../../config/config.php';
require_once '../../controllers/NotificationController.php';

// Отримання метрик панелей для аналізу
function getPanelMetrics($connection) {
  $metrics = [];
  $stmt = $connection->prepare("SELECT * FROM panel_metrics pm JOIN solar_panels sp ON pm.panel_id = sp.panel_id");
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $metrics[] = $row;
  }

  $stmt->close();
  return $metrics;
}

// Функція для аналізу метрик і генерації повідомлень
function generateNotifications($metrics, $connection) {
  $notifications = [];

  foreach ($metrics as $metric) {
    $panelId = $metric['panel_id'];
    $irradiance = $metric['irradiance'];
    $temperature = $metric['temperature'];
    $season = getCurrentSeason(); // Визначаємо сезон
    $daylightHours = getDaylightHours($season); // Тривалість світлового дня

    // Моніторинг інсоляції
    if ($irradiance < 500) {
      $notifications[] = [
        'panel_id' => $panelId,
        'message' => 'Низька інсоляція (<500 Вт/м²). Можливе затінення. Перевірте стан панелей.'
      ];
    } elseif ($irradiance > 1200) {
      $notifications[] = [
        'panel_id' => $panelId,
        'message' => 'Висока інсоляція (>1200 Вт/м²). Перевірте температуру батарей для запобігання перегріву.'
      ];
    }

    // Контроль температури
    if ($temperature > 25) {
      $notifications[] = [
        'panel_id' => $panelId,
        'message' => "Температура висока ({$temperature}°C). Система обмежує потужність або активує охолодження."
      ];
    } elseif ($temperature < -10) {
      $notifications[] = [
        'panel_id' => $panelId,
        'message' => "Температура низька ({$temperature}°C). Активовано теплоізоляцію."
      ];
    }

    // Адаптація до сезонних змін
//    $notifications[] = [
//      'panel_id' => $panelId,
//      'message' => "Тривалість світлового дня: {$daylightHours} годин. Сезон: {$season}."
//    ];
  }

  // Збереження повідомлень у базу даних
  saveNotifications($notifications, $connection);
}

// Функція для збереження повідомлень у базу даних
function saveNotifications($notifications, $connection) {
  $stmt = $connection->prepare("INSERT INTO notifications (panel_id, message, notification_date, is_resolved) VALUES (?, ?, ?, 0)");

  foreach ($notifications as $notification) {
    $panelId = $notification['panel_id'];
    $message = $notification['message'];
    $notificationDate = date("Y-m-d H:i:s");

    $stmt->bind_param("iss", $panelId, $message, $notificationDate);
    $stmt->execute();
  }

  $stmt->close();
}

// Функція для отримання поточного сезону
function getCurrentSeason() {
  $month = date('n');
  return match (true) {
    in_array($month, [12, 1, 2]) => 'winter',
    in_array($month, [3, 4, 5]) => 'spring',
    in_array($month, [6, 7, 8]) => 'summer',
    in_array($month, [9, 10, 11]) => 'autumn',
    default => 'unknown',
  };
}

// Функція для визначення тривалості світлового дня
function getDaylightHours($season) {
  return match ($season) {
    'summer' => 16,
    'winter' => 8,
    'spring', 'autumn' => 12,
    default => 12,
  };
}

// Основна логіка
$metrics = getPanelMetrics($connection);
generateNotifications($metrics, $connection);

echo json_encode(['status' => 'success']);
?>
