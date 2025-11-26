<?php
$title = 'Claim Detail';
ob_start();
?>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
