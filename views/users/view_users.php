<?php
// Підключаємо конфігураційний файл і контролер
require_once '../../config/config.php';
require_once '../../controllers/UserController.php';

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
  header('Location: ../../login.php');
  exit;
}

$controller = new UserController($connection);
$users      = $controller->getAllUsers();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Користувачі - Solar Panel Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../public/assets/css/bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/assets/css/styles.css">
  <link rel="stylesheet" href="../../public/assets/css/bt/bootstrap-editable.css">
  <link rel="stylesheet" href="../../public/assets/css/bt/bootstrap-table.min.css">
  <script src="../../public/assets/js/jquery.min.js"></script>
  <script src="../../public/assets/js/fontawesome.js"></script>
</head>
<body>
<?php include '../layouts/header.php'; ?>

<section class="p-3">
  <div class="container-fluid">
    <h2 class="text-center mb-5">Список користувачів</h2>

    <div class="actions" style="margin-bottom: 20px;">
      <a href="add_user.php" class="btn btn-primary">Додати нового користувача</a>
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
             data-export-options='{ "fileName":"Analytics", "worksheetName":"list1" }'>
        <thead>
        <tr>
          <th data-field="state" data-print-ignore="true" data-checkbox="true" tabindex="0"></th>
          <th data-sortable="true" class="text-center" title="Ім'я користувача" data-filter-control="input"
              data-visible="true"
              data-field="username">Ім'я користувача
          </th>
          <th data-sortable="true" class="text-center" title="Електронна адреса" data-filter-control="input"
              data-visible="true"
              data-field="email">Електронна адреса
          </th>
          <th data-sortable="true" class="text-center" title="Роль" data-filter-control="input"
              data-visible="true"
              data-field="role_name">Роль
          </th>
          <th data-sortable="true" class="text-center" title="Дата створення" data-filter-control="input"
              data-visible="true"
              data-field="created_at">Дата створення
          </th>
          <th class="text-center" title="Дії" data-print-ignore="true">Дії</th>
        </tr>
        </thead>
        <tbody id="metrics-table-body">
        <?php foreach ($users as $user): ?>
          <tr id="tr-id-<?= htmlspecialchars($user['user_id']); ?>"
              class="tr-c-<?= htmlspecialchars($user['user_id']); ?>">
            <td id="td-id-<?= htmlspecialchars($user['user_id']); ?>"
                class="td-c-<?= htmlspecialchars($user['user_id']); ?>"></td>
            <td title="<?= htmlspecialchars($user['username']); ?>">
                    <span data-type="text" data-title="Ім'я користувача" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($user['user_id']); ?>" data-name="username">
                      <?= htmlspecialchars($user['username']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($user['email']); ?>">
                    <span data-type="text" data-title="Електронна адреса" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($user['user_id']); ?>" data-name="email">
                      <?= htmlspecialchars($user['email']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($user['role_name']); ?>">
                    <span data-type="text" data-title="Роль" data-mode="popup"
                          data-placement="top"
                          data-pk="<?= htmlspecialchars($user['user_id']); ?>" data-name="role_name">
                      <?= htmlspecialchars($user['role_name']); ?> </span>
            </td>
            <td title="<?= htmlspecialchars($user['created_at']); ?>">
                    <span data-type="text" data-title="Дата створення" data-mode="popup" data-placement="top"
                          data-pk="<?= htmlspecialchars($user['user_id']); ?>" data-name="created_at">
                      <?= htmlspecialchars($user['created_at']); ?> </span>
            </td>
            <td title="Дії">
              <a href="edit_user.php?id=<?php echo htmlspecialchars($user['user_id']); ?>" class="button edit-button">Редагувати</a>
              <a href="delete_user.php?id=<?php echo htmlspecialchars($user['user_id']); ?>"
                 class="button delete-button"
                 onclick="return confirm('Ви впевнені, що хочете видалити цього користувача?');">Видалити</a>
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
        pdf.save('Users.pdf');
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
