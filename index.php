<?php

use base\router\Router;
require 'global.php';
$router = new Router();
$router->route();
$controller = $router->controller();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Napfény Tours</title>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <link rel="stylesheet" href="/assets/css/menu.css">
  <link rel="stylesheet" href="/assets/css/table.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'views/header.php'; ?>
<?php
  $content = $controller->getContent();
  if (!empty($content)) {
    echo $content;
  }
?>
<?php include 'views/footer.php'; ?>

<script src="/assets/js/script.js"></script>
</body>
</html>
