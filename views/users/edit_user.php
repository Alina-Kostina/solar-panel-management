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
$error      = '';
$success    = '';

// Перевірка, чи передано ID користувача
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $error = 'ID користувача не вказано.';
} else {
  $user_id = intval($_GET['id']);
  $user    = $controller->getUserById($user_id);
  if (!$user) {
    $error = 'Користувача не знайдено.';
  }
}

// Отримуємо список ролей
$roles = $controller->getAllRoles();

// Обробка форми після відправлення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
  $username         = $_POST['username'];
  $email            = $_POST['email'];
  $role_id          = intval($_POST['role_id']);
  $password         = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if (empty($username) || empty($email) || empty($role_id)) {
    $error = 'Будь ласка, заповніть всі поля.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Введіть коректну електронну адресу.';
  } elseif (!empty($password) && $password !== $confirm_password) {
    $error = 'Паролі не співпадають.';
  } else {
    // Оновлення даних користувача
    if (!empty($password)) {
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      $updateResult  = $controller->updateUserPassword($user_id, $password_hash);
      if ($updateResult && $controller->updateUser($user_id, $username, $email, $role_id)) {
        $success = 'Дані користувача успішно оновлено!';
      } else {
        $error = 'Помилка при оновленні даних користувача. Спробуйте ще раз.';
      }
    } else {
      if ($controller->updateUser($user_id, $username, $email, $role_id)) {
        $success = 'Дані користувача успішно оновлено!';
      } else {
        $error = 'Помилка при оновленні даних користувача. Спробуйте ще раз.';
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Редагувати користувача - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>

<?php include '../layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center">Редагувати користувача</h2>

    <div class="actions" style="margin-bottom: 20px;">
      <a href="view_users.php" class="btn btn-primary">Назад до списку користувачів</a>
    </div>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($user): ?>
      <div class="row">
        <div class="w-50 m-auto mt-2 mb-5">
          <form class="text-center border border-info-subtle rounded-4 shadow-lg p-4"
                action="edit_user.php?id=<?php echo htmlspecialchars($user_id); ?>" method="POST">
            <div class="mb-3 text-start">
              <label class="form-label" for="username">Ім'я користувача</label>
              <input class="form-control" type="text" name="username" id="username"
                     value="<?php echo htmlspecialchars($user['username']); ?>"
                     required>
            </div>

            <div class="mb-3 text-start">
              <label class="form-label" for="email">Електронна адреса</label>
              <input class="form-control" type="email" name="email" id="email"
                     value="<?php echo htmlspecialchars($user['email']); ?>"
                     required>
            </div>

            <div class="mb-3 text-start">
              <label class="form-label" for="role_id">Роль</label>
              <select class="form-control" name="role_id" id="role_id" required>
                <option value="">Оберіть роль</option>
                <?php foreach ($roles as $role): ?>
                  <option
                      value="<?php echo htmlspecialchars($role['role_id']); ?>" <?php echo ($role['role_id'] == $user['role_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($role['role_name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row">
              <div class="col-auto mb-3 w-50 text-start">
                <label class="form-label" for="password">Новий пароль (якщо потрібно змінити)</label>
                <input class="form-control" type="password" name="password" id="password">
              </div>

              <div class="col-auto mb-3 w-50 text-start">
                <label class="form-label" for="confirm_password">Підтвердьте новий пароль</label>
                <input class="form-control" type="password" name="confirm_password" id="confirm_password">
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-50 text-center">Оновити користувача</button>
          </form>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include '../layouts/footer.php'; ?>
</body>
</html>
