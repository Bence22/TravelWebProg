<?php
session_start();
require 'global.php';
require __DIR__ . '/user/model/UserModel.php';
require __DIR__ . '/user/view/UserView.php';
require __DIR__ . '/user/controller/UserController.php';
$user_model = new UserModel();
$user_view = new UserView();
$user_view->setUser($user_model);
$user_controller = new UserController($user_model, $user_view);
$user_controller->handle();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Napfeny Tours - Welcome</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include 'views/header.php'; ?>

<section id="home">
  <div class="hero">
    <h1>Welcome to Napfeny Tours</h1>
    <p>Your Perfect Getaway</p>
  </div>
</section>

<?php
  $content = $user_controller->getContent();
  if (!empty($content)) {
    echo '<main> ' . $content . '</main>';
  }
?>
<?php include 'views/footer.php'; ?>

<script src="assets/js/script.js"></script>
</body>
</html>