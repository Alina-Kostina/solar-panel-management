<?php
// Підключаємо конфігураційний файл і контролер панелей, перевіряємо авторизацію
require_once '../../config/config.php';
require_once '../../controllers/PanelController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Ініціалізація контролера панелей
$panelController = new PanelController($connection);

// Отримуємо ID панелі з URL
$panel_id = $_GET['id'] ?? null;
if (!$panel_id) {
  die('ID панелі не вказано');
}

// Отримуємо дані панелі для редагування
$panel = $panelController->getPanelById($panel_id);
if (!$panel) {
  die('Панель не знайдено');
}

// Обробка форми після відправлення
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $panel_name        = $_POST['panel_name'];
  $installation_date = $_POST['installation_date'];
  $location          = $_POST['location'];
  $capacity          = $_POST['capacity'];
  $tilt_angle        = $_POST['tilt_angle'];
  $status            = $_POST['status'];
  $module_efficiency = $_POST['module_efficiency'];

  // Оновлюємо панель
  $result = $panelController->updatePanel($panel_id, $panel_name, $installation_date, $location, $capacity, $tilt_angle, $status, $module_efficiency);

  if ($result) {
    header('Location: view_panels.php');
    exit;
  } else {
    $error = 'Помилка при оновленні панелі.';
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Редагувати панель - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>
<?php include '../layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center">Редагувати панель</h2>

    <div class="actions" style="margin-bottom: 20px;">
      <a href="view_panels.php" class="btn btn-primary">Назад до списку панелей</a>
    </div>

    <?php if (isset($error)): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="w-50 m-auto mt-2 mb-5">
        <form class="text-center border border-info-subtle rounded-4 shadow-lg p-4"
              action="edit_panel.php?id=<?php echo htmlspecialchars($panel_id); ?>" method="POST">
          <div class="mb-3 text-start">
            <label class="form-label" for="panel_name">Назва панелі</label>
            <input class="form-control" type="text" name="panel_name" id="panel_name"
                   value="<?php echo htmlspecialchars($panel['panel_name']); ?>"
                   required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="installation_date">Дата встановлення</label>
            <input class="form-control" type="date" name="installation_date" id="installation_date"
                   value="<?php echo htmlspecialchars($panel['installation_date']); ?>" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="location">Розташування</label>
            <input class="form-control" type="text" name="location" id="location"
                   value="<?php echo htmlspecialchars($panel['location']); ?>"
                   required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="capacity">Потужність (кВт)</label>
            <input class="form-control" type="number" step="0.01" name="capacity" id="capacity"
                   value="<?php echo htmlspecialchars($panel['capacity']); ?>" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="tilt_angle">Кут нахилу (°)</label>
            <input class="form-control" type="number" step="0.1" name="tilt_angle" id="tilt_angle"
                   value="<?php echo htmlspecialchars($panel['tilt_angle']); ?>" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="status">Статус</label>

            <select class="form-control" name="status" id="status" required>
              <option value="active" <?php echo ($panel['status'] == 'active') ? 'selected' : ''; ?>>Активна</option>
              <option value="inactive" <?php echo ($panel['status'] == 'inactive') ? 'selected' : ''; ?>>Неактивна
              </option>
            </select>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="module_efficiency">Ефективність модуля (%)</label>
            <input class="form-control" type="number" step="0.1" min="0" max="100" name="module_efficiency"
                   id="module_efficiency"
                   value="<?php echo htmlspecialchars($panel['module_efficiency']); ?>" required>
          </div>

          <button type="submit" class="btn btn-primary w-50 text-center">Оновити панель</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include '../layouts/footer.php'; ?>
</body>
</html>
