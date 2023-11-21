<?php

namespace base\controller;

abstract class SoapServiceController extends BaseController {
  protected \SoapClient $client;

  protected $url = '';

  protected $options = [];

  protected $wsdl = NULL;

  protected function setClient() {
    try {
      if (empty($this->options)) {
        $this->options = [
          'keep_alive' => FALSE,
          'trace' => TRUE,
        ];
      }
      $this->client = new \SoapClient($this->wsdl, $this->options);
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
}
