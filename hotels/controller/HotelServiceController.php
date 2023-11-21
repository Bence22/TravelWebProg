<?php
namespace hotels\controller;
use base\controller\SoapServiceController;

class HotelServiceController extends SoapServiceController {
  public function __construct() {
    $this->url = $_ENV['URL'] ?? 'napfeny.loc';
    $this->options = [
      'location' => 'http://' . $this->url . "/hotels/server/HotelServer.php",
      'uri' => 'http://' . $this->url . "/hotels/server/HotelServer.php",
      'keep_alive' => FALSE,
      'trace' => TRUE,
    ];
    $this->setClient();
  }

    public function getHotel($id) {
      $client = $this->getClient();
      $this->content = $client->getHotel($id);
    }

    public function index() {
      $client = $this->getClient();
      $this->content = $client->listHotels();
    }

    public function getComments(string $szalloda_az) {
      $client = $this->getClient();
      $this->content = $client->getComments($szalloda_az);
    }

    public function sortByName(string $sort_by) {
      $client = $this->getClient();
      $this->content = $client->sortByName($sort_by);
    }

  }
