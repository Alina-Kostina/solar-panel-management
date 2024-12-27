<?php
// Підключаємо конфігураційний файл і перевіряємо авторизацію
require_once '../config/config.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Ініціалізація змінних для панелі інструментів
$totalPanels             = 0;
$totalEnergyProduced     = 0;
$activePanels            = 0;
$inactivePanels          = 0;
$avgEfficiency           = 0;
$dailyEnergyData         = [];
$topPanels               = [];
$monthlyEnergyComparison = [];
$avgTemperature          = 0;
$newNotificationsCount   = 0;

// Загальна кількість панелей та вироблена енергія
$stmt = $connection->prepare("SELECT COUNT(*) AS total_panels FROM solar_panels");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) $totalPanels = $row['total_panels'];
$stmt->close();

$stmt = $connection->prepare("SELECT SUM(total_energy_produced) AS total_energy FROM performance_analytics");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) $totalEnergyProduced = $row['total_energy'];
$stmt->close();

// Кількість активних та неактивних панелей
$stmt = $connection->prepare("SELECT status, COUNT(*) AS count FROM solar_panels GROUP BY status");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  if ($row['status'] === 'active') $activePanels = $row['count'];
  if ($row['status'] === 'inactive') $inactivePanels = $row['count'];
}
$stmt->close();

// Середня ефективність панелей
$stmt = $connection->prepare("SELECT COALESCE(AVG(efficiency), 0) AS avg_efficiency FROM performance_analytics");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) $avgEfficiency = $row['avg_efficiency'];
$stmt->close();

// Середня температура
$stmt = $connection->prepare("SELECT COALESCE(AVG(temperature), 0) AS avg_temperature FROM panel_metrics");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) $avgTemperature = $row['avg_temperature'];
$stmt->close();

// Щоденна вироблена енергія за останній тиждень
$stmt = $connection->prepare("SELECT date, SUM(total_energy_produced) AS daily_energy FROM performance_analytics GROUP BY date ORDER BY date DESC LIMIT 7");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $dailyEnergyData[] = $row;
$stmt->close();

