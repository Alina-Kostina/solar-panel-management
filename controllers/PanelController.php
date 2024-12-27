<?php

class PanelController
{
  private $connection;

  // Конструктор для ініціалізації з'єднання з базою даних
  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  // Метод для отримання всіх панелей
  public function getAllPanels()
  {
    $panels = [];
    $stmt   = $this->connection->prepare("
        SELECT panel_id, panel_name, installation_date, location, capacity, tilt_angle, module_efficiency, status 
        FROM solar_panels
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
      $panels[] = $row;
    }

    $stmt->close();
    return $panels;
  }

  // Метод для отримання панелі за ID
  public function getPanelById($panel_id)
  {
    $stmt = $this->connection->prepare("SELECT * FROM solar_panels WHERE panel_id = ?");
    $stmt->bind_param("i", $panel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $panel  = $result->fetch_assoc();

    $stmt->close();
    return $panel;
  }

  // Метод для додавання нової панелі
  public function addPanel($panel_name, $installation_date, $location, $capacity, $tilt_angle, $status, $module_efficiency)
  {
    $stmt = $this->connection->prepare("
        INSERT INTO solar_panels 
        (panel_name, installation_date, location, capacity, tilt_angle, status, module_efficiency) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdisd", $panel_name, $installation_date, $location, $capacity, $tilt_angle, $status, $module_efficiency);

    $result = $stmt->execute();
    $stmt->close();
    return $result;
  }

  // Метод для оновлення панелі за ID
  public function updatePanel($panel_id, $panel_name, $installation_date, $location, $capacity, $tilt_angle, $status, $module_efficiency)
  {
    $stmt = $this->connection->prepare("
        UPDATE solar_panels 
        SET panel_name = ?, installation_date = ?, location = ?, capacity = ?, tilt_angle = ?, status = ?, module_efficiency = ?
        WHERE panel_id = ?");
    $stmt->bind_param("sssdisdi", $panel_name, $installation_date, $location, $capacity, $tilt_angle, $status, $module_efficiency, $panel_id);

    $result = $stmt->execute();
    $stmt->close();
    return $result;
  }

  // Метод для видалення панелі за ID
  public function deletePanel($panel_id)
  {
    $stmt = $this->connection->prepare("DELETE FROM solar_panels WHERE panel_id = ?");
    $stmt->bind_param("i", $panel_id);

    $result = $stmt->execute();
    $stmt->close();

    return $result;
  }
}
