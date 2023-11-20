<?php

namespace hotels\model;
use base\model\BaseModel;

class HotelModel extends BaseModel {

  /**
   * Table name.
   *
   * @var string
   */
  protected string $tableName = 'szalloda';

  protected string $tableHelyseg = 'helyseg';
  protected string $tableTavasz = 'tavasz';
  protected string $tableComment = 'comments';

  public function __construct() {
  }

  /**
   * {@inheritdoc }
   */
  protected function getIdColumn() {
    return 'az';
  }

  public function setHotelAz(string $az) {
    $this->az = $az;
  }


  /**
   * Gets a hotel by id.
   *
   * If no id was supplied then return all the hotels.
   *
   * @param string $az
   *
   * @return array|false
   */
  public function getHotel(string $az = '') {
    $query_string = "
      SELECT 
        {$this->tableName}.*, 
        {$this->tableHelyseg}.nev as 'helyseg_nev', {$this->tableHelyseg}.orszag as 'orszag',
        {$this->tableTavasz}.szalloda_az as 'szalloda_az', {$this->tableTavasz}.indulas as 'indulas',
        {$this->tableTavasz}.idotartam as 'idotartam', {$this->tableTavasz}.ar as 'tavasz_ar'
      FROM $this->tableName
      LEFT JOIN $this->tableHelyseg ON {$this->tableName}.helyseg_az = {$this->tableHelyseg}.az
      LEFT JOIN $this->tableTavasz ON {$this->tableName}.az = {$this->tableTavasz}.szalloda_az
    ";

    $params = NULL;
    if (!empty($az)) {
      $query_string = $query_string . " WHERE {$this->tableName}.az = :az";
      $params[':az'] = $az;
    }
    $stmt = $this->getConnection()->prepare($query_string);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getComments(string $szalloda_az) {
    $stmt = $this->getConnection()->prepare("
        SELECT comments.*, szalloda.nev FROM comments
        LEFT JOIN szalloda ON comments.szalloda_az = szalloda.az
        WHERE szalloda.az = :szalloda_az
    ");
    $stmt->bindParam(':szalloda_az', $szalloda_az);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getCommentCount(string $szalloda_az) {
    $comments = $this->getComments($szalloda_az);
    return count($comments);
  }
}