// Топ панелі за виробленою енергією
$stmt = $connection->prepare("SELECT sp.panel_name, SUM(pa.total_energy_produced) AS energy FROM performance_analytics pa JOIN solar_panels sp ON pa.panel_id = sp.panel_id GROUP BY sp.panel_name ORDER BY energy DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $topPanels[] = $row;
$stmt->close();

// Порівняння з минулим місяцем
$stmt = $connection->prepare("SELECT YEAR(date) AS year, MONTH(date) AS month, SUM(total_energy_produced) AS monthly_energy 
                              FROM performance_analytics 
                              GROUP BY YEAR(date), MONTH(date) 
                              ORDER BY year DESC, month DESC 
                              LIMIT 2");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $monthlyEnergyComparison[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Solar Panel Management System</title>
  <link href="../../public/assets/css/bt5/bootstrap.min.css" rel="stylesheet">
  <link href="../../public/assets/css/bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
  <link rel="stylesheet" href="../../public/assets/css/bt/bootstrap-editable.css">
  <link rel="stylesheet" href="../../public/assets/css/bt/bootstrap-table.min.css">
  <script src="../../public/assets/js/jquery.min.js"></script>
  <script src="../../public/assets/js/fontawesome.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Підключаємо бібліотеку Chart.js -->
</head>
<body>
<?php
// Підключаємо заголовок
include 'layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center mb-5">Dashboard</h2>

    <div class="row">
      <!-- Загальні дані про панелі -->
      <div class="summary-container">
        <h3 class="text-center">Загальна інформація</h3>
        <div class="row">
          <div class="col-4 text-center">
            <p>Загальна кількість панелей: <?php echo htmlspecialchars($totalPanels); ?></p>
            <p>Загальна вироблена енергія (кВт·год): <?php echo htmlspecialchars($totalEnergyProduced ?? 'Немає результатів'); ?></p>
          </div>

          <div class="col-4 text-center">
            <p>Активні панелі: <?php echo htmlspecialchars($activePanels); ?></p>
            <p>Неактивні панелі: <?php echo htmlspecialchars($inactivePanels); ?></p>
          </div>

          <div class="col-4 text-center">
            <p>Середня ефективність: <?php echo round($avgEfficiency, 2) . '%'; ?></p>
            <p>Середня температура: <?php echo round($avgTemperature, 2) . '°C'; ?></p>
          </div>
        </div>
      </div>

      <hr>

      <div class="row align-items-center p-3">
        <div class="col-6">
          <!-- Діаграми -->
          <div class="chart-container">
            <h3 class="text-center">Кількість панелей (Активні vs Неактивні)</h3>

            <div style="width: 70%; margin: auto;">
              <canvas id="panelStatusChart"></canvas>
            </div>
          </div>
        </div>

        <div class="col-6">
          <div class="chart-container">
            <h3 class="text-center">Щоденна вироблена енергія за останній тиждень</h3>
            <canvas id="dailyEnergyChart"></canvas>
          </div>
        </div>
      </div>

      <hr>

      <div class="row align-items-center p-3">
        <div class="col-6">
          <div class="chart-container">
            <h3 class="text-center">Топ панелі за виробленою енергією</h3>
            <canvas id="topPanelsChart"></canvas>
          </div>
        </div>

        <div class="col-6">
          <div class="chart-container">
            <h3 class="text-center">Порівняння енергії з минулим місяцем</h3>
            <canvas id="monthlyComparisonChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  // Діаграма статусу панелей
  const panelStatusCtx = document.getElementById('panelStatusChart').getContext('2d');
  new Chart(panelStatusCtx, {
    type: 'doughnut',
    data: {
      labels: ['Активні', 'Неактивні'],
      datasets: [{
        data: [<?php echo $activePanels; ?>, <?php echo $inactivePanels; ?>],
        backgroundColor: ['#4CAF50', '#F44336']
      }]
    }
  });

  // Діаграма щоденної енергії за останній тиждень
  const dailyEnergyCtx = document.getElementById('dailyEnergyChart').getContext('2d');
  new Chart(dailyEnergyCtx, {
    type: 'line',
    data: {
      labels: [<?php foreach (array_reverse($dailyEnergyData) as $data) {
        echo '"' . $data['date'] . '",';
      } ?>],
      datasets: [{
        label: 'Вироблена енергія (кВт·год)',
        data: [<?php foreach (array_reverse($dailyEnergyData) as $data) {
          echo $data['daily_energy'] . ',';
        } ?>],
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)'
      }]
    }
  });

  // Діаграма топ панелей за виробленою енергією
  const topPanelsCtx = document.getElementById('topPanelsChart').getContext('2d');
  new Chart(topPanelsCtx, {
    type: 'bar',
    data: {
      labels: [<?php foreach ($topPanels as $panel) {
        echo '"' . $panel['panel_name'] . '",';
      } ?>],
      datasets: [{
        label: 'Вироблена енергія (кВт·год)',
        data: [<?php foreach ($topPanels as $panel) {
          echo $panel['energy'] . ',';
        } ?>],
        backgroundColor: '#FF9800'
      }]
    }
  });

  // Діаграма порівняння виробленої енергії
  const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
  new Chart(monthlyComparisonCtx, {
    type: 'bar',
    data: {
      labels: [<?php foreach ($monthlyEnergyComparison as $data) {
        echo '"Місяць ' . $data['month'] . '",';
      } ?>],
      datasets: [{
        label: 'Вироблена енергія (кВт·год)',
        data: [<?php foreach ($monthlyEnergyComparison as $data) {
          echo $data['monthly_energy'] . ',';
        } ?>],
        backgroundColor: '#FF9800'
      }]
    }
  });
</script>

<?php include 'layouts/footer.php'; ?>
</body>
</html>
