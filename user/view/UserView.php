<?php

class UserView {

  protected UserModel|null $user;
  public function __construct() {
  }

  /**
   * Sets user property.
   *
   * @param \UserModel $user
   *   User model.
   *
   */
  public function setUser(UserModel $user) {
    $this->user = $user;
  }

  /**
   * Getter for displayName.
   *
   * @return string
   */
  protected function getDisplayName() {
    return $this->user?->get('username') ?? '';
  }

  /**
   * Getter for email.
   *
   * @return string
   */
  protected function getEmail() {
    return $this->user?->get('email') ?? '';
  }

  public function buildDashboard() {
    return
    '<section class="dashboard">
        <h1>User Profile</h1>
        <p>Hello,  ' . htmlspecialchars($this->getDisplayName()) . '!</p>
        <p>You are registered with <b>' . htmlspecialchars($this->getEmail()) . '</b>!</p>
    </section>';
  }

  public function buildRegistrationForm() {
    return
      '<section class="registration">
        <h1>Registration</h1>
        <form action="index.php?action=register" method="post">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <br>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <br>
            <input type="submit" name="register" value="Register">
        </form>
      </section>';
  }

  public function buildLoginForm() {
    return '<section class="login">
        <h1>Login</h1>
        <form action="index.php?action=login" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <br>
            <input type="submit" name="login" value="Login">
        </form>
    </section>
    ';
  }

}