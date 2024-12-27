<?php
// Підключаємо конфігураційний файл, контролер показників і перевіряємо авторизацію
require_once '../../config/config.php';
require_once '../../controllers/MetricController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Ініціалізація контролера показників
$metricController = new MetricController($connection);

// Отримуємо список панелей для вибору
$panels = [];
$stmt   = $connection->prepare("SELECT panel_id, panel_name FROM solar_panels WHERE status = 'active'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $panels[] = $row;
}
$stmt->close();

// Обробка форми після відправлення
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $panel_id        = $_POST['panel_id'];
  $timestamp       = $_POST['timestamp'];
  $voltage         = $_POST['voltage'];
  $current         = $_POST['current'];
  $power_output    = $_POST['power_output'];
  $energy_produced = $_POST['energy_produced'];
  $temperature     = $_POST['temperature'];
  $irradiance      = $_POST['irradiance'];
  $cleanliness     = $_POST['cleanliness'];
  $shading         = $_POST['shading'];

  // Додаємо новий показник
  $result = $metricController->addMetric($panel_id, $timestamp, $voltage, $current, $power_output, $energy_produced, $temperature, $irradiance, $cleanliness, $shading);

  if ($result) {
    header('Location: view_metrics.php');
    exit;
  } else {
    $error = 'Помилка при додаванні показника.';
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Додати показник - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>
<?php include '../layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center">Додати показник продуктивності</h2>

    <div class="actions" style="margin-bottom: 20px;">
      <a href="view_metrics.php" class="btn btn-primary">Назад до списку показників</a>
    </div>

    <?php if (isset($error)): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="w-50 m-auto mt-2 mb-5">
        <form class="text-center border border-info-subtle rounded-4 shadow-lg p-4" action="add_metric.php"
              method="POST">
          <div class="mb-3 text-start">
            <label class="form-label" for="panel_id">Панель</label>
            <select class="form-control" name="panel_id" id="panel_id" required>
              <option value="">Оберіть панель</option>
              <?php foreach ($panels as $panel): ?>
                <option value="<?php echo htmlspecialchars($panel['panel_id']); ?>">
                  <?php echo htmlspecialchars($panel['panel_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="timestamp">Дата і час</label>
            <input class="form-control" type="datetime-local" name="timestamp" id="timestamp" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="voltage">Напруга (V)</label>
            <input class="form-control" type="number" step="0.01" name="voltage" id="voltage" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="current">Струм (A)</label>
            <input class="form-control" type="number" step="0.01" name="current" id="current" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="power_output">Потужність (Вт)</label>
            <input class="form-control" type="number" step="0.01" name="power_output" id="power_output" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="energy_produced">Енергія (кВт·год)</label>
            <input class="form-control" type="number" step="0.01" name="energy_produced" id="energy_produced" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="temperature">Температура навколишнього середовища (°C)</label>
            <input class="form-control" type="number" step="0.1" name="temperature" id="temperature" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="irradiance">Інсоляція (Вт/м²)</label>
            <input class="form-control" type="number" step="0.1" name="irradiance" id="irradiance" required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="cleanliness">Чистота поверхні (%)</label>
            <input class="form-control" type="number" step="1" min="0" max="100" name="cleanliness" id="cleanliness"
                   required>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label" for="shading">Затінення (%)</label>
            <input class="form-control" type="number" step="1" min="0" max="100" name="shading" id="shading" required>
          </div>

          <button type="submit" class="btn btn-primary w-50 text-center">Додати показник</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include '../layouts/footer.php'; ?>
</body>
</html>
