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

  public function __construct() {
  }

  /**
   * {@inheritdoc }
   */
  protected function getIdColumn() {
    return 'az';
  }

  public function list() {
    $hotels = $this->getHotel();
    $cards = [];
    foreach ($hotels as $hotel) {
      $cards[$hotel[$this->getIdColumn()]] = $this->buildCard($hotel);
    }
    if (empty($cards)) {
      return '';
    }
    return '<div class="hotels container">' . implode($cards) . '</div>';

  }

  /**
   * Gets a hotel by id.
   *
   * If no id was supplied then return all the hotels.
   *
   * @param int $id
   *
   * @return array|false
   */
  protected function getHotel(int $id = 0) {
    $query_string = "
      SELECT 
        {$this->tableName}.*, 
        {$this->tableHelyseg}.nev as 'helyseg_nev', {$this->tableHelyseg}.orszag as 'orszag',
        {$this->tableTavasz}.sorszam as 'sorszam', {$this->tableTavasz}.indulas as 'indulas',
        {$this->tableTavasz}.idotartam as 'idotartam', {$this->tableTavasz}.ar as 'tavasz_ar'
      FROM $this->tableName
      LEFT JOIN $this->tableHelyseg ON {$this->tableName}.helyseg_az = {$this->tableHelyseg}.az
      LEFT JOIN $this->tableTavasz ON {$this->tableName}.az = {$this->tableTavasz}.szalloda_az
    ";

    $params = NULL;
    if (!empty($id)) {
      $query_string = $query_string . " WHERE {$this->tableName}.az = :az";
      $params[':az'] = $id;
    }
    $stmt = $this->getConnection()->prepare($query_string);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  protected function buildCard(array $hotel) {
    return "<div class='hotel card'>
      <div class='fake-img'></div>
      <div class='container'>
        <div class='field'>
          <p>{$hotel['nev']} <span class='helyseg'>{$hotel['helyseg_nev']}</span></p>
        </div>
        <div class='field'>
          <p>Tengerpart tav <span>" . ($hotel['tengerpart_tav'] ?? 0) . "</span></p>
        </div>
        <div class='field'>
          <p>Repter tav <span>" . ($hotel['repter_tav'] ?? 0) . "</span></p>
        </div>
        <div class='field'>
          <p>Ar <span>" . ($hotel['ar'] ?? 0) . "</span></p>
        </div>
        <div class='field'>
          <p>Besorolas <span>" . ($hotel['besorolas'] ?? 1) . "</span></p>
        </div>
      </div>
    </div>";
  }

}