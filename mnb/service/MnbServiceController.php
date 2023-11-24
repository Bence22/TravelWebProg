<?php

namespace mnb\service;

use base\controller\SoapServiceController;
use SimpleXMLElement;

/** @see https://www.mnb.hu/letoltes/documentation-on-the-mnb-s-web-service-on-current-and-historic-exchange-rates.pdf */
class MnbServiceController extends SoapServiceController {

  protected $currencies = [];

  public function __construct() {
    $this->url = $_ENV['URL'] ?? 'napfeny.loc';
    $this->wsdl = 'http://www.mnb.hu/arfolyamok.asmx?WSDL';
    $this->setClient();
  }

  protected function setCurrencies() {
    $client = $this->getClient();
    $result = $this->xmlToArray(
      simplexml_load_string(
        $client->GetCurrencies()->GetCurrenciesResult
      )
    );
    $curr_attributes = $result['MNBCurrencies']['Currencies']['Curr'];
    foreach ($curr_attributes as $currency) {
      $this->currencies[] = $currency['value'];
    }
  }

  protected function getCurrencies() {
    if (empty($this->currencies)) {
      $this->setCurrencies();
    }
    return $this->currencies;
  }

  public function buildExchangeRatesForm(array $default_values = []) {
    $options = $this->getCurrencies();
    foreach ($options as $option) {
      $options_tag[] = "<option value=$option>" . $option . '</option>';
    }
    $options_tag = implode($options_tag);
    $default_start_date = $default_values['start_date'] ?? date('Y.m.d', strtotime('-1 days'));
    $default_end_date = $default_values['end_date'] ?? date('Y.m.d');

    return '<section class="exhcange-rates form">
        <h1>Get exchange rates</h1>
        <form action="/mnb/get-exchange-rates" method="post">
            <label for="start_date">Start date</label>
            <input type="date" value="' . $default_start_date . '" name="start_date" id="start_date" required>
            <label for="end_date">End date</label>
            <input type="date" value="' . $default_end_date . '" name="end_date" id="end_date" required>
            <select name="currency_one">' . $options_tag . '</select>
            <select name="currency_two">' . $options_tag . '</select>
            <input type="submit" name="search" value="Search">
        </form>
    </section>
    ';
  }

  public function getExchangeRates() {
    if (
      empty($_POST['start_date'])
      || empty($_POST['end_date'])
      || empty($_POST['currency_one'])
      || empty($_POST['currency_two'])
    ) {
      $this->content = $this->buildExchangeRatesForm();
    }
    else {
      $start_date = $_POST['start_date'] ?? date('Y.m.d', strtotime('-30 days'));
      $end_date = $_POST['end_date'] ?? date('Y.m.d');
      $currency_one = $_POST['currency_one'];
      $currency_two = $_POST['currency_two'];
      $currencies_are_the_same = $currency_one === $currency_two;

      if ($currencies_are_the_same) {
        $this->redirect('/mnb/get-exchange-rates?error=' . $this::SOAP_GET_EXCHANGE_RATES_ERROR);
      }
      $currency_names = implode(',', [$_POST['currency_one'], $_POST['currency_two']]);

      $is_error = FALSE;
      $name_array = explode(',', $currency_names);
      $currency_list = array_flip($this->getCurrencies());
      foreach ($name_array as $currency_name) {
        if (!isset($currency_list[$currency_name])) {
          $is_error = TRUE;
          break;
        }
      }
      if ($is_error) {
        $this->redirect('/mnb/get-exchange-rates?error=' . $this::SOAP_GET_EXCHANGE_RATES_ERROR);
      }
      $client = $this->getClient();
      $result = $this->xmlToArray(
        simplexml_load_string(
          $client->GetExchangeRates([
            'startDate' => $start_date,
            'endDate' => $end_date,
            'currencyNames' => $currency_names,
          ])->GetExchangeRatesResult
        )
      );
      $rate_attributes = $result['MNBExchangeRates']['Day'];
      $rates = [];
      foreach ($rate_attributes as $result) {
        $date = $result['attributes']['date'];
        $value = $result['Rate']['value'];
        if (
          $date
          && $value
        ) {
          $rates[] = [
            'date' => $date,
            'value' => $value
          ];
        }
      }

      $table = $this->buildTable(
        $rates,
        [
          'date' => 'Date',
          'value' => "$currency_names",
        ],
        "$currency_one - $currency_two rate",
      );
      $this->content =
        '<section class="mnb flex">' .
        $this->buildChart() .
        $this->buildExchangeRatesForm([
        'currency_one' => $_POST['currency_one'],
        'currency_two' => $_POST['currency_two'],
        'start_date' => $start_date,
        'end_date' => $end_date,
      ]) . $table
        . '</section>';
    }
  }

  protected function buildChart() {
    return '<canvas class="mnb-chart"></canvas>';
  }

  public function getCurrentExchangeRatesResult() {
    $client = $this->getClient();
    $result = $this->xmlToArray(
      simplexml_load_string(
        $client->GetCurrentExchangeRates()->GetCurrentExchangeRatesResult
      )
    );
    $rate_attributes = $result['MNBCurrentExchangeRates']['Day']['Rate'];
    $rates = [];
    foreach ($rate_attributes as $attribute) {
      $rates[$attribute['attributes']['curr']] = [
        'curr' => $attribute['attributes']['curr'],
        'unit' => $attribute['attributes']['unit'],
        'value' => $attribute['value'],
      ];
    }
    $table = $this->buildTable($rates, [
      'curr' => 'Currency',
      'unit'  => 'Unit',
      'value' => 'Value',
    ],
    );

    $this->content = $table;
  }

  protected function xmlToArray(SimpleXMLElement $xml): array {
    $parser = function (SimpleXMLElement $xml, array $collection = []) use (&$parser) {
      $nodes = $xml->children();
      $attributes = $xml->attributes();

      if (0 !== count($attributes)) {
        foreach ($attributes as $attrName => $attrValue) {
          $collection['attributes'][$attrName] = strval($attrValue);
        }
      }

      if (0 === $nodes->count()) {
        $collection['value'] = strval($xml);
        return $collection;
      }

      foreach ($nodes as $nodeName => $nodeValue) {
        if (count($nodeValue->xpath('../' . $nodeName)) < 2) {
          $collection[$nodeName] = $parser($nodeValue);
          continue;
        }

        $collection[$nodeName][] = $parser($nodeValue);
      }

      return $collection;
    };

    return [
      $xml->getName() => $parser($xml)
    ];
  }

  protected function buildTable(array $data, array $headers, string $label = 'Rates') {
    if (empty($data) || empty($headers)) {
      return [];
    }
    foreach ($headers as $key => $header) {
      $headers_html[] = '<th>' . $header . '</th>';
    }
    $thead = '<thead><tr>' . implode($headers_html) . '</tr></thead>';
    $rows = [];
    foreach ($data as $values) {
      $tds = [];
      foreach ($headers as $key => $header) {
        if (empty($values[$key])) {
          continue;
        }

        if ($key === 'value') {
          $values[$key] = str_replace(',', '.', $values[$key]);
        }

        $tds[] =  "<td data-attribute-$key='$values[$key]'>" . $values[$key] . '</td>';
      }
      $rows[] = '<tr>' .implode($tds) . '</tr>';
    }
    if (!empty($rows)) {
      $tbody = '<tbody>' . implode($rows) . '</tbody>';
    }

    return '<table data-attribute-label="' . $label. '">' . $thead . $tbody . '</table>' ;
  }

  protected function debugDump($dump) {
    echo '<pre>' . var_dump($dump) . '</pre>';
  }
}
