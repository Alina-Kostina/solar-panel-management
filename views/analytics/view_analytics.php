<?php
require_once '../../config/config.php';
require_once '../../controllers/AnalyticsController.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

$analyticsController = new AnalyticsController($connection);
$analytics           = $analyticsController->getAllAnalytics();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Аналітика - Solar Panel Management System</title>
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

<?php include '../layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center mb-5">Аналітика ефективності сонячних панелей</h2>

    <form action="generate_analytics.php" method="POST" style="margin-bottom: 20px;">
      <button type="submit" class="btn btn-primary">Згенерувати аналітику</button>
    </form>

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
             data-export-options='{ "fileName":"Analytics", "worksheetName":"list1" }'>
        <thead>
        <tr>
          <th data-field="state" data-print-ignore="true" data-checkbox="true" tabindex="0"></th>
          <th data-sortable="true" class="text-center" title="Назва панелі" data-filter-control="input"
              data-visible="true"
              data-field="panel_name">Назва панелі
          </th>
          <th data-sortable="true" class="text-center" title="Дата" data-filter-control="input"
              data-visible="true"
              data-field="date">Дата
          </th>
          <th data-sortable="true" class="text-center" title="Загальна енергія (кВт·год)" data-filter-control="input"
              data-visible="true"
              data-field="total_energy_produced">Загальна енергія (кВт·год)
          </th>
          <th data-sortable="true" class="text-center" title="Пікова потужність (Вт)" data-filter-control="input"
              data-visible="true"
              data-field="peak_power_output">Пікова потужність (Вт)
          </th>
          <th data-sortable="true" class="text-center" title="Ефективність (%)" data-filter-control="input"
              data-visible="true"
              data-field="efficiency">Ефективність (%)
          </th>
          <th data-sortable="true" class="text-center" title="Середня температура (°C)" data-filter-control="input"
              data-visible="true"
              data-field="avg_temperature">Середня температура (°C)
          </th>
          <th data-sortable="true" class="text-center" title="Середня інсоляція (Вт/м²)" data-filter-control="input"
              data-visible="true"
              data-field="avg_irradiance">Середня інсоляція (Вт/м²)
          </th>
          <th data-sortable="true" class="text-center" title="Інсоляція (Вт/м²)" data-filter-control="input"
              data-visible="true"
              data-field="avg_cleanliness">Чистота поверхні (%)
          </th>
          <th data-sortable="true" class="text-center" title="Затінення (%)" data-filter-control="input"
              data-visible="true"
              data-field="avg_shading">Затінення (%)
          </th>
        </tr>
        </thead>
        <tbody id="metrics-table-body">
        <?php foreach ($analytics as $data): ?>
          <tr id="tr-id-<?= htmlspecialchars($data['panel_id']); ?>"
              class="tr-c-<?= htmlspecialchars($data['panel_id']); ?>">
            <td id="td-id-<?= htmlspecialchars($data['panel_id']); ?>"
                class="td-c-<?= htmlspecialchars($data['panel_id']); ?>"></td>
            <td title="<?= htmlspecialchars($data['panel_name']); ?>">
                    <span data-type="text" data-title="Назва панелі" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="panel_name">
                      <?= htmlspecialchars($data['panel_name']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($data['date']); ?>">
                    <span data-type="text" data-title="Дата" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="date">
                      <?= htmlspecialchars($data['date']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($data['total_energy_produced']); ?>">
                    <span data-type="text" data-title="Загальна енергія (кВт·год)" data-mode="popup"
                          data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="total_energy_produced">
                      <?= htmlspecialchars($data['total_energy_produced']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($data['peak_power_output']); ?>">
                    <span data-type="text" data-title="Пікова потужність (Вт)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="peak_power_output">
                      <?= htmlspecialchars($data['peak_power_output']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($data['efficiency']); ?>">
                    <span data-type="text" data-title="Ефективність (%)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="efficiency">
                      <?= htmlspecialchars($data['efficiency']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($data['avg_temperature']); ?>">
                    <span data-type="text" data-title="Середня температура (°C)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="avg_temperature">
                      <?= htmlspecialchars($data['avg_temperature']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($data['avg_irradiance']); ?>">
                    <span data-type="text" data-title="Середня інсоляція (Вт/м²)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="avg_irradiance">
                      <?= htmlspecialchars($data['avg_irradiance']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($data['avg_cleanliness']); ?>">
                    <span data-type="text" data-title="Чистота поверхні (%)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="avg_cleanliness">
                      <?= htmlspecialchars($data['avg_cleanliness']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($data['avg_shading']); ?>">
                    <span data-type="text" data-title="Затінення (%)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($data['panel_id']); ?>" data-name="avg_shading">
                      <?= htmlspecialchars($data['avg_shading']); ?> </span>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
  function updateAnalytics() {
    const tableBody = $('#analytics-table-body');  // Define tableBody at the start

    $.ajax({
      url: 'generate_analytics.php',
      method: 'GET',
      success: function (response) {
        window.location.href = window.location.href;
      },
      error: function () {
        tableBody.empty().append('<tr><td colspan="10">Не вдалося завантажити дані.</td></tr>');
      }
    });
  }

  // setInterval(updateAnalytics, 10000);
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
        pdf.save('Analytics.pdf');
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
