<?php
require_once '../../config/config.php';
require_once '../../controllers/AnalyticsController.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

$analyticsController = new AnalyticsController($connection);
$message             = $analyticsController->generateAnalytics();

header('Location: view_analytics.php');
exit;
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Генерація аналітики - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>
<div class="generate-analytics-container">
  <h2>Генерація аналітики ефективності</h2>

  <?php if ($message): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>

  <p><a href="view_analytics.php">Переглянути аналітику</a></p>
</div>

<?php include '../layouts/footer.php'; ?>
</body>
</html>
