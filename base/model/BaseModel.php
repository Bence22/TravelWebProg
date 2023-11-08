<?php

namespace base\model;
abstract class BaseModel {

  /**
   * @var \PDO $db
   *
   * Database connection.
   */
  private \PDO $db;

  protected string $tableName;

  /**
   * Entity id.
   *
   * @var int|null
   */
  protected $id = NULL;

  /**
   * Gets db connection.
   *
   * @return \PDO|void
   */
  protected function getConnection() {
    if (empty($this->db)) {
      $this->setEnvVariables();
      $host = getenv('DB_HOST');
      $dbname = getenv('DB_NAME');
      $username = getenv('DB_USER');
      $password = getenv('DB_PASS');

      try {
        $this->db = new \PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $this->db->query('SET NAMES utf8 COLLATE utf8_hungarian_ci');
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      } catch (\PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
      }
    }
    return $this->db;
  }

  /**
   * Set .env variables.
   *
   * @param string $path
   *   Path to .env file.
   *
   * @return void
   */
  protected function setEnvVariables(string $path = '.env') {
    if (file_exists($path)) {
      $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($lines as $line) {
        [$name, $value] = explode('=', $line, 2);
        putenv("$name=$value");
      }
    }
  }

  /**
   * Gets a given column name from db.
   *
   * @param string $column_name
   *
   * @return mixed|string
   */
  public function get(string $column_name = '') {
    if (empty($this->id)) {
      return '';
    }
    if (empty($column_name)) {
      $column_name = '*';
    }

    $stmt = $this->getConnection()
      ->prepare(
        "SELECT $column_name FROM {$this->tableName}"
        . " WHERE {$this->getIdColumn()} = :id");
    $stmt->bindParam(':id', $this->id);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  /**
   * Gets id column name.
   *
   * @return string
   */
  protected function getIdColumn() {
    return 'id';
  }
}