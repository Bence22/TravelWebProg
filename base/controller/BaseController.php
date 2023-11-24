<?php

namespace base\controller;

abstract class BaseController {

  const EMAIL_IN_USE_ERROR = '1';
  const WRONG_CREDENTIALS_ERROR = '2';
  const CANNOT_DELETE_OTHERS_COMMENT_ERROR = '3';
  const SOAP_GET_EXCHANGE_RATES_ERROR = '4';
  const SOAP_GET_EXCHANGE_RATES_CURRENCIES_ARE_THE_SAME_ERROR = '5';

  /**
   * HTML content.
   *
   * @var string
   */
  protected string $content = '';
  /**
   * Getter for content.
   *
   * @return string
   */
  public function getContent() {
    $errors = $this->getErrors();
    if (!empty($errors)) {
      $this->content = '<div class"error-wrapper">' . $errors . $this->content . '</div>';
    }
    return '<main>' . $this->content . '</main>';
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
      self::EMAIL_IN_USE_ERROR => 'We already have a registration with this email address.',
      self::WRONG_CREDENTIALS_ERROR => 'Wrong credentials. Try again.',
      self::CANNOT_DELETE_OTHERS_COMMENT_ERROR => 'You cannot delete other users comments.',
      self::SOAP_GET_EXCHANGE_RATES_ERROR => 'Something went wrong with soap. Try again.',
      self::SOAP_GET_EXCHANGE_RATES_CURRENCIES_ARE_THE_SAME_ERROR => 'Currencies should be different.',
      default => 'Something went wrong. Try again.',
    };
    return '<section class="error-message-box">
      <p class="error-message">' . $message . '</p>
    </section>';
  }

  protected function redirect(string $url, int $status_code = 302) {
    unset($_POST);
    header("Location: $url", TRUE, $status_code);
    exit;
  }
}
