<?php

require_once 'LoggerHandler.php';

//$mapCreator = new MapCreator();
//$url = $mapCreator->getAddressByLatIng("3.407406,-76.5530109");
//
//echo $url;

class MapCreator {

  const GOOGLE_MAPS_API_KEY = "AIzaSyDzjFoNaHh_kH4gAJ2JkoY3Xlr1AH8Nlyk";
  const GOOGLE_MAPS_START_MARKER = "markers=color:blue|label:I|";
  const GOOGLE_MAPS_END_MARKER = "markers=color:green|label:F|";
  const GOOGLE_MAPS_ROUTE_COLOR = "0xff000090";
  const GOOGLE_MAPS_ROUTE_WEIGHT = "2";
  const GOOGLE_MAPS_SIZE = "640x640";
  const GOOGLE_MAPS_URL = "https://maps.googleapis.com/maps/api/staticmap?";
  const MAP_URL = "http://www.transportesejecutivos.com/maps/";
  
  const GOOGLE_GEOCODE_URL = "https://maps.googleapis.com/maps/api/geocode/json?";
  const GOOGLE_GEOCODE_LOCATION_TYPE = "ROOFTOP";
  const GOOGLE_GEOCODE_LOCATION_STREET_ADDRESS = "street_address";
  
  public function __construct() {
    require_once dirname(__FILE__) . '/DbConnect.php';
    // opening db connection
    $db = new DbConnect();
    $this->conn = $db->connect();
  }

  public function createMap($name, $start, $end, $points) {
    $url = MapCreator::GOOGLE_MAPS_URL . MapCreator::GOOGLE_MAPS_START_MARKER . $start . "&" . MapCreator::GOOGLE_MAPS_END_MARKER . $end . "&path=color:" . MapCreator::GOOGLE_MAPS_ROUTE_COLOR . "|weight:" . MapCreator::GOOGLE_MAPS_ROUTE_WEIGHT . "|" . $points . "&size=" . MapCreator::GOOGLE_MAPS_SIZE . "&key=" . MapCreator::GOOGLE_MAPS_API_KEY;

    $log = new LoggerHandler();
    $log->writeString("URL: {$url}");

    if (!file_put_contents("../../maps/{$name}.png", file_get_contents($url))) {
      $log->writeString("No se pudo guardar la imagen de la ubicación de google maps, referncia: {$name}");
    }
    
    $url = MapCreator::MAP_URL . "{$name}.png";
    $log->writeString("URL: {$url}");
    return $url;
  }

  public function getMapUrl($start, $end, $points) {
    $url = MapCreator::GOOGLE_MAPS_URL . MapCreator::GOOGLE_MAPS_START_MARKER . $start . "&" . MapCreator::GOOGLE_MAPS_END_MARKER . $end . "&path=color:" . MapCreator::GOOGLE_MAPS_ROUTE_COLOR . "|weight:" . MapCreator::GOOGLE_MAPS_ROUTE_WEIGHT . "|" . $points . "&size=" . MapCreator::GOOGLE_MAPS_SIZE . "&key=" . MapCreator::GOOGLE_MAPS_API_KEY;

    $log = new LoggerHandler();
    $log->writeString("URL: {$url}");

    return $url;
  }
  
  public function getAddressByLatIng($latIng) {
    $log = new LoggerHandler();
    $url = MapCreator::GOOGLE_GEOCODE_URL . "latlng=" . $latIng . "&location_type=" . MapCreator::GOOGLE_GEOCODE_LOCATION_TYPE . "&result_type=" . MapCreator::GOOGLE_GEOCODE_LOCATION_STREET_ADDRESS ."&key=" . MapCreator::GOOGLE_MAPS_API_KEY;
    $log->writeString("URL: {$url}");
    $result = file_get_contents($url);
    $res = json_decode($result);
    $address = (isset($res->results[0]->formatted_address) ? $res->results[0]->formatted_address : null);
    $log->writeString("Address: {$address}");
    return $address;
  }

  public function findLocationPoints($id, $pre = false) {
    $points = array();
    $table = "location";
    $order = "idLocation";
    if ($pre) {
      $table = "prelocation";
      $order = "idPrelocation";
    }
    
    $stmt = $this->conn->prepare("SELECT latitude, longitude FROM {$table} WHERE idOrden = ? ORDER BY {$order}");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->bind_result($latitude, $longitude);

    $stmt->store_result();

    $numResults = $stmt->num_rows;

    $j = 1;
    $i = 0;
    $f = true;

    while ($stmt->fetch()) {
      $latLan = "{$latitude},{$longitude}";
      
      if ($numResults > 1900) {  
        if (!in_array($latLan, $points)) {
          if ($f) {
            $points[] = $latLan;
            $f = false;
          } else if ($i == 10) {
            $i = 0;
            $points[] = $latLan;
          } else if ($j == $numResults) {
            $points[] = $latLan;
          }

          $j++;
          $i++;
        } 
      }
      else {
        if (!in_array($latLan, $points)) {
          $points[] = $latLan;
        }
      }
    }

    $stmt->close();

    //return implode("|", $points);
    return $points;
  }

}
