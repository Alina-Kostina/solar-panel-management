<?php
// Підключаємо конфігураційний файл для з'єднання з базою даних і старту сесії
require_once '../config/config.php';

// Якщо користувач уже авторизований, перенаправляємо його на панель керування
if (isset($_SESSION['user_id'])) {
  header('Location: ../views/dashboard.php');
  exit;
}

// Ініціалізація змінної для зберігання помилок
$error = '';

// Обробка форми після відправлення
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Очищуємо та отримуємо дані з форми
  $username = sanitize($_POST['username']);
  $password = $_POST['password'];

  // Перевіряємо, що всі поля заповнені
  if (empty($username) || empty($password)) {
    $error = 'Будь ласка, заповніть всі поля';
  } else {
    // Пошук користувача в базі даних
    $stmt = $connection->prepare("SELECT user_id, password_hash, role_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Перевіряємо, чи користувача знайдено
    if ($stmt->num_rows > 0) {
      $stmt->bind_result($user_id, $password_hash, $role_id);
      $stmt->fetch();

      // Перевірка пароля
      if (password_verify($password, $password_hash)) {
        // Встановлення сесії для користувача
        $_SESSION['user_id']  = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role_id']  = $role_id; // Зберігаємо роль користувача

        // Перенаправлення на панель керування
        header('Location: ../views/dashboard.php');
        exit;
      } else {
        $error = 'Невірний пароль. Спробуйте ще раз.';
      }
    } else {
      $error = 'Користувача не знайдено';
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
  <title>Вхід - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php require_once '../views/layouts/header.php'; ?>

<section class="p-3" style="height: calc(100vh - 174px);">
  <div class="container-fluid">
    <h2 class="text-center">Вхід</h2>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="w-50 m-auto mt-5 mb-5">
        <form class="text-center border border-info-subtle rounded-4 shadow-lg p-4" action="login.php" method="POST">
          <div class="mb-3 text-start">
            <label for="exampleInputEmail1" class="form-label">Ім'я користувача</label>
            <input type="text" class="form-control" id="exampleInputEmail1" name="username"
                   aria-describedby="emailHelp">
          </div>
          <div class="mb-3 text-start">
            <label for="exampleInputPassword1" class="form-label">Пароль</label>
            <input type="password" class="form-control" name="password" id="exampleInputPassword1">
          </div>

          <button type="submit" class="btn btn-primary w-50 text-center">Увійти</button>
        </form>
      </div>

      <p class="text-center">
        Ще не маєте акаунта?
        <a href="register.php">Зареєструватися</a>
      </p>
    </div>
  </div>
</section>

<?php require_once '../views/layouts/footer.php'; ?>
</body>
</html>
