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
      $host = $_ENV['DB_HOST'];
      $dbname = $_ENV['DB_NAME'];
      $username = $_ENV['DB_USER'];
      $password = $_ENV['DB_PASS'];

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
