<?php

namespace hotels\service;
use hotels\model\HotelModel;
use hotels\view\HotelView;

class HotelService {
  public function getHotel(string $az = ''): array|false {
    $hotel_model = new HotelModel();
    return $hotel_model->getHotel($az);
  }

  public function listHotels(): string {
    $hotel_model = new HotelModel();
    $hotel_view = new HotelView();
    $hotel_view->setModel($hotel_model);
    return $hotel_view->list();
  }

  public function getComments(string $szalloda_az) {
    $hotel_model = new HotelModel();
    $hotel_view = new HotelView();
    $hotel_view->setModel($hotel_model);
    return $hotel_view->getComments($szalloda_az);
  }

  public function sortByName(string $sort_by) {
    return '';
  }

}
