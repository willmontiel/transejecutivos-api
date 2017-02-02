<?php

require_once dirname(__FILE__) . '/DbConnect.php';

//$dm = new DistanceManager();
//$dm->setIdService(433552);
//$dm->getPoints();
//
//var_dump($dm->getDistance());
//
//echo PHP_EOL;
//
//echo $dm->getTimeDiff("04:36", "04:49");

class DistanceManager {

    const GOOGLE_MAPS_API_KEY = "AIzaSyBYVnIyRFZKK_nH_GZj4AFC9uNsjuBAH_4";
    const GOOGLE_MAPS_URL = "https://maps.googleapis.com/maps/api/distancematrix/json?";

    public $idService;
    public $reference;
    public $service;
    public $distance = array('distance' => 0, 'time' => 0);

    public function __construct() {
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
    
    public function setService($service) {
        $this->service = $service;
    }

    public function setIdService($idService) {
        $this->idService = $idService;
    }

    public function setReference($reference) {
        $this->reference = $reference;
    }

    public function getTimeDiff($dtime, $atime) {
        $nextDay = $dtime > $atime ? 1 : 0;
        $dep = explode(':', $dtime);
        $arr = explode(':', $atime);
        $diff = abs(mktime($dep[0], $dep[1], 0, date('n'), date('j'), date('y')) - mktime($arr[0], $arr[1], 0, date('n'), date('j') + $nextDay, date('y')));
        $hours = floor($diff / (60 * 60));
        $mins = floor(($diff - ($hours * 60 * 60)) / (60));
        $secs = floor(($diff - (($hours * 60 * 60) + ($mins * 60))));
        if (strlen($hours) < 2) {
            $hours = "0" . $hours;
        }
        if (strlen($mins) < 2) {
            $mins = "0" . $mins;
        }
        if (strlen($secs) < 2) {
            $secs = "0" . $secs;
        }
        
        $mins = ($mins > 0 ? $mins . " min" : "");
        $hours = ($hours > 0 ? $hours . " h " : "");
        
        return $hours . $mins;
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

    public function getDistanceBeetweenTwoPoints($coords) {
        $url = DistanceManager::GOOGLE_MAPS_URL . "origins=" . $coords['startLat'] . "," . $coords['startLng'] . "&destinations=" . $coords['endLat'] . "," . $coords['endLng'] . "&mode=driving&language=pl-PL&key=" . DistanceManager::GOOGLE_MAPS_API_KEY;
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

    public function saveDistanceAndTime() {
        $stmt = $this->conn->prepare("UPDATE seguimiento SET distance = ?, time = ? WHERE referencia = ?");
        $stmt->bind_param("sss", $this->distance['distance'], $this->distance['time'], $this->reference);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }

    public function getDistance() {
        $distance = ($this->distance['distance'] / 1000);
        $time = ($this->distance['time'] / 60);

        $distance = round($distance, 1) . " km";
        $time = round($time);

        if ($time > 60) {
            $time = ($time / 60);
            $time = round($time, 1);
            $t = explode(".", $time);
            $time = $t[0] . " h" . (isset($t[1]) ? $t[1] . " min" : "");
        } else {
            $time = $time . " min";
        }

        $this->distance = array(
            "distance" => $distance,
            "time" => $time,
        );

        return $this->distance;
    }

}
