<?php

require_once 'LoggerHandler.php';

class MapCreator {

  const GOOGLE_MAPS_API_KEY = "AIzaSyDzjFoNaHh_kH4gAJ2JkoY3Xlr1AH8Nlyk";
  const GOOGLE_MAPS_START_MARKER = "markers=color:blue|label:I|";
  const GOOGLE_MAPS_END_MARKER = "markers=color:green|label:F|";
  const GOOGLE_MAPS_ROUTE_COLOR = "0xff000090";
  const GOOGLE_MAPS_ROUTE_WEIGHT = "2";
  const GOOGLE_MAPS_SIZE = "640x640";

  public function __construct() {
    require_once dirname(__FILE__) . '/DbConnect.php';
    // opening db connection
    $db = new DbConnect();
    $this->conn = $db->connect();
  }

  public function createMap($name, $points) {
    //$url = urlencode("https://maps.googleapis.com/maps/api/staticmap?path=color:" . MapCreator::GOOGLE_MAPS_ROUTE_COLOR . "|weight:" . MapCreator::GOOGLE_MAPS_ROUTE_WEIGHT . "|" . $points . "&size=" . MapCreator::GOOGLE_MAPS_SIZE . "&key=" . MapCreator::GOOGLE_MAPS_API_KEY);
    $url = "https://maps.googleapis.com/maps/api/staticmap?path=color:" . MapCreator::GOOGLE_MAPS_ROUTE_COLOR . "|weight:" . MapCreator::GOOGLE_MAPS_ROUTE_WEIGHT . "|" . $points . "&size=" . MapCreator::GOOGLE_MAPS_SIZE . "&key=" . MapCreator::GOOGLE_MAPS_API_KEY;

    $log = new LoggerHandler();
    $log->writeString("URL: {$url}");

    if (!file_put_contents("../../maps/{$name}.png", file_get_contents($url))) {
      $log->writeString("No se pudo guardar la imagen de la ubicación de google maps, referncia: {$name}");
    }
  }

  public function getMapUrl($start, $end, $points) {
    $url = "https://maps.googleapis.com/maps/api/staticmap?" . MapCreator::GOOGLE_MAPS_START_MARKER . $start . "&" . MapCreator::GOOGLE_MAPS_END_MARKER . $end . "&path=color:" . MapCreator::GOOGLE_MAPS_ROUTE_COLOR . "|weight:" . MapCreator::GOOGLE_MAPS_ROUTE_WEIGHT . "|" . $points . "&size=" . MapCreator::GOOGLE_MAPS_SIZE . "&key=" . MapCreator::GOOGLE_MAPS_API_KEY;

    $log = new LoggerHandler();
    $log->writeString("URL: {$url}");

    return $url;
  }

  public function findLocationPoints($id) {
    $points = array();

    $stmt = $this->conn->prepare("SELECT latitude, longitude FROM location WHERE idOrden = ?");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->bind_result($latitude, $longitude);

    $stmt->store_result();

    $numResults = $stmt->num_rows;

    $j = 1;
    $i = 0;
    $f = true;

    while ($stmt->fetch()) {
      if ($numResults > 20) {
        if ($f) {
          $points[] = "{$latitude},{$longitude}";
          $f = false;
        } else if ($i == 10) {
          $i = 0;
          $points[] = "{$latitude},{$longitude}";
        } else if ($j == $numResults) {
          $points[] = "{$latitude},{$longitude}";
        }

        $j++;
        $i++;
      } else {
        $points[] = "{$latitude},{$longitude}";
      }
    }

    $stmt->close();

    //return implode("|", $points);
    return $points;
  }

}
