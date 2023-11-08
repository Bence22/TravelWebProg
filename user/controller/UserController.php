<?php

class UserController {

  protected UserModel $user;
  protected UserView $view;

  /**
   * HTML content.
   *
   * @var string
   */
  protected string $content;

  public function __construct(UserModel $user, UserView $view) {
    $this->user = $user;
    $this->view = $view;
    $this->content = '';
  }

  public function handle() {
    $action = $_GET['action'] ?? '';
    if (empty($action)) {
      return;
    }
    if ($action === 'login') {
      $this->handleLogin();
    }
    elseif ($action === 'register') {
      $this->handleRegistration();
    }
    elseif ($action === 'logout') {
      $this->handleLogout();
    }
    elseif ($action === 'dashboard') {
      $this->dashboard();
    }
    else {
      header('Location: index.php?action=login');
    }
  }

  protected function handleLogin() {
    if (isset($_SESSION[SESSION_USER_LOGGED_IN])) {
      header('Location: index.php');
    }
    if (!isset($_POST['login'])) {
      $this->content = $this->view->buildLoginForm();
    }
    else {
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';
      $authenticated = $this->user->authenticate($username, $password);
      if ($authenticated) {
        header('Location: index.php?action=dashboard');
      } else {
        header('Location: index.php?action=login&error=2');
      }
    }
  }

  protected function handleRegistration() {
    if (isset($_SESSION[SESSION_USER_LOGGED_IN])) {
      header('Location: index.php');
    }
    if (!isset($_POST['register'])) {
      $this->content = $this->view->buildRegistrationForm();
    } else {
      $email = $_POST['email'] ?? '';
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';
      $registered = $this->user->register($email, $username, $password);

      if ($registered) {
        header('Location: index.php?action=login');
      } else {
        header('Location: index.php?action=register&error=1');
      }
    }
  }

  protected function handleLogout() {
    $this->content = '';
    unset($_SESSION[SESSION_CURRENT_USER_ID]);
    unset($_SESSION[SESSION_USER_LOGGED_IN]);
    session_destroy();
    header('Location: index.php');
  }

  protected function dashboard() {
    if (empty($_SESSION[SESSION_USER_LOGGED_IN])) {
      header('Location: index.php?action=login');
    }
    $this->content = $this->view->buildDashboard();
  }

  /**
   * Getter for content.
   *
   * @return string
   */
  public function getContent() {
    $errors = $this->getErrors();
    if (!empty($errors)) {
      $this->content = $errors . $this->content;
    }
    return $this->content;
  }

  /**
   * Renders error messages.
   *
   * @return string
   */
  protected function getErrors() {
    if (empty($_GET['error'])) {
      return '';
    }

    $message = match ($_GET['error']) {
      '1' => 'We already have a registration with this email address.',
      '2' => 'Wrong credentials. Try again.',
      default => 'Something went wrong. Try again.',
    };
    return '<section class="error-message-box">
      <p class="error-message">' . $message . '</p>
    </section>';
  }
}