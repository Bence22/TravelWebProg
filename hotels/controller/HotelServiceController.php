<?php
namespace hotels\controller;
use base\controller\BaseController;
  class HotelServiceController extends BaseController {

    const SORT_BY_ASC = 'ASC';
    const SORT_BY_DESC = 'DESC';
    protected \SoapClient $client;

    protected $url = '';

    protected $options;

    public function __construct() {
      $this->url = $_ENV['URL'] ?? 'napfeny.loc';
      $this->setClient();
    }

    protected function setClient() {
      try {
        $wsdl = NULL;
        if (empty($options)) {
          $options = [
            'location' => 'http://' . $this->url . "/hotels/server/HotelServer.php",
            'uri' => 'http://' . $this->url . "/hotels/server/HotelServer.php",
            'keep_alive' => FALSE,
            'trace' => TRUE,
          ];
        }
        $this->client = new \SoapClient($wsdl, $options);
      }
      catch (\SoapFault $f) {
        echo $f->getMessage();
        $this->debug();
      }
    }

    protected function getClient() {
      if (empty($this->client)) {
        $this->setClient();
      }
      return $this->client;
    }

    protected function debug() {
      echo $this->client->__getLastResponse();
      echo $this->client->__getLastRequest();
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