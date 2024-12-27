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

// Отримуємо всі панелі
$panels = $panelController->getAllPanels();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Список панелей - Solar Panel Management System</title>
  <link href="../../public/assets/css/bt5/bootstrap.min.css" rel="stylesheet">
  <link href="../../public/assets/css/bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
  <link rel="stylesheet" href="../../public/assets/css/bt/bootstrap-editable.css">
  <link rel="stylesheet" href="../../public/assets/css/bt/bootstrap-table.min.css">
  <script src="../../public/assets/js/jquery.min.js"></script>
  <script src="../../public/assets/js/fontawesome.js"></script>
</head>
<body>

<?php // Підключаємо заголовок
include '../layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center mb-5">Список сонячних панелей</h2>

    <!-- Кнопка для додавання нової панелі -->
    <div class="actions" style="margin-bottom: 20px;">
      <a href="add_panel.php" class="btn btn-primary">Додати нову панель</a>
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
             data-export-options='{ "fileName":"Panels", "worksheetName":"list1" }'>
        <thead>
        <tr>
          <th data-field="state" data-print-ignore="true" data-checkbox="true" tabindex="0"></th>
          <th data-sortable="true" class="text-center" title="Назва панелі" data-filter-control="input"
              data-visible="true"
              data-field="panel_name">Назва панелі
          </th>
          <th data-sortable="true" class="text-center" title="Дата встановлення" data-filter-control="input"
              data-visible="true"
              data-field="installation_date">Дата встановлення
          </th>
          <th data-sortable="true" class="text-center" title="Розташування" data-filter-control="input"
              data-visible="true"
              data-field="location">Розташування
          </th>
          <th data-sortable="true" class="text-center" title="Потужність (кВт)" data-filter-control="input"
              data-visible="true"
              data-field="capacity">Потужність (кВт)
          </th>
          <th data-sortable="true" class="text-center" title="Кут нахилу (°)" data-filter-control="input"
              data-visible="true"
              data-field="tilt_angle">Кут нахилу (°)
          </th>
          <th data-sortable="true" class="text-center" title="Ефективність модуля (%)" data-filter-control="input"
              data-visible="true"
              data-field="module_efficiency">Ефективність модуля (%)
          </th>
          <th data-sortable="true" class="text-center" title="Статус" data-filter-control="input"
              data-visible="true"
              data-field="status">Статус
          </th>
          <th class="text-center" title="Дії" data-print-ignore="true">Дії</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($panels as $panel): ?>
          <tr id="tr-id-<?= htmlspecialchars($panel['panel_id']); ?>"
              class="tr-c-<?= htmlspecialchars($panel['panel_id']); ?>">
            <td id="td-id-<?= htmlspecialchars($panel['panel_id']); ?>"
                class="td-c-<?= htmlspecialchars($panel['panel_id']); ?>"></td>
            <td title="<?= htmlspecialchars($panel['panel_name']); ?>">
                    <span data-type="text" data-title="Назва панелі" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($panel['panel_id']); ?>" data-name="panel_name">
                      <?= htmlspecialchars($panel['panel_name']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($panel['installation_date']); ?>">
                    <span data-type="text" data-title="Дата встановлення" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($panel['panel_id']); ?>" data-name="installation_date">
                      <?= htmlspecialchars($panel['installation_date']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($panel['location']); ?>">
                    <span data-type="text" data-title="Розташування" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($panel['panel_id']); ?>" data-name="location">
                      <?= htmlspecialchars($panel['location']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($panel['capacity']); ?>">
                    <span data-type="text" data-title="Потужність (кВт)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($panel['panel_id']); ?>" data-name="capacity">
                      <?= htmlspecialchars($panel['capacity']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($panel['tilt_angle']); ?>">
                    <span data-type="text" data-title="Кут нахилу (°)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($panel['panel_id']); ?>" data-name="tilt_angle">
                      <?= htmlspecialchars($panel['tilt_angle']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($panel['module_efficiency']); ?>">
                    <span data-type="text" data-title="Ефективність модуля (%)" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($panel['panel_id']); ?>" data-name="module_efficiency">
                      <?= htmlspecialchars($panel['module_efficiency']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($panel['status']); ?>">
                    <span data-type="text" data-title="Статус" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($panel['panel_id']); ?>" data-name="status">
                      <?= htmlspecialchars($panel['status']); ?> </span>
            </td>
            <td title="Дії">
              <a href="edit_panel.php?id=<?php echo htmlspecialchars($panel['panel_id']); ?>"
                 class="button edit-button">Редагувати</a>
              <a href="delete_panel.php?id=<?php echo htmlspecialchars($panel['panel_id']); ?>"
                 class="button delete-button" onclick="return confirm('Ви впевнені, що хочете видалити цю панель?');">Видалити</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

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
        pdf.save('Panels.pdf');
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
