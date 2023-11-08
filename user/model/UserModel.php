<?php

use base\model\BaseModel;

require 'base/model/BaseModel.php';

class UserModel extends BaseModel {

  /**
   * User id.
   *
   * @var int|null
   */
  private $uid = NULL;

  /**
   * Table name.
   *
   * @var string
   */
  protected string $tableName = 'users';

  public function __construct() {
    if (!empty($_SESSION[SESSION_USER_LOGGED_IN])) {
      $this->uid = $_SESSION[SESSION_CURRENT_USER_ID] ?? 0;
    }
  }

  /**
   * Handles user authentication.
   *
   * @param string $username
   *   User name.
   * @param string $password
   *   Password. Should be hashed.
   *
   * @return bool|void
   *   Either true or false.
   */
  public function authenticate(string $username, string $password) {
    $authenticated = FALSE;
    if (
      empty($username)
      || empty($password)
    ) {
      return $authenticated;
    }

    $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      if (password_verify($password, $user['password'])) {
        $authenticated = TRUE;
      }
    }

    if ($authenticated) {
      $_SESSION[SESSION_CURRENT_USER_ID] = $user['uid'];
      $_SESSION[SESSION_CURRENT_USER_NAME] = $user['username'];
      $_SESSION[SESSION_USER_LOGGED_IN] = TRUE;
    }

    return $authenticated;
  }

  /**
   * Handles user registration.
   *
   * @param string $email
   *   User email.
   * @param string $username
   *   User name.
   * @param string $password
   *   Password hash, not the password itself.
   *
   * @return bool|void
   *   Either true or false.
   */
  public function register(string $email, string $username, string $password) {
    $registered = FALSE;
    if (
      empty($email)
      || empty($username)
      || empty($password)
    ) {
      return $registered;
    }

    $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();


    if ($stmt->fetchColumn()) {
      // we found an user with the given email.
      return $registered;
    }

    $password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $this->getConnection()->prepare("
      INSERT INTO users (username, password, email) 
      VALUES (:username, :password, :email)
    ");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $registered = TRUE;
    return $registered;
  }

  /**
   * {@inheritdoc }
   */
  protected function getIdColumn() {
    return 'uid';
  }

}