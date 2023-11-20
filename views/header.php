<header>
  <nav>
    <?php
      if (!empty($_SESSION[SESSION_USER_LOGGED_IN])) {
         echo '<p class="username"> Bejelentkezett: ' . $_SESSION[SESSION_CURRENT_USER_NAME] . '!</p>';
      }
    ?>
    <ul>
      <li><a href="/">Home</a></li>
      <?php
        if (empty($_SESSION[SESSION_USER_LOGGED_IN])) {
          echo '
            <li><a href="/login">Login</a></li>
            <li><a href="/register">Register</a></li>
          ';
        }
        else {
      ?>
        <li><a href="/my-comments">My comments</a></li>
        <li><a href="/hotel">Hotels</a></li>
        <li><a href="/logout">Logout</a></li>
      <?php
        }
      ?>
    </ul>
  </nav>
</header>