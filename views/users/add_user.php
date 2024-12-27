<?php
// Підключаємо конфігураційний файл і контролер
require_once '../../config/config.php';
require_once '../../controllers/UserController.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
  header('Location: ../../login.php');
  exit;
}

$controller = new UserController($connection);
$roles      = $controller->getAllRoles();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username         = $_POST['username'];
  $email            = $_POST['email'];
  $password         = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $role_id          = $_POST['role_id'];

  if ($password !== $confirm_password) {
    $error = 'Паролі не співпадають';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Введіть коректну електронну адресу';
  } else {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if ($controller->addUser($username, $email, $password_hash, $role_id)) {
      $success = 'Користувач успішно доданий!';
    } else {
      $error = 'Помилка при додаванні користувача.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Додати користувача - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>

<?php include '../layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center">Додати нового користувача</h2>

    <div class="actions" style="margin-bottom: 20px;">
      <a href="view_users.php" class="btn btn-primary">Назад до списку користувачів</a>
    </div>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="w-50 m-auto mt-2 mb-5">
        <form class="text-center border border-info-subtle rounded-4 shadow-lg p-4" action="add_user.php" method="POST">
          <div class="mb-3 text-start">
            <label class="form-label" for="username">Ім'я користувача</label>
            <input class="form-control" type="text" name="username" id="username" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="email">Електронна адреса</label>
            <input class="form-control" type="email" name="email" id="email" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="role_id">Роль</label>
            <select class="form-control" name="role_id" id="role_id" required>
              <option value="">Оберіть роль</option>
              <?php foreach ($roles as $role): ?>
                <option
                    value="<?php echo htmlspecialchars($role['role_id']); ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
              <?php endforeach; ?>
            </select>
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

          <button type="submit" class="btn btn-primary w-50 text-center">Додати користувача</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include '../layouts/footer.php'; ?>
</body>
</html>
