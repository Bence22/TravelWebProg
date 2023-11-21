<?php

namespace hotels\view;
use hotels\model\HotelModel;

class HotelView {

  protected HotelModel|null $hotel;

  public function setModel(HotelModel $model) {
    $this->hotel = $model;
  }
  public function list() {
    $hotels = $this->hotel->getHotel();
    $cards = [];
    foreach ($hotels as $hotel) {
      $cards[$hotel['az']] = $this->buildCard($hotel);
    }
    if (empty($cards)) {
      return '';
    }
    return '<div class="hotels container">' . implode($cards) . '</div>';

  }

  protected function buildCard(array $hotel) {
    return "<div class='hotel card'>
      <div class='image-container'>
        <img src='../../assets/images/" . $hotel['az'] . ".jpg'" . " alt='Hotel image'>
      </div>
      <div class='container'>
        <div class='field'>
          <p>{$hotel['nev']} <span class='helyseg'>{$hotel['helyseg_nev']}</span></p>
        </div>
        <div class='field'>
          <p>Tengerpart tav <span>" . ($hotel['tengerpart_tav'] ?? 0) . " m</span></p>
        </div>
        <div class='field'>
          <p>Repter tav <span>" . ($hotel['repter_tav'] ?? 0) . " km</span></p>
        </div>
        <div class='field'>
          <p>Ar <span>" . ($hotel['tavasz_ar'] ?? 0) . " Ft</span></p>
        </div>
        <div class='field'>
          <p>Besorolas <span>" . ($hotel['besorolas'] ?? 1) . " / 5</span></p>
        </div>
        <a class='primary' href='/hotel/comments/" . $hotel['az'] . "'> View comments (" . $this->hotel->getCommentCount($hotel['az']) . ")</a>
        <a class='secondary' href='/hotel/add-comment/" . $hotel['az'] . "'> Comment</a>
      </div>
    </div>";
  }

  public function getComments(string $az) {
    $comments = $this->hotel->getComments($az);
    if (empty($comments)) {
      return "<section><h2>No comments yet!</h2></section>";
    }
    $rows = [];
    foreach ($comments as $comment) {
      $rows[] = $this->buildTableRow($comment);
    }
    return "
    <section>
      <h2>Comments</h2>
      <table border='1'>
        <thead>
          <tr>
            <th>Username</th>
            <th>Comment</th>
            <th>Date</th>
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
      <td> " . $comment['username'] . "</td>
      <td> " . $comment['comment'] . "</td>
      <td> " . $comment['created'] . "</td>
    </tr>";
  }
}
