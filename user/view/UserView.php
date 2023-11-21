<?php


namespace user\view;
use user\model\UserModel;

class UserView {

  protected UserModel|null $user;
  public function __construct() {
  }

  /**
   * Sets user property.
   *
   * @param UserModel $user
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
  public function getDisplayName() {
    return $this->user?->get('username') ?? '';
  }

  /**
   * Getter for email.
   *
   * @return string
   */
  public function getEmail() {
    return $this->user?->get('email') ?? '';
  }

  public function getComments() {
    $comments = $this->user?->getComments();
    if (empty($comments)) {
      return "<section><h2>No comments yet!</h2></section>";
    }
    $rows = [];
    foreach ($comments as $comment) {
      $rows[] = $this->buildTableRow($comment);
    }
    return "
    <section class='table comment'>
      <h2>My comments</h2>
      <table>
        <thead>
          <tr>
            <th>Comment</th>
            <th>Date</th>
            <th>Hotel name</th>
            <th>Operations</th>
          </tr>
        </thead>
        <tbody>
          " . implode($rows)
          . "
        </tbody>
      </table>
    </section>";
  }

  protected function buildTableRow(array $comment) {
    return "
    <tr>
      <td> " . $comment['comment'] . "</td>
      <td> " . $comment['created'] . "</td>
      <td> " . $comment['nev'] . "</td>
      <td> " . '<a href="/delete/comment/' . $comment['cid'] . '"' . ' >Delete comment</a>' . "</td>
    </tr>";
  }

  public function buildRegistrationForm() {
    return
      '<section class="registration form">
        <h1>Registration</h1>
        <form action="/register" method="post">
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
    return '<section class="login form">
        <h1>Login</h1>
        <form action="/login" method="post">
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

  public function buildCommentForm(string $szalloda_az) {
    return '<section class="comment form">
        <h1>Comment on ' . $szalloda_az . '</h1>
        <form action="/hotel/add-comment' . "/$szalloda_az" . '"' . 'method="post">
            <input type="hidden" name="szalloda_az" value="' . $szalloda_az . '">
            <input type="hidden" name="uid" value="' . ($_SESSION[SESSION_CURRENT_USER_ID] ?? 0) . '">
            <label for="comment">Comment</label>
            <textarea name="comment_text" id="comment_text" required></textarea>
            <input type="submit" name="comment" value="Comment">
        </form>
    </section>
    ';
  }

}
