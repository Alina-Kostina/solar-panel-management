<?php
// Підключаємо конфігураційний файл, контролер історії і перевіряємо авторизацію
require_once '../../config/config.php';
require_once '../../controllers/HistoryController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Ініціалізація контролера історії
$historyController = new HistoryController($connection);

// Отримуємо всі записи історії
$history = $historyController->getAllHistory();

// Підключаємо заголовок
include '../layouts/header.php';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Історія змін - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>
<div class="history-container">
  <h2>Історія змін стану панелей</h2>

  <table>
    <thead>
    <tr>
      <th>ID</th>
      <th>Назва панелі</th>
      <th>Дата зміни</th>
      <th>Попередній статус</th>
      <th>Новий статус</th>
      <th>Опис</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($history) > 0): ?>
      <?php foreach ($history as $record): ?>
        <tr>
          <td><?php echo htmlspecialchars($record['history_id'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($record['panel_name'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($record['change_date'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($record['previous_status'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($record['new_status'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($record['description'] ?? ''); ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="6">Немає доступних даних історії змін.</td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../layouts/footer.php'; ?>
</body>
</html>
