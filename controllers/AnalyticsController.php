<?php

class AnalyticsController
{
  private $connection;

  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  // Метод для отримання всіх записів аналітики
  public function getAllAnalytics()
  {
    $analytics = [];
    $stmt = $this->connection->prepare("
            SELECT 
                pa.analytics_id, 
                pa.panel_id,
                sp.panel_name,
                pa.date, 
                pa.total_energy_produced, 
                pa.peak_power_output, 
                pa.efficiency, 
                pa.avg_temperature, 
                pa.avg_irradiance, 
                pa.avg_cleanliness, 
                pa.avg_shading
            FROM performance_analytics pa
            JOIN solar_panels sp ON pa.panel_id = sp.panel_id
            ORDER BY pa.date DESC
        ");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
      $analytics[] = $row;
    }

    $stmt->close();
    return $analytics;
  }

  // Метод для генерації та збереження аналітики
  public function generateAnalytics()
  {
    $message = '';

    // Отримуємо список унікальних панелей
    $stmt = $this->connection->prepare("SELECT DISTINCT panel_id FROM panel_metrics");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
      $panel_id = $row['panel_id'];

      // Обчислюємо всі необхідні значення
      $stmtMetrics = $this->connection->prepare("
                SELECT 
                    SUM(energy_produced) AS total_energy,
                    MAX(power_output) AS peak_power,
                    AVG((power_output / (voltage * current)) * 100) AS efficiency,
                    AVG(temperature) AS avg_temperature,
                    AVG(irradiance) AS avg_irradiance,
                    AVG(cleanliness_level) AS avg_cleanliness,
                    AVG(shading) AS avg_shading
                FROM panel_metrics
                WHERE panel_id = ?
            ");
      $stmtMetrics->bind_param("i", $panel_id);
      $stmtMetrics->execute();
      $metricsResult = $stmtMetrics->get_result();
      $metricsData = $metricsResult->fetch_assoc();
      $stmtMetrics->close();

      if ($metricsData) {
        $total_energy = (float)($metricsData['total_energy'] ?? 0);
        $peak_power = (float)($metricsData['peak_power'] ?? 0);
        $efficiency = (float)($metricsData['efficiency'] ?? 0);
        $avg_temperature = (float)($metricsData['avg_temperature'] ?? 0);
        $avg_irradiance = (float)($metricsData['avg_irradiance'] ?? 0);
        $avg_cleanliness = (float)($metricsData['avg_cleanliness'] ?? 0);
        $avg_shading = (float)($metricsData['avg_shading'] ?? 0);

        // Зберігаємо аналітичні дані в таблицю
        $stmtAnalytics = $this->connection->prepare("
                    INSERT INTO performance_analytics (
                        panel_id, date, total_energy_produced, peak_power_output, efficiency, avg_temperature, avg_irradiance, avg_cleanliness, avg_shading
                    ) 
                    VALUES (?, CURDATE(), ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        total_energy_produced = VALUES(total_energy_produced),
                        peak_power_output = VALUES(peak_power_output),
                        efficiency = VALUES(efficiency),
                        avg_temperature = VALUES(avg_temperature),
                        avg_irradiance = VALUES(avg_irradiance),
                        avg_cleanliness = VALUES(avg_cleanliness),
                        avg_shading = VALUES(avg_shading)
                ");

        $stmtAnalytics->bind_param(
          "iddddddd",
          $panel_id,
          $total_energy,
          $peak_power,
          $efficiency,
          $avg_temperature,
          $avg_irradiance,
          $avg_cleanliness,
          $avg_shading
        );

        if ($stmtAnalytics->execute()) {
          $message = 'Аналітика успішно згенерована та збережена!';
        } else {
          $message = 'Помилка при збереженні аналітики для панелі ID ' . htmlspecialchars($panel_id);
        }

        $stmtAnalytics->close();
      }
    }

    $stmt->close();
    return $message;
  }
}
