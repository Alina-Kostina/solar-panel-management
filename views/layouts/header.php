<?php
// Перевіряємо, чи користувач авторизований
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin    = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1; // Припускаємо, що role_id 1 - це адміністратор
?>
<header>
  <nav class="navbar navbar-expand-lg" style="background-color: #e3f2fd;">
    <div class="container-fluid">
      <a class="navbar-brand" href="/index.php">Solar Panel Management System</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
              aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <?php if ($isLoggedIn): ?>
            <li class="nav-item">
              <a class="nav-link text-black" href="/views/dashboard.php">Панель керування</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-black" href="/views/panels/view_panels.php">Сонячні панелі</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-black" href="/views/metrics/view_metrics.php">Показники</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-black" href="/views/analytics/view_analytics.php">Аналітика</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-black position-relative" href="/views/notifications/view_notifications.php">
                Сповіщення

                <?php
                // Кількість нових сповіщень
                $stmt = $connection->prepare("SELECT COUNT(*) AS new_notifications FROM notifications WHERE is_resolved = 0");
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) $newNotificationsCount = $row['new_notifications'];
                $stmt->close();

                if ($newNotificationsCount > 0) {
                  ?>
                  <span id="counter"
                        class="position-absolute top-2 start-90 translate-middle badge rounded-pill bg-danger">
                  <?php
                  echo htmlspecialchars($newNotificationsCount);
                  ?>
                  <span class="visually-hidden">unread messages</span>
                </span>
                <?php } ?>
              </a>
            </li>
            <!--            <li class="nav-item">-->
            <!--              <a class="nav-link text-black" href="/views/history/view_history.php">Історія</a>-->
            <!--            </li>-->

            <?php if ($isAdmin): ?>
              <li class="nav-item">
                <a class="nav-link text-black" href="/views/users/view_users.php">Користувачі</a>
              </li>
            <?php endif; ?>

            <li class="nav-item">
              <a class="nav-link text-black" href="/public/logout.php">Вийти</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link text-black" href="/public/login.php">Увійти</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-black" href="/public/register.php">Реєстрація</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>

<?php if ($isLoggedIn) { ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    // Функція для отримання кількості нових сповіщень
    function fetchNewNotificationsCount() {
      $.ajax({
        url: '/views/notifications/get_new_notifications_count.php',
        method: 'GET',
        success: function (response) {
          try {
            // Перевірка чи відповідь є об'єктом
            const data = typeof response === 'object' ? response : JSON.parse(response);

            if (data.status === 'success') {
              const newNotificationsCount = data.new_notifications;
              const notificationCounter = $('#counter');

              if (newNotificationsCount > 0) {
                notificationCounter.text(newNotificationsCount).show();
              } else {
                notificationCounter.text('').hide(); // Ховаємо лічильник, якщо немає нових сповіщень
              }
            } else {
              console.error('Не вдалося отримати кількість нових сповіщень. Повідомлення:', data.message);
            }
          } catch (error) {
            console.error('Помилка при парсингу JSON:', error, response);
          }
        },
        error: function (xhr, status, error) {
          console.error('Не вдалося отримати кількість нових сповіщень:', status, error, xhr.responseText);
        }
      });
    }

    // Виклик функції для оновлення кожні .1 секунд
    setInterval(fetchNewNotificationsCount, 100);

    // Функція для отримання та оновлення повідомлень
    function fetchNotifications() {
      $.ajax({
        url: '/views/notifications/get_notifications.php',
        method: 'GET',
        success: function (response) {
          // console.log("Відповідь сервера перед парсингом:", response); // Лог для діагностики

          // Переконуємось, що response є масивом
          const notifications = Array.isArray(response) ? response : [];
          const notificationContainer = $('#notification-container');
          const notificationBadge = $('#notification-badge');

          notificationContainer.empty();

          if (notifications.length > 0) {
            notificationBadge.text(notifications.length).show();

            notifications.forEach(notification => {
              const notificationItem = `
                        <div class="notification-item">
                            <p>${notification.panel_name}</p>
                            <p>${notification.message}</p>
                            <small>${new Date(notification.notification_date).toLocaleString()}</small>
                        </div>
                    `;
              notificationContainer.append(notificationItem);
            });
          } else {
            notificationBadge.hide(); // Приховуємо значок, якщо сповіщень немає
          }
        },
        error: function () {
          // console.error('Не вдалося отримати сповіщення.');
        }
      });
    }

    // Періодичне оновлення
    setInterval(fetchNotifications, 3000);


    // Функція для генерації випадкового повідомлення
    function generateRandomNotification() {
      $.ajax({
        url: '/views/notifications/generate_random_notification.php', // Замініть шлях за необхідності
        method: 'GET',
        success: function (response) {
          // console.log('Випадкове повідомлення додано:', response);
        },
        error: function () {
          // console.error('Не вдалося додати випадкове повідомлення.');
        }
      });
    }

    // Додавання нового випадкового повідомлення кожні 15 секунд
    setInterval(generateRandomNotification, 8000);

    // Показати/приховати сповіщення при кліку на значок
    $('#notification-badge-container').on('click', function () {
      $('#notification-container').toggle();
    });
  </script>
<?php } ?>

