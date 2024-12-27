<?php
// Підключаємо конфігураційний файл, контролер сповіщень і перевіряємо авторизацію
require_once '../../config/config.php';
require_once '../../controllers/NotificationController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

// Ініціалізація контролера сповіщень
$notificationController = new NotificationController($connection);

// Отримуємо всі сповіщення
$notifications = $notificationController->getAllNotifications();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Сповіщення - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <h2 class="text-center mb-5">Список сповіщень</h2>

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
          <th data-sortable="true" class="text-center" title="Повідомлення" data-filter-control="input"
              data-visible="true"
              data-field="message">Повідомлення
          </th>
          <th data-sortable="true" class="text-center" title="Напруга (V)" data-filter-control="input"
              data-visible="true"
              data-field="notification_date">Дата сповіщення
          </th>
          <th data-sortable="true" class="text-center" title="Статус" data-filter-control="input"
              data-visible="true"
              data-field="is_resolved">Статус
          </th>
          <th class="text-center" title="Дії" data-print-ignore="true">Дії</th>
        </tr>
        </thead>
        <tbody id="metrics-table-body">
        <?php foreach ($notifications as $notification): ?>
          <tr id="tr-id-<?= htmlspecialchars($notification['notification_id']); ?>"
              class="tr-c-<?= htmlspecialchars($notification['notification_id']); ?>">
            <td id="td-id-<?= htmlspecialchars($notification['notification_id']); ?>"
                class="td-c-<?= htmlspecialchars($notification['notification_id']); ?>"></td>
            <td title="<?= htmlspecialchars($notification['panel_name'] ?? ''); ?>">
                    <span data-type="text" data-title="Назва панелі" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($notification['notification_id']); ?>" data-name="panel_name">
                      <?= htmlspecialchars($notification['panel_name'] ?? ''); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($notification['message'] ?? ''); ?>">
                    <span data-type="text" data-title="Повідомлення" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($notification['notification_id']); ?>" data-name="message">
                      <?= htmlspecialchars($notification['message'] ?? ''); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($notification['notification_date'] ?? ''); ?>">
                    <span data-type="text" data-title="Дата сповіщення" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($notification['notification_id']); ?>"
                          data-name="notification_date">
                      <?= htmlspecialchars($notification['notification_date'] ?? ''); ?> </span>
            </td>
            <td title="<?= $notification['is_resolved'] ? 'Вирішено' : 'Невирішено'; ?>">
                    <span data-type="text" data-title="Статус" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($notification['notification_id']); ?>" data-name="is_resolved">
                      <?= $notification['is_resolved'] ? 'Вирішено' : 'Невирішено'; ?> </span>
            </td>
            <td title="Дії">
              <?php if (!$notification['is_resolved']): ?>
                <a href="resolve_notification.php?id=<?php echo htmlspecialchars($notification['notification_id']); ?>"
                   class="button resolve-button">Позначити як вирішене</a>
              <?php endif; ?>

              <a href="delete_notification.php?id=<?php echo htmlspecialchars($notification['notification_id']); ?>"
                 class="button delete-button"
                 onclick="return confirm('Ви впевнені, що хочете видалити це сповіщення?');">Видалити</a>
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
        pdf.save('Notifications.pdf');
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
