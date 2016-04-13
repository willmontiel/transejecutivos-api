<?php

require_once 'LoggerHandler.php';

$m = new MapCreator();
$points = $m->findLocationPoints();
$m->createMap("W436983-1", $points);

class MapCreator {
    const GOOGLE_MAPS_API_KEY = "AIzaSyDzjFoNaHh_kH4gAJ2JkoY3Xlr1AH8Nlyk";
    const GOOGLE_MAPS_ROUTE_COLOR = "0xff0000ff";
    const GOOGLE_MAPS_ROUTE_WEIGHT = "5";
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
            throw new Exception("No se pudo guardar la imagen de la ubicación de google maps");
        }
    }
    
    public function findLocationPoints() {
        $log = new LoggerHandler();
        $points = array();
        
        $stmt = $this->conn->prepare("SELECT latitude, longitude FROM location WHERE idOrden = ?");
        
        $id = 102817;
        
        $stmt->bind_param("i", $id);

        $stmt->execute();
        
        $stmt->bind_result($latitude, $longitude);
        
        $stmt->store_result();
        
        $numResults = $stmt->num_rows;
        
        $log->writeString("POINTS: " . $numResults);
     
        $j = 1;
        $i = 0;
        $f = true;
        
        while ($stmt->fetch()) {
            if ($f) {
                $points[] = "{$latitude},{$longitude}";
                $f = false;
            }
            else if ($i == 18) {
                $i = 0;
                $points[] = "{$latitude},{$longitude}";
            }
            else if ($j == $numResults) {
                $points[] = "{$latitude},{$longitude}";
            }
        
            $j++;
            $i++;
        }
        
        $stmt->close(); 
        
        $log->writeString("POINTS: " . implode("|", $points));
        
        return implode("|", $points);
    }
}