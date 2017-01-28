<?php

require_once dirname(__FILE__) . '/DbConnect.php';

$dm = new DistanceManager();
$dm->setIdService(432891);
$dm->getPoints();

var_dump($dm->getDistance());

class DistanceManager {

    public $idService;
    public $distance = array('distance' => 0, 'time' => 0);

    public function __construct() {
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function setIdService($idService) {
        $this->idService = $idService;
    }

    public function getPoints() {
        $stmt = $this->conn->prepare("SELECT latitude, longitude FROM location WHERE idOrden = ? ORDER BY idLocation");

        $stmt->bind_param("i", $this->idService);
        $stmt->execute();
        $stmt->bind_result($latitude, $longitude);
        $stmt->store_result();

        $numResults = $stmt->num_rows;
        $i = 1;
        $coords = array("startLat" => 0, "startLng" => 0, "endLat" => 0, "endLng" => 0);
        
        while ($stmt->fetch()) {
            if ($i == 1) {
                $coords["startLat"] = $latitude;
                $coords["startLng"] = $longitude;
            } else if ($i == 10) {
                $i = 0;
                $coords["endLat"] = $latitude;
                $coords["endLng"] = $longitude;
                
                $this->getDistanceBeetweenTwoPoints($coords);
            }

            $i++;
        }
        $stmt->close();
    }

    private function getDistanceBeetweenTwoPoints($coords) {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $coords['startLat'] . "," . $coords['startLng'] . "&destinations=" . $coords['endLat'] . "," . $coords['endLng'] . "&mode=driving&language=pl-PL";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        //$dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
        $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
        //$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
        $time = $response_a['rows'][0]['elements'][0]['duration']['value'];

        $this->distance = array(
            "distance" => $this->distance['distance'] + $dist,
            "time" => $this->distance['time'] + $time,
        );
    }

    public function getDistance() {
        return $this->distance;
    }

}
