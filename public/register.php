<?php
// Підключаємо конфігураційний файл для з'єднання з базою даних і старту сесії
require_once '../config/config.php';

// Якщо користувач уже авторизований, перенаправляємо його на панель керування
if (isset($_SESSION['user_id'])) {
  header('Location: ../views/dashboard.php');
  exit;
}

// Ініціалізація змінних для зберігання помилок і повідомлень
$error   = '';
$success = '';

// Обробка форми після відправлення
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Отримуємо та очищуємо дані з форми
  $username         = sanitize($_POST['username']);
  $email            = sanitize($_POST['email']);
  $password         = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  // Перевірка, що всі поля заповнені
  if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $error = 'Будь ласка, заповніть всі поля';
  } elseif ($password !== $confirm_password) {
    $error = 'Паролі не співпадають';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Введіть коректну електронну адресу';
  } else {
    // Перевірка, чи існує користувач з таким же ім'ям або електронною адресою
    $stmt = $connection->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $error = 'Користувач з таким ім\'ям або електронною адресою вже існує';
    } else {
      // Хешування пароля
      $password_hash = password_hash($password, PASSWORD_DEFAULT);

      // Встановлення ролі за замовчуванням (наприклад, "viewer")
      $default_role_id = 3; // ID ролі "viewer" у таблиці roles

      // Додавання нового користувача до бази даних
      $stmt = $connection->prepare("INSERT INTO users (username, password_hash, email, role_id) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("sssi", $username, $password_hash, $email, $default_role_id);

      if ($stmt->execute()) {
        $success = 'Реєстрація успішна! Тепер ви можете <a href="login.php">увійти</a>';
      } else {
        $error = 'Помилка під час реєстрації. Спробуйте ще раз.';
      }
    }

    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Реєстрація - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php require_once '../views/layouts/header.php'; ?>

<section class="p-3" style="height: calc(100vh - 80px);">
  <div class="container-fluid">
    <h2 class="text-center">Реєстрація</h2>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="w-50 m-auto mt-5 mb-5">
        <form class="text-center border border-info-subtle rounded-4 shadow-lg p-4" action="register.php" method="POST">
          <div class="mb-3 text-start">
            <label class="form-label" for="username">Ім'я користувача</label>
            <input class="form-control" type="text" name="username" id="username" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="email">Електронна адреса</label>
            <input class="form-control" type="email" name="email" id="email" required>
          </div>

          <div class="row">
            <div class="col-auto mb-3 w-50 text-start">
              <label class="form-label" for="password">Пароль</label>
              <input class="form-control" type="password" name="password" id="password" required>
            </div>

            <div class="col-auto mb-3 w-50 text-start">
              <label class="form-label" for="confirm_password">Підтвердьте пароль</label>
              <input class="form-control" type="password" name="confirm_password" id="confirm_password" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-50 text-center">Зареєструватися</button>
        </form>
      </div>

      <p class="text-center">
        Вже маєте акаунт?
        <a href="login.php">Увійти</a>
      </p>
    </div>
  </div>
</section>

<?php require_once '../views/layouts/footer.php'; ?>
</body>
</html>
