<header>
  <nav>
    <?php
      if (!empty($_SESSION[SESSION_USER_LOGGED_IN])) {
         echo '<p class="username"> ' . $_SESSION[SESSION_CURRENT_USER_NAME] . '</p>';
      }
    ?>
    <ul>
      <li><a href="index.php">Home</a></li>
      <?php
        if (empty($_SESSION[SESSION_USER_LOGGED_IN])) {
          echo '
            <li><a href="index.php?action=login">Login</a></li>
            <li><a href="index.php?action=register">Register</a></li>
          ';
        }
        else {
      ?>
        <li><a href="index.php?action=dashboard">Dashboard</a></li>
        <li><a href="index.php?action=logout">Logout</a></li>
      <?php
        }
      ?>
    </ul>
  </nav>
</header>