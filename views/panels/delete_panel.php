<?php
// Підключаємо конфігураційний файл, контролер панелей і перевіряємо авторизацію
require_once '../../config/config.php';
require_once '../../controllers/PanelController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Ініціалізація контролера панелей
$panelController = new PanelController($connection);

// Перевірка, чи передано ID панелі для видалення
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $panel_id = intval($_GET['id']);

  // Видалення панелі через контролер
  if ($panelController->deletePanel($panel_id)) {
    // Створення повідомлення про успішне видалення
    $_SESSION['message'] = 'Панель успішно видалено.';
  } else {
    // Створення повідомлення про помилку
    $_SESSION['message'] = 'Помилка при видаленні панелі. Спробуйте ще раз.';
  }
} else {
  // Повідомлення, якщо ID не вказано або невірне
  $_SESSION['message'] = 'ID панелі не вказано або невірне.';
}

// Перенаправлення на сторінку зі списком панелей після видалення
header('Location: view_panels.php');
exit;
