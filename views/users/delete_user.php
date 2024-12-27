<?php
// Підключаємо конфігураційний файл і контролер
require_once '../../config/config.php';
require_once '../../controllers/UserController.php';

// Перевірка авторизації користувача та ролі адміністратора
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
  header('Location: ../../login.php');
  exit;
}

$controller = new UserController($connection);

// Перевірка, чи передано ID користувача для видалення
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $user_id = intval($_GET['id']);

  if ($controller->deleteUser($user_id)) {
    $_SESSION['message'] = 'Користувач успішно видалений.';
  } else {
    $_SESSION['message'] = 'Помилка при видаленні користувача. Спробуйте ще раз.';
  }
} else {
  $_SESSION['message'] = 'ID користувача не вказано або невірне.';
}

// Перенаправлення на сторінку зі списком користувачів після видалення
header('Location: view_users.php');
exit;
?>
