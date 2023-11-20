<?php

namespace user\controller;
use base\controller\BaseController;
use user\model\UserModel;
use user\view\UserView;

class UserController extends BaseController {

  protected UserModel $user;
  protected UserView $view;

  public function __construct() {
    $this->user = new UserModel();
    $this->view = new UserView();
    $this->view->setUser($this->user);
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
    elseif ($action === 'comment') {
      $this->handleComment();
    }
    else {
      $this->redirect('/login');
    }
  }

  public function handleLogin() {
    if (isset($_SESSION[SESSION_USER_LOGGED_IN])) {
      $this->redirect('/');
    }
    if (!isset($_POST['login'])) {
      $this->content = $this->view->buildLoginForm();
    }
    else {
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';
      $authenticated = $this->user->authenticate($username, $password);
      if ($authenticated) {
        $this->redirect('/');
      } else {
        $this->redirect('/login?error=2');
      }
    }
  }

  public function handleRegistration() {
    if (isset($_SESSION[SESSION_USER_LOGGED_IN])) {
      $this->redirect('/');
    }
    if (!isset($_POST['register'])) {
      $this->content = $this->view->buildRegistrationForm();
    } else {
      $email = $_POST['email'] ?? '';
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';
      $registered = $this->user->register($email, $username, $password);

      if ($registered) {
        $this->redirect('/login');
      } else {
        $this->redirect('/register?error=1');
      }
    }
  }

  public function handleLogout() {
    $this->content = '';
    unset($_SESSION[SESSION_CURRENT_USER_ID]);
    unset($_SESSION[SESSION_USER_LOGGED_IN]);
    session_destroy();
    $this->redirect('/');
  }

  public function index() {
    $this->content = '';
  }

  public function getComments() {
    $this->content = $this->view->getComments();
  }

  public function comment(string $az = '') {
    if (!isset($_POST['comment_text'])) {
      $this->content = $this->view->buildCommentForm($az);
    } else {
      $hotel_az = $_POST['szalloda_az'];
      $comment = $_POST['comment_text'];
      $uid = $_POST['uid'];
      $this->user->comment($hotel_az, $comment, $uid);
      $this->redirect('/my-comments');
    }
  }

  public function deleteComment(int $cid) {
    $deleted = $this->user->deleteComment($cid);
    $redirect_uri = '/my-comments';
    if (!$deleted) {
      $redirect_uri .= '?error=3';
    }
    $this->redirect($redirect_uri);
  }

}