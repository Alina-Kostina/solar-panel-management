<?php
// Підключаємо конфігураційний файл і контролер показників, перевіряємо авторизацію
require_once '../../config/config.php';
require_once '../../controllers/MetricController.php';
require_once '../../controllers/PanelController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Ініціалізація контролера показників
$metricController = new MetricController($connection);
$panelController  = new PanelController($connection);

// Отримуємо всі показники для початкового завантаження
$metrics = $metricController->getAllMetrics();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Показники продуктивності панелей - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../public/assets/css/bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
  <link rel="stylesheet" href="../../public/assets/css/bt/bootstrap-editable.css">
  <link rel="stylesheet" href="../../public/assets/css/bt/bootstrap-table.min.css">
  <script src="../../public/assets/js/jquery.min.js"></script>
  <script src="../../public/assets/js/fontawesome.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php // Підключаємо заголовок
include '../layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center mb-5">Показники продуктивності сонячних панелей</h2>

    <div class="actions" style="margin-bottom: 20px;">
      <a href="add_metric.php" class="btn btn-primary">Додати новий показник</a>
    </div>

    <div id="table1" class="table-responsive">
      <table class="table table-striped table-condensed table-hover"
             data-locale="uk-UA"
             id="table111"
             data-toggle="table111"
             data-show-toggle="false"
             data-toolbar="#toolbar1"
             data-show-fullscreen="false"
             data-filter-control="true"
             data-filter-show-clear="false"
             data-show-print="true"
             data-show-copy-rows="false"
             data-show-export="true"
             data-click-to-select="false"
             data-pagination="true"
             data-page-list="[10, 25, 50, 100, 250, 500]"
             data-maintain-selected="true"
             data-maintain-meta-data="true"
             data-show-refresh="false"
             data-show-columns="true"
             data-show-search-button="false"
             data-show-search-clear-button="true"
             data-unique-id="id"
             data-minimum-count-columns="1"
             data-detail-view="false"
             data-mobile-responsive="true"
             data-check-on-init="true"
             data-export-types="['excel', 'doc']"
             data-export-options='{ "fileName":"Metrics", "worksheetName":"list1" }'>
        <thead>
        <tr>
          <th data-field="state" data-print-ignore="true" data-checkbox="true" tabindex="0"></th>
          <th data-sortable="true" class="text-center" title="Назва панелі" data-filter-control="input"
              data-visible="true"
              data-field="panel_name">Назва панелі
          </th>
          <th data-sortable="true" class="text-center" title="Час і дата" data-filter-control="input"
              data-visible="true"
              data-field="timestamp">Час і дата
          </th>
          <th data-sortable="true" class="text-center" title="Напруга (V)" data-filter-control="input"
              data-visible="true"
              data-field="voltage">Напруга (V)
          </th>
          <th data-sortable="true" class="text-center" title="Струм (A)" data-filter-control="input"
              data-visible="true"
              data-field="current">Струм (A)
          </th>
          <th data-sortable="true" class="text-center" title="Потужність (Вт)" data-filter-control="input"
              data-visible="true"
              data-field="power_output">Потужність (Вт)
          </th>
          <th data-sortable="true" class="text-center" title="Енергія (кВт·год)" data-filter-control="input"
              data-visible="true"
              data-field="module_efficiency">Енергія (кВт·год)
          </th>
          <th data-sortable="true" class="text-center" title="Температура (°C)" data-filter-control="input"
              data-visible="true"
              data-field="temperature">Температура (°C)
          </th>
          <th data-sortable="true" class="text-center" title="Інсоляція (Вт/м²)" data-filter-control="input"
              data-visible="true"
              data-field="irradiance">Інсоляція (Вт/м²)
          </th>
          <th data-sortable="true" class="text-center" title="Чистота поверхні (%)" data-filter-control="input"
              data-visible="true"
              data-field="cleanliness_level">Чистота поверхні (%)
          </th>
          <th data-sortable="true" class="text-center" title="Затінення (%)" data-filter-control="input"
              data-visible="true"
              data-field="shading">Затінення (%)
          </th>
          <th class="text-center" title="Дії" data-print-ignore="true">Дії</th>
        </tr>
        </thead>
        <tbody id="metrics-table-body">
        <?php foreach ($metrics as $metric):
          $panel = $panelController->getPanelById($metric['panel_id']); ?>
          <tr id="tr-id-<?= htmlspecialchars($metric['metric_id']); ?>"
              class="tr-c-<?= htmlspecialchars($metric['metric_id']); ?>">
            <td id="td-id-<?= htmlspecialchars($metric['metric_id']); ?>"
                class="td-c-<?= htmlspecialchars($metric['metric_id']); ?>"></td>
            <td title="<?= htmlspecialchars($panel['panel_name']); ?>">
                    <span data-type="text" data-title="Назва панелі" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="panel_name">
                      <?= htmlspecialchars($panel['panel_name'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['timestamp']); ?>">
                    <span data-type="text" data-title="Час і дата" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="timestamp">
                      <?= htmlspecialchars($metric['timestamp'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['voltage']); ?>">
                    <span data-type="text" data-title="Напруга (V)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="voltage">
                      <?= htmlspecialchars($metric['voltage'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['current']); ?>">
                    <span data-type="text" data-title="Струм (A)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="current">
                      <?= htmlspecialchars($metric['current'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['power_output']); ?>">
                    <span data-type="text" data-title="Потужність (Вт)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="power_output">
                      <?= htmlspecialchars($metric['power_output'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['energy_produced']); ?>">
                    <span data-type="text" data-title="Енергія (кВт·год)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="energy_produced">
                      <?= htmlspecialchars($metric['energy_produced'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['temperature']); ?>">
                    <span data-type="text" data-title="Температура (°C)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="temperature">
                      <?= htmlspecialchars($metric['temperature'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['irradiance']); ?>">
                    <span data-type="text" data-title="Інсоляція (Вт/м²)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="irradiance">
                      <?= htmlspecialchars($metric['irradiance'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['cleanliness_level']); ?>">
                    <span data-type="text" data-title="Чистота поверхні (%)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="cleanliness_level">
                      <?= htmlspecialchars($metric['cleanliness_level'] ?? '-'); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($metric['shading']); ?>">
                    <span data-type="text" data-title="Затінення (%)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($metric['metric_id']); ?>" data-name="shadings">
                      <?= htmlspecialchars($metric['shading'] ?? '-'); ?> </span>
            </td>
            <td title="Дії">
              <!--              <a href="edit_metric.php?id=-->
              <?php //echo htmlspecialchars($metric['metric_id']);
              ?><!--"-->
              <!--                 class="button edit-button">Редагувати</a>-->
              <a href="delete_metric.php?id=<?php echo htmlspecialchars($metric['metric_id']); ?>"
                 class="button delete-button"
                 onclick="return confirm('Ви впевнені, що хочете видалити цей показник?');">Видалити</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
  // Функція для оновлення показників
  function updateMetrics(saveToDb = true) {
    $.ajax({
      url: 'get_metrics.php',
      method: 'GET',
      data: {save: saveToDb ? 1 : 0}, // Якщо saveToDb = true, зберігаємо дані в базу
      success: function (response) {
        const metrics = JSON.parse(response);
        const tableBody = $('#metrics-table-body');
        tableBody.empty();

        if (metrics.length > 0) {
          metrics.forEach(metric => {
            const row = `<tr>
              <td></td>
              <td>${metric.panel_name}</td>
              <td>${metric.timestamp}</td>
              <td>${metric.voltage}</td>
              <td>${metric.current}</td>
              <td>${metric.power_output}</td>
              <td>${metric.energy_produced}</td>
              <td>${metric.temperature}</td>
              <td>${metric.irradiance}</td>
              <td>${metric.cleanliness_level}</td>
              <td>${metric.shading}</td>
              <td>
                <a href="delete_metric.php?id=${metric.metric_id}" class="button delete-button" onclick="return confirm('Ви впевнені, що хочете видалити цей показник?');">Видалити</a>
              </td>
            </tr>`;
            tableBody.append(row);
          });
        } else {
          tableBody.append('<tr><td colspan="12">Немає доступних показників продуктивності.</td></tr>');
        }
      }
    });
  }

  setInterval(updateMetrics, 8000);

  // $(window).on('beforeunload', function () {
  //   // if (isRealTime) {
  //   updateMetrics(true); // Оновлення даних у базі перед виходом зі сторінки
  //   // }
  // });
</script>

<?php include '../layouts/footer.php'; ?>

<script src="../../public/assets/js/bt/bootstrap-table.min.js"></script>
<script src="../../public/assets/js/bt/jspdf.min.js"></script>
<script src="../../public/assets/js/bt/bootstrap-table-print.min.js"></script>
<script src="../../public/assets/js/bt/bootstrap-table-locale-all.min.js"></script>
<script src="../../public/assets/js/bt/bootstrap-table-export.min.js"></script>
<script src="../../public/assets/js/bt/tableExport.min.js"></script>
<script src="../../public/assets/js/bt/bootstrap-table-mobile.js"></script>
<script src="../../public/assets/js/bt/bootstrap-table-filter-control.min.js"></script>
<script src="../../public/assets/js/bt/bootstrap-editable.min.js"></script>
<script src="../../public/assets/js/bt/bootstrap-table-editable.min.js"></script>

<script type="text/javascript">
  $(document).ready(function () {
    $(document).on("click", "a[data-type='pdf']", function () {
      var pdf = new jsPDF('p', 'pt', 'a4');
      pdf.addHTML($("#table111"), function () {
        pdf.save('Metrics.pdf');
        document.location.href = document.location.href;
      });
    });

    $(function () {
      var $table1 = $('#table111'), selections1 = [], ids = [];

      function getHeight() {
        return $(window).height() - 150;
      }

      $(window).resize(function () {
        $table1.bootstrapTable('resetView', {'height': getHeight()});
      });

      $('#show').click(function () {
        $table1.bootstrapTable('togglePagination');
        $table1.bootstrapTable('checkInvert');
        var ids = $.map($table1.bootstrapTable('getSelections'), function (row) {
          return row.id
        })
        $table1.bootstrapTable('remove', {
          field: 'id',
          values: ids
        })
        $table1.bootstrapTable('togglePagination');
      });

      $table1.bootstrapTable({
        height: getHeight(),
        silent: true,
        search: true,
        paginationLoop: true,
        sidePagination: 'client', // client or server
        totalRows: 1, // server side need to set
        pageNumber: 1,
        pageSize: 10,
        showPrint: true,
        paginationHAlign: 'right',
        paginationVAlign: 'both',
        icons: {print: 'fa-print', export: 'fa-file-export', columns: 'fa-list', clearSearch: 'fa-trash'}
      });
      setTimeout(function () {
        $table1.bootstrapTable('resetView', {'height': getHeight()});
      }, 1000);
    });
  });
</script>
</body>
</html>
