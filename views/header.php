<header>
  <nav role="navigation">
    <div id="menu--control">
      <input type="checkbox"/>
      <span></span>
      <span></span>
      <span></span>
      <ul class="menu" id="menu">
        <li><a href="/">Home</a></li>
        <li><a href="/hotel">Hotels</a></li>
        <li>
          <a href="#">MNB</a>
          <ul>
            <li><a href="/mnb/get-exchange-rates" class="href">Current exchange rates</a></li>
          </ul>
        </li>
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
          <li><a href="/logout">Logout</a></li>
          <?php
        }
        ?>
      </ul>
    </div>
  </nav>
  <div class="hero">
    <h1>Welcome to Napfény Tours</h1>
    <h4>
      <?php
      if (!empty($_SESSION[SESSION_USER_LOGGED_IN])) {
        echo '<span class="username">Logged in: ' . $_SESSION[SESSION_CURRENT_USER_NAME] . '</span>';
      }
      ?>
    </h4>
    <p>Your Perfect Getaway</p>
  </div>
</header>
