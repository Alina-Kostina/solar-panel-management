<?php

class NotificationController
{

  private $db;

  // Конструктор для ініціалізації з'єднання з базою даних
  public function __construct($dbConnection) {
    $this->db = $dbConnection;
  }


  // Функція для збереження нотифікацій у таблицю
  private function saveNotification($panel_id, $message, $is_resolved = 0, $is_seen = 0)
  {
    $query = "INSERT INTO notifications (panel_id, message, notification_date, is_resolved, is_seen) 
              VALUES (?, ?, NOW(), ?, ?)";
    $stmt = $this->db->prepare($query);
    if (!$stmt) {
      throw new Exception("Помилка підготовки запиту: " . $this->db->error);
    }

    $stmt->bind_param('isii', $panel_id, $message, $is_resolved, $is_seen);
    $stmt->execute();
    $stmt->close();
  }

  // Функція для створення нотифікацій на основі моніторингу
  public function generateMonitoringNotifications($params)
  {
    $irradiance    = $params['irradiance'] ?? null;
    $temperature   = $params['temperature'] ?? null;
    $season        = $params['season'] ?? null;
    $daylightHours = $params['daylightHours'] ?? null;
    $panel_id      = $params['panel_id'] ?? null; // Ідентифікатор панелі

    // Перевірка наявності панелі
    if (!$panel_id) {
      throw new Exception("Panel ID is required to generate notifications.");
    }

    // Виклик функцій моніторингу
    $messages = [];

    if ($irradiance !== null) {
      $messages = array_merge($messages, monitorSolarIrradiance($irradiance));
    }
    if ($temperature !== null) {
      $messages = array_merge($messages, monitorTemperature($temperature));
    }
    if ($season !== null) {
      $messages = array_merge($messages, adjustPanelAngle($season));
    }
    if ($daylightHours !== null) {
      $messages = array_merge($messages, monitorDaylightHours($daylightHours));
    }

    // Збереження кожного повідомлення у таблицю
    foreach ($messages as $message) {
      $this->saveNotification($panel_id, $message);
    }
  }

  public function getAllNotifications()
  {
    $query = "SELECT n.notification_id, n.panel_id, p.panel_name, n.message, n.notification_date, n.is_resolved 
              FROM notifications n
              LEFT JOIN solar_panels p ON n.panel_id = p.panel_id
              ORDER BY n.notification_date DESC";
    $result = $this->db->query($query);

    if (!$result) {
      throw new Exception("Помилка запиту до бази даних: " . $this->db->error);
    }

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
      $notifications[] = $row;
    }

    return $notifications;
  }
}

// Функції моніторингу
function monitorSolarIrradiance($irradiance)
{
  $messages = [];
  if ($irradiance < 500) {
    $messages[] = "Низька інсоляція (<500 Вт/м²). Можливе затінення. Перевірте стан панелей.";
  } elseif ($irradiance > 1200) {
    $messages[] = "Висока інсоляція (>1200 Вт/м²). Перевірте температуру батарей для запобігання перегріву.";
  } else {
    $messages[] = "Інсоляція в нормі: {$irradiance} Вт/м².";
  }
  return $messages;
}

function monitorTemperature($temperature)
{
  $messages = [];
  if ($temperature > 25) {
    $messages[] = "Температура висока ({$temperature}°C). Система обмежує потужність або активує охолодження.";
  } elseif ($temperature < -10) {
    $messages[] = "Температура низька ({$temperature}°C). Активовано теплоізоляцію.";
  } elseif ($temperature >= 15 && $temperature <= 25) {
    $messages[] = "Температура навколишнього середовища оптимальна: {$temperature}°C.";
  } else {
    $messages[] = "Температура навколишнього середовища: {$temperature}°C. Моніторинг триває.";
  }
  return $messages;
}

function adjustPanelAngle($season)
{
  $angles = [
    'winter' => 60,
    'spring' => 50,
    'summer' => 40,
    'autumn' => 50,
  ];
  if (isset($angles[$season])) {
    $angle = $angles[$season];
    return ["Кут нахилу батарей налаштовано до {$angle}° для {$season} сезону."];
  } else {
    return ["Невідомий сезон: {$season}. Налаштування кута нахилу неможливе."];
  }
}

function monitorDaylightHours($hours)
{
  $messages = [];
  if ($hours > 16) {
    $messages[] = "Світловий день триває довше 16 годин. Адаптація до літнього періоду.";
  } elseif ($hours < 8) {
    $messages[] = "Світловий день триває менше 8 годин. Адаптація до зимового періоду.";
  } else {
    $messages[] = "Тривалість світлового дня: {$hours} годин. Умови стабільні.";
  }
  return $messages;
}

?>
