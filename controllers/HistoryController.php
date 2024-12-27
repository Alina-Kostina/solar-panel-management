<?php

class HistoryController
{
  private $connection;

  // Конструктор для ініціалізації з'єднання з базою даних
  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  // Метод для отримання всіх записів історії з приєднанням до таблиці сонячних панелей
  public function getAllHistory()
  {
    $history = [];
    $stmt = $this->connection->prepare("SELECT ph.history_id, sp.panel_name, ph.change_date, ph.previous_status, ph.new_status, ph.description
                                            FROM panel_history ph
                                            LEFT JOIN solar_panels sp ON ph.panel_id = sp.panel_id
                                            ORDER BY ph.change_date DESC");

    if ($stmt->execute()) {
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {
        $history[] = $row;
      }
    } else {
      echo "Помилка виконання запиту: " . $this->connection->error;
    }

    $stmt->close();
    return $history;
  }

  // Метод для додавання нового запису історії змін панелі
  public function addHistory($panel_id, $previous_status, $new_status, $description)
  {
    $stmt = $this->connection->prepare("INSERT INTO panel_history (panel_id, change_date, previous_status, new_status, description) VALUES (?, NOW(), ?, ?, ?)");
    $stmt->bind_param("isss", $panel_id, $previous_status, $new_status, $description);

    if ($stmt->execute()) {
      return true;
    } else {
      echo "Помилка додавання запису історії: " . $this->connection->error;
      return false;
    }
  }
}
