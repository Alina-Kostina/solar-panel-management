<?php
// Підключаємо конфігураційний файл для з'єднання з базою даних
require_once '../../config/config.php';
require_once '../../controllers/NotificationController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Перевірка, чи передано ID сповіщення через параметр URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  echo json_encode(['status' => 'error', 'message' => 'ID сповіщення не вказано']);
  exit;
}

$notification_id = intval($_GET['id']); // Отримуємо ID сповіщення

// Ініціалізація контролера сповіщень
$notificationController = new NotificationController($connection);

// Позначаємо сповіщення як вирішене
if ($notificationController->resolveNotification($notification_id)) {
  echo json_encode(['status' => 'success', 'message' => 'Сповіщення позначено як вирішене']);

  header('Location: view_notifications.php');
  exit;
} else {
  echo json_encode(['status' => 'error', 'message' => 'Не вдалося оновити сповіщення']);
}
?>

<script>
  // Функція для позначення сповіщення як вирішеного
  function resolveNotification(notificationId) {
    $.ajax({
      url: `/views/notifications/resolve_notification.php?id=${notificationId}`,
      method: 'GET',
      success: function (response) {
        if (response.status === 'success') {
          console.log(response.message);
          fetchNotifications(); // Оновлюємо список сповіщень
        } else {
          console.error(response.message);
        }
      },
      error: function () {
        console.error('Не вдалося позначити сповіщення як вирішене.');
      }
    });
  }

  // Приклад використання: додайте обробник для кнопки вирішення
  $(document).on('click', '.resolve-button', function () {
    const notificationId = $(this).data('id');
    resolveNotification(notificationId);
  });
</script>
