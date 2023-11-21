<?php

namespace user\model;
use base\model\BaseModel;

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

    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

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

  public function comment(string $szalloda_az, string $comment, int $uid = 0) {
    !empty($uid) ?
      $username = $_SESSION[SESSION_CURRENT_USER_NAME] :
      $username = 'anonymous';

    $stmt = $this->getConnection()->prepare("
      INSERT INTO comments (uid, username, comment, created, szalloda_az) 
      VALUES (:uid, :username, :comment, :created, :szalloda_az)
    ");
    $created = date('Y-m-d H:i:s', time());
    $stmt->bindParam(':uid', $uid);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':created', $created);
    $stmt->bindParam(':szalloda_az', $szalloda_az);
    $stmt->execute();
  }

  public function deleteComment(int $cid, int $uid = 0) {
    if (empty($uid)) {
      $uid = $_SESSION[SESSION_CURRENT_USER_ID];
    }

    $stmt = $this->getConnection()->prepare("
      SELECT cid FROM comments WHERE cid = :cid AND uid = :uid
    ");
    $stmt->bindParam(':cid', $cid);
    $stmt->bindParam(':uid', $uid);
    $stmt->execute();

    $comment = $stmt->fetchColumn();
    $deleted = FALSE;
    if (!empty($comment)) {
      $stmt = $this->getConnection()->prepare("
      DELETE FROM comments WHERE cid = :cid
    ");
      $stmt->bindParam(':cid', $cid);
      $stmt->execute();
      $deleted = TRUE;
    }
    return $deleted;

  }

  public function getComments() {
    if (empty($_SESSION[SESSION_CURRENT_USER_ID])) {
      return [];
    }
    $uid = $_SESSION[SESSION_CURRENT_USER_ID];
    $stmt = $this->getConnection()->prepare("
        SELECT comments.*, szalloda.nev FROM comments
        LEFT JOIN szalloda ON comments.szalloda_az = szalloda.az
        WHERE uid = :uid
    ");
    $stmt->bindParam(':uid', $uid);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * {@inheritdoc }
   */
  protected function getIdColumn() {
    return 'uid';
  }

}
