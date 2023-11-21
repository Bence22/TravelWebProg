<?php

session_start();

spl_autoload_register(function ($class_name) {
  $file = str_replace('\\', '/', $class_name);
  require "$file.php";
});

const SESSION_CURRENT_USER_ID = 'current_user_id';
const SESSION_CURRENT_USER_NAME = 'current_user_name';
const SESSION_USER_LOGGED_IN = 'user_logged_in';

/**
 * Set .env variables.
 *
 * @param string $path
 *   Path to .env file.
 *
 * @return void
 */
function set_env_variables(string $path = '.env') {
  if (file_exists($path)) {
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      [$name, $value] = explode('=', $line, 2);
      $_ENV[$name] = $value;
    }
  }
}

set_env_variables($_SERVER['DOCUMENT_ROOT'] . '/.env');
