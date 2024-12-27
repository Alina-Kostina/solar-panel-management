<?php

require_once 'PanelController.php';

class MetricController
{
  private $connection;
  private $panelController;

  // Конструктор для ініціалізації з'єднання з базою даних
  public function __construct($connection)
  {
    $this->connection      = $connection;
    $this->panelController = new PanelController($connection);
  }

  // Додати метод до MetricController для отримання всіх записів
  public function getAllMetrics()
  {
    $metrics = [];
    $stmt    = $this->connection->prepare("
        SELECT *
        FROM panel_metrics 
        ORDER BY timestamp DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
      $metrics[] = $row;
    }

    $stmt->close();
    return $metrics;
  }

  // Метод для отримання всіх показників для конкретної панелі
  public function getMetricsByPanelId($panel_id)
  {
    // Перевірка на коректність panel_id
    if (is_null($panel_id) || !is_numeric($panel_id)) {
      echo "Некоректний panel_id: " . htmlspecialchars($panel_id);
      return [];
    }

    $metrics = [];

    if ($stmt = $this->connection->prepare("SELECT * FROM panel_metrics WHERE panel_id = ? ORDER BY timestamp DESC")) {
      $stmt->bind_param("i", $panel_id);
      if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
          $metrics[] = $row;
        }
      } else {
        // Помилка при виконанні запиту
        echo "Помилка виконання запиту: " . $this->connection->error;
      }
      $stmt->close();
    } else {
      // Помилка при підготовці запиту
      echo "Помилка підготовки запиту: " . $this->connection->error;
    }

    return $metrics;
  }

  // Метод для отримання показника за його ID
  public function getMetricById($metric_id)
  {
    if ($stmt = $this->connection->prepare("SELECT * FROM panel_metrics WHERE metric_id = ?")) {
      $stmt->bind_param("i", $metric_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $metric = $result->fetch_assoc();
      $stmt->close();

      return $metric;
    } else {
      echo "Помилка підготовки запиту: " . $this->connection->error;
      return null;
    }
  }

  // Метод для додавання нового показника продуктивності
  public function addMetric($panel_id, $timestamp, $voltage, $current, $power_output, $energy_produced, $temperature, $irradiance, $cleanliness, $shading)
  {
    $stmt = $this->connection->prepare("
        INSERT INTO panel_metrics 
        (panel_id, timestamp, voltage, current, power_output, energy_produced, temperature, irradiance, cleanliness_level, shading) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Переконайтеся, що типи в bind_param відповідають типам значень
    $stmt->bind_param("isddddddii", $panel_id, $timestamp, $voltage, $current, $power_output, $energy_produced, $temperature, $irradiance, $cleanliness, $shading);

    $result = $stmt->execute();
    $stmt->close();
    return $result;
  }

  // Метод для оновлення показника продуктивності за його ID
  public function updateMetric($metric_id, $voltage, $current, $power_output, $energy_produced, $temperature, $irradiance)
  {
    $stmt = $this->connection->prepare("UPDATE panel_metrics SET voltage = ?, current = ?, power_output = ?, energy_produced = ?, temperature = ?, irradiance = ? WHERE metric_id = ?");
    $stmt->bind_param("ddddddi", $voltage, $current, $power_output, $energy_produced, $temperature, $irradiance, $metric_id);

    if (!$stmt->execute()) {
      // Додаємо перевірку для відображення можливих помилок
      error_log("Помилка оновлення даних: " . $stmt->error);
    }

    $stmt->close();
  }

  // Метод для видалення показника продуктивності за його ID
  public function deleteMetric($metric_id)
  {
    if ($stmt = $this->connection->prepare("DELETE FROM panel_metrics WHERE metric_id = ?")) {
      $stmt->bind_param("i", $metric_id);
      $result = $stmt->execute();
      $stmt->close();

      return $result;
    } else {
      echo "Помилка підготовки запиту: " . $this->connection->error;
      return false;
    }
  }

  public function getMetricsForRealTime($saveToDb = true)
  {
    $metrics = $this->getAllMetrics(); // Отримуємо всі показники для відображення

    if ($saveToDb) {
      // Оновлюємо значення метрик у базі даних
      foreach ($metrics as $key => $metric) {
        $namePanel = $this->panelController->getPanelById($metric['panel_id']);

        $metrics[$key]['panel_name'] = $namePanel['panel_name'];

        // Генерація випадкових значень для метрик (замініть на реальні значення, якщо потрібно)
        $season        = getCurrentSeason(); // Отримуємо поточний сезон
        $daylightHours = getDaylightHours($season); // Отримуємо тривалість світлового дня

        $irradiance = match ($season) {
          'winter'           => rand(400, 800),
          'spring', 'autumn' => rand(600, 1000),
          'summer'           => rand(800, 1200),
          default            => rand(800, 1000),
        };

        $voltage = match (true) {
          $irradiance < 500  => rand(200, 220), // Низька інсоляція -> нижча напруга
          $irradiance > 1000 => rand(230, 240), // Висока інсоляція -> вища напруга
          default            => rand(220, 230), // Середнє значення
        };

        $current = rand(10, 20); // Струм у стандартному діапазоні

        $power_output = $voltage * $current; // Потужність = напруга * струм

        $energy_produced = match (true) {
          $irradiance > 1000 => rand(800, 1200), // Висока інсоляція -> більше енергії
          $irradiance < 500  => rand(300, 500), // Низька інсоляція -> менше енергії
          default            => rand(500, 800),
        };

        $temperature = match (true) {
          $irradiance > 1200 => rand(30, 40), // Висока інсоляція -> висока температура
          $irradiance < 500  => rand(-20, 15), // Низька інсоляція -> низька температура
          default            => rand(15, 25), // Оптимальна температура
        };

        $cleanliness_level = rand(75, 100); // Відносно стабільна чистота поверхні

        $shading = match (true) {
          $irradiance < 500  => rand(15, 30), // Низька інсоляція -> більше затінення
          $irradiance > 1000 => rand(0, 10), // Висока інсоляція -> мінімальне затінення
          default            => rand(5, 20),
        };

        // Оновлення у базі даних
        $stmt = $this->connection->prepare("
                UPDATE panel_metrics 
                SET voltage = ?, current = ?, power_output = ?, energy_produced = ?, temperature = ?, irradiance = ?, cleanliness_level = ?, shading = ? 
                WHERE metric_id = ?
            ");
        $stmt->bind_param("ddddddddi", $voltage, $current, $power_output, $energy_produced, $temperature, $irradiance, $cleanliness_level, $shading, $metric['metric_id']);
        $stmt->execute();
        $stmt->close();

        // Оновлюємо значення в масиві для виводу без запиту до бази
        $metric['voltage']           = $voltage;
        $metric['current']           = $current;
        $metric['power_output']      = $power_output;
        $metric['energy_produced']   = $energy_produced;
        $metric['temperature']       = $temperature;
        $metric['irradiance']        = $irradiance;
        $metric['cleanliness_level'] = $cleanliness_level;
        $metric['shading']           = $shading;
      }
    }

    return $metrics;
  }
}

function getCurrentSeason()
{
  $month = date('n');
  return match (true) {
    in_array($month, [12, 1, 2])  => 'winter',
    in_array($month, [3, 4, 5])   => 'spring',
    in_array($month, [6, 7, 8])   => 'summer',
    in_array($month, [9, 10, 11]) => 'autumn',
    default                       => 'unknown',
  };
}

function getDaylightHours($season)
{
  return match ($season) {
    'summer'           => 16,
    'winter'           => 8,
    'spring', 'autumn' => 12,
    default            => 12,
  };
}

