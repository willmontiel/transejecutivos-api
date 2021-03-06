<?php

require_once 'LoggerHandler.php';
require_once 'ReferenceCreator.php';

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Will Montiel
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* ------------- `admin` table method ------------------ */

    /**
     * Checking user login
     * @param String $username User login username
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($username, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT clave FROM admin WHERE usuario = ? AND nivel_clte != 'conductor'");

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $stmt->bind_result($clave);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the username
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            //if (PassHash::check_password($password_hash, $password)) {
            if ($password == $clave) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }

    /**
     * Fetching user by username
     * @param String $username User username
     */
    public function getUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT id, usuario, nombre, apellido, correo1, correo2, telefono1, telefono2, empresa, api_key, device_token, nivel_clte, codigo, first_time, notifications, request_service, update_order FROM admin WHERE usuario = ? AND estado = ?");

        $status = "activo";
        $stmt->bind_param("ss", $username, $status);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $lastname, $email1, $email2, $phone1, $phone2, $company, $api_key, $device_token, $type, $code, $first_time, $notifications, $request_service, $update_order);
            $stmt->fetch();
            $user = array();
            $user["id"] = $id;
            $user["username"] = $username;
            $user["name"] = $name;
            $user["lastname"] = $lastname;
            $user["email1"] = $email1;
            $user["email2"] = $email2;
            $user["phone1"] = $phone1;
            $user["phone2"] = $phone2;
            $user["type"] = $type;
            $user["company"] = $company;
            $user["api_key"] = $api_key;
            $user["device_token"] = $device_token;
            $user["first_time"] = $first_time;
            $user["notifications"] = $notifications;
            $user["code"] = $code;
            $user["request_service"] = $request_service;
            $user["update_order"] = $update_order;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in admin table
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM admin WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            // $api_key = $stmt->get_result()->fetch_assoc();
            // TODO
            $stmt->bind_result($api_key);
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user data by api key
     * @param String $api_key user api key
     */
    public function getUser($api_key) {
        $stmt = $this->conn->prepare("SELECT id, usuario, nivel_clte, empresa, codigo FROM admin WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id, $username, $type, $company, $codigo);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["username"] = $username;
            $user["type"] = $type;
            $user["company"] = $company;
            $user["code"] = $codigo;
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from admin WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    /**
     * Fetching all user services
     * @param String $code user code
     */
    public function getServices($code) {
        //$log = new LoggerHandler();

        $sql = $this->getServicesSQL(true);

        $stmt = $this->conn->prepare($sql);

        $currentDate = date('m/d/Y');
        $nextdate = date('m/d/Y', strtotime(date('Y-m-d') . ' + 30 days'));

        $stmt->bind_param("sss", $currentDate, $nextdate, $code);
        $stmt->execute();

        $services = $this->modelDataServices($stmt);

        //$services = $stmt->get_result();
        $stmt->close();
        return $services;
    }

    /**
     * Fetching all user services grouped by date
     * @param String $code user code
     */
    public function getServicesGrouped($user) {
        $sql = $this->getServicesSQL(true);
        $query = $user['code'];
        $pax = false;

        if ($user['type'] == "empresa") {
            $sql = $this->getServicesSQLForCompany(true);
            $query = $user['company'];
            $pax = true;
        }

        $stmt = $this->conn->prepare($sql);

        $currentDate = date('m/d/Y');
        $nextdate = date('m/d/Y', strtotime(date('Y-m-d') . ' + 30 days'));

    //$log = new LoggerHandler();
    //$log->writeArray($user);
    //$log->writeString("Current Day: {$currentDate}");
    //$log->writeString("Next Day: {$nextdate}");
    //$log->writeString("Query: {$query}");
    //$log->writeString("SQL: {$sql}");

        $stmt->bind_param("sss", $currentDate, $nextdate, $query);
        $stmt->execute();

        $services = $this->modelGroupedDataServices($stmt, $pax);

        //$services = $stmt->get_result();
        $stmt->close();
        return $services;
    }

    /**
     * Fetching all user services by date
     * @param String $code user code
     * @param String $date service date
     */
    public function getServicesByDate($user, $date) {
        $log = new LoggerHandler();
        $sql = $this->getServicesSQL(false);
        $query = $user['code'];
        $pax = false;
        
        if ($user['type'] == "empresa") {
            $sql = $this->getServicesSQLForCompany(false);
            $query = $user['company'];
            $pax = true;
        }

        //$log->writeString("SQL: " . $sql);
        //$log->writeString("Query: " . $query);
        //$log->writeString("DATE: " . $date);
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("ss", $date, $query);

        $stmt->execute();

        $services = $this->modelGroupedDataServices($stmt, $pax);
        //$services = $stmt->get_result();
        $stmt->close();
        return $services;
    }

    private function getServicesSQLForCompany($between) {
        $date = ($between ? "STR_TO_DATE(o.fecha_s, '%m/%d/%Y') BETWEEN STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(?, '%m/%d/%Y') " : "o.fecha_s = ? ");

        $sql = "SELECT DISTINCT 
                            a.id AS idPassenger,
                            a.nombre AS passenger_name,
                            a.apellido AS passenger_apellido,
                            a.correo1 AS passenger_correo1,
                            a.telefono1 AS passenger_telefono1,
                            o.id AS orden_id, 
                            o.referencia,
                            o.fecha_e,
                            o.hora_e,
                            o.fecha_s,
                            o.hora_s1,
                            o.hora_s2,
                            o.hora_s3,
                            o.vuelo,
                            o.aerolinea,
                            o.empresa,
                            o.cant_pax,
                            o.pax2,
                            o.pax3,
                            o.pax4,
                            o.pax5,
                            o.ciudad_inicio,
                            o.dir_origen,
                            o.ciudad_destino,
                            o.dir_destino,                                            
                            o.obaservaciones,
                            o.estado AS orden_estado,
                            c.id AS conductor_id, 
                            c.nombre AS conductor_nombre, 
                            c.apellido AS conductor_apellido, 
                            c.telefono1 AS conductor_telefono1, 
                            c.telefono2 AS conductor_telefono2, 
                            c.direccion AS conductor_direccion,
                            c.ciudad AS conductor_ciudad,
                            c.email1 AS conductor_email,
                            c.codigo AS conductor_codigo, 
                            c.carro_tipo,
                            c.marca,
                            c.modelo,
                            c.color,
                            c.placa,
                            c.estado,
                            s.id as seguimiento_id,
                            s.b1ha,
                            s.bls,
                            s.pab,
                            s.st
            FROM orden AS o
                    LEFT JOIN pasajeros AS a ON (o.persona_origen = a.codigo)
                    LEFT JOIN conductor AS c ON (c.codigo = o.conductor) 
                    LEFT JOIN seguimiento as s ON (s.referencia = o.referencia)
            WHERE {$date} 
            AND o.empresa = ? 
            AND o.estado != 'cancelar'
            ORDER BY STR_TO_DATE(o.fecha_s, '%m/%d/%Y') ASC";
        return $sql;
    }

    private function getServicesSQL($between) {
        //$date = ($between ? 'o.fecha_s between ? AND ? ' : "o.fecha_s = ? ");
        $date = ($between ? "STR_TO_DATE(o.fecha_s, '%m/%d/%Y') BETWEEN STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(?, '%m/%d/%Y') " : "o.fecha_s = ? ");

        $sql = "SELECT DISTINCT o.id AS orden_id, 
                            o.referencia,
                            o.fecha_e,
                            o.hora_e,
                            o.fecha_s,
                            o.hora_s1,
                            o.hora_s2,
                            o.hora_s3,
                            o.vuelo,
                            o.aerolinea,
                            o.empresa,
                            o.cant_pax,
                            o.pax2,
                            o.pax3,
                            o.pax4,
                            o.pax5,
                            o.ciudad_inicio,
                            o.dir_origen,
                            o.ciudad_destino,
                            o.dir_destino,                                            
                            o.obaservaciones,
                            o.estado AS orden_estado,
                            c.id AS conductor_id, 
                            c.nombre AS conductor_nombre, 
                            c.apellido AS conductor_apellido, 
                            c.telefono1 AS conductor_telefono1, 
                            c.telefono2 AS conductor_telefono2, 
                            c.direccion AS conductor_direccion,
                            c.ciudad AS conductor_ciudad,
                            c.email1 AS conductor_email,
                            c.codigo AS conductor_codigo, 
                            c.carro_tipo,
                            c.marca,
                            c.modelo,
                            c.color,
                            c.placa,
                            c.estado,
                            s.id as seguimiento_id,
                            s.b1ha,
                            s.bls,
                            s.pab,
                            s.st
            FROM admin AS a
                    LEFT JOIN orden AS o ON (o.persona_origen = a.codigo)
                    LEFT JOIN conductor AS c ON (c.codigo = o.conductor) 
                    LEFT JOIN seguimiento as s ON (s.referencia = o.referencia)
            WHERE {$date} 
            AND a.codigo = ? 
            AND o.estado != 'cancelar'
            ORDER BY STR_TO_DATE(o.fecha_s, '%m/%d/%Y') ASC";
        return $sql;
    }

    private function modelDataServices($stmt) {
        $services = array();

        $stmt->bind_result($orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $empresa, $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $obaservaciones, $orden_estado, $conductor_id, $conductor_nombre, $conductor_apellido, $conductor_telefono1, $conductor_telefono2, $conductor_direccion, $conductor_ciudad, $conductor_email, $conductor_codigo, $carro_tipo, $marca, $modelo, $color, $placa, $estado, $seguimiento_id, $b1ha, $bls, $pab, $st);

        while ($stmt->fetch()) {
            $tmp = array();
            //Service information
            $tmp["service_id"] = $orden_id;
            $tmp["ref"] = $referencia;
            $tmp["date"] = $fecha_e . " " . $hora_e;
            $tmp["start_date"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2;
            $tmp["fly"] = $vuelo;
            $tmp["aeroline"] = $aerolinea;
            $tmp["company"] = $empresa;
            $tmp["pax_cant"] = (is_numeric($cant_pax) ? $cant_pax : 1);
            $tmp["pax"] = $this->getPassengers($pax2, $pax3, $pax4, $pax5);
            $tmp["source"] = trim($ciudad_inicio) . ", " . trim($dir_origen);
            $tmp["destiny"] = trim($ciudad_destino) . ", " . trim($dir_destino);
            $tmp["service_status"] = trim($orden_estado);
            $tmp["service_observations"] = $obaservaciones;
            //Passenger information
            //Driver information
            $tmp["driver_id"] = $conductor_id;
            $tmp["driver_code"] = $conductor_codigo;
            $tmp["driver_name"] = $conductor_nombre;
            $tmp["driver_lastname"] = $conductor_apellido;
            $tmp["driver_phone1"] = $conductor_telefono1;
            $tmp["driver_phone2"] = $conductor_telefono2;
            $tmp["driver_address"] = $conductor_direccion;
            $tmp["driver_city"] = $conductor_ciudad;
            $tmp["driver_email"] = $conductor_email;
            $tmp["car_type"] = $carro_tipo;
            $tmp["car_brand"] = $marca;
            $tmp["car_model"] = $modelo;
            $tmp["car_color"] = $color;
            $tmp["car_license_plate"] = $placa;
            $tmp["driver_status"] = $estado;

            $services[] = $tmp;
        }

        return $services;
    }

    private function modelGroupedDataServices($stmt, $pax) {
//    $log = new LoggerHandler();
        $dates = array();
        $data = array(
            'dates' => array(),
            'services' => array(),
        );

        $passenger_id = null;
        $passenger_name = null;
        $passenger_lastname = null;
        $passenger_email1 = null;
        $passenger_phone1 = null;

        if ($pax) {
            $stmt->bind_result($passenger_id, $passenger_name, $passenger_lastname, $passenger_email1, $passenger_phone1, $orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $empresa, $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $obaservaciones, $orden_estado, $conductor_id, $conductor_nombre, $conductor_apellido, $conductor_telefono1, $conductor_telefono2, $conductor_direccion, $conductor_ciudad, $conductor_email, $conductor_codigo, $carro_tipo, $marca, $modelo, $color, $placa, $estado, $seguimiento_id, $b1ha, $bls, $pab, $st);
        } else {
            $stmt->bind_result($orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $empresa, $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $obaservaciones, $orden_estado, $conductor_id, $conductor_nombre, $conductor_apellido, $conductor_telefono1, $conductor_telefono2, $conductor_direccion, $conductor_ciudad, $conductor_email, $conductor_codigo, $carro_tipo, $marca, $modelo, $color, $placa, $estado, $seguimiento_id, $b1ha, $bls, $pab, $st);
        }

        while ($stmt->fetch()) {
            $dlocation = 0;
            $slocation = 0;
            $date = trim($fecha_s);

            $seguimiento_id = trim($seguimiento_id);
            $b1ha = trim($b1ha);
            $bls = trim($bls);
            $pab = trim($pab);
            $st = trim($st);

            if ($seguimiento_id > 0) {
                if (!empty($b1ha) && empty($bls)) {
//        if (!empty($b1ha)) {
                    $dlocation = 1;
                }

                if (!empty($pab) && empty($st)) {
                    $slocation = 1;
                }
            }

            $tmp = array();
            //Service information
            $tmp["service_id"] = $orden_id;
            $tmp["ref"] = $referencia;
            $tmp["date"] = $date . " " . $hora_e;
            $tmp["start_date"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2;
            $tmp["fly"] = $vuelo;
            $tmp["aeroline"] = $aerolinea;
            $tmp["company"] = $empresa;
            $tmp["pax_cant"] = (is_numeric($cant_pax) ? $cant_pax : 1);
            $tmp["pax"] = $this->getPassengers($pax2, $pax3, $pax4, $pax5);
            $tmp["source"] = trim($ciudad_inicio) . ", " . trim($dir_origen);
            $tmp["destiny"] = trim($ciudad_destino) . ", " . trim($dir_destino);
            $tmp["service_status"] = trim($orden_estado);
            $tmp["service_observations"] = $obaservaciones;
            //Passenger information
            $tmp["passenger_id"] = $passenger_id;
            $tmp["passenger_name"] = $passenger_name;
            $tmp["passenger_lastname"] = $passenger_lastname;
            $tmp["passenger_email1"] = $passenger_email1;
            $tmp["passenger_phone1"] = $passenger_phone1;
            //Driver information
            $tmp["driver_id"] = $conductor_id;
            $tmp["driver_code"] = $conductor_codigo;
            $tmp["driver_name"] = $conductor_nombre;
            $tmp["driver_lastname"] = $conductor_apellido;
            $tmp["driver_phone1"] = $conductor_telefono1;
            $tmp["driver_phone2"] = $conductor_telefono2;
            $tmp["driver_address"] = $conductor_direccion;
            $tmp["driver_city"] = $conductor_ciudad;
            $tmp["driver_email"] = $conductor_email;
            $tmp["car_type"] = $carro_tipo;
            $tmp["car_brand"] = $marca;
            $tmp["car_model"] = $modelo;
            $tmp["car_color"] = $color;
            $tmp["car_license_plate"] = $placa;
            $tmp["driver_status"] = $estado;
            $tmp['driver_location'] = $dlocation;
            $tmp['share_location'] = $slocation;

            if (in_array($date, $dates)) {
                $key = array_search($date, $dates);
                $data['services'][$key][] = $tmp;
            } else {
                $newKey = count($dates);
                $dates[$newKey] = $date;
                $data['services'][$newKey][] = $tmp;
            }
        }

        $data['dates'] = $dates;

        return $data;
    }

    private function getPassengers($pax2, $pax3, $pax4, $pax5) {
        $p = array();
        $pax = null;

        $pax2 = trim($pax2);
        $pax3 = trim($pax3);
        $pax4 = trim($pax4);
        $pax5 = trim($pax5);

        if (!empty($pax2) && $pax2 != "Seleccione una...") {
            $p[] = $pax2;
        }

        if (!empty($pax3) && $pax3 != "Seleccione una...") {
            $p[] = $pax3;
        }

        if (!empty($pax4) && $pax4 != "Seleccione una...") {
            $p[] = $pax4;
        }

        if (!empty($pax5) && $pax5 != "Seleccione una...") {
            $p[] = $pax5;
        }

        if (count($p) > 0) {
            $pax = implode(", ", $p);
        }

        return $pax;
    }

    /**
     * Create service/order
     */
    public function requestService($user, $data) {
        $log = new LoggerHandler();
        $refc = new ReferenceCreator();
        $ref = $refc->getReference();

        $date = date("m/d/Y");
        $time = date("H:i:s");
        $serviceType = "Pasajero (s)";
        $hour = explode(":", $data->time);

        $stmt = $this->conn->prepare("INSERT INTO orden 
                                                  (referencia, elaboradopor, fecha_e, hora_e, empresa, fecha_s, hora_s1, hora_s2, tipo_s, vehiculo_s, cant_pax, persona_origen, ciudad_inicio, dir_origen, ciudad_destino, dir_destino, aerolinea, vuelo, obaservaciones) 
                                                  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


        $stmt->bind_param("sssssssssssssssssss", $ref, $user['code'], $date, $time, $user["company"], $data->date, $hour[0], $hour[1], $serviceType, $data->carType, $data->passengers, $user['codigo'], $data->startCity, $data->startAddress, $data->endCity, $data->endAddress, $data->aeroline, $data->fly, $data->observations);

        if ($stmt->execute()) {
            $stmt->close();
            file_get_contents("http://www.transportesejecutivos.com/admin/correos/crear_orden_admin.php?codigo={$ref}&proceso=5&app=1");
            return true;
        }

        $log->writeString("Error " . $stmt->error);
        $stmt->close();
        return false;
    }


    /**
     * List all car types.
     */
    public function getCarTypes() {
        $stmt = $this->conn->prepare("SELECT id, nombre FROM tipo_vehiculo");

        $data = array(
            'data' => array(),
        );

        if ($stmt->execute()) {
            
            $stmt->bind_result($id, $name);

            while ($stmt->fetch()) {
                $carType = array(
                    "id" => $id,
                    "name" => $name
                );

                $data['data'][] = $carType;
            }

            $stmt->close();
        } 

        return $data;
    }

    /**
     * List all aerolines.
     */
    public function getAerolines() {
        $stmt = $this->conn->prepare("SELECT id, nombre, abreviacion FROM aerolinea");

        $data = array(
            'data' => array(),
        );

        if ($stmt->execute()) {
            
            $stmt->bind_result($id, $name, $abb);

            while ($stmt->fetch()) {
                $aeroline = array(
                    "id" => $id,
                    "name" => $name . " ({$abb})"
                );

                $data['data'][] = $aeroline;
            }

            $stmt->close();
        } 

        return $data;
    }

    /**
     * List all cities.
     */
    public function getCities() {
        $stmt = $this->conn->prepare("SELECT id, nombre, departamento FROM ciudad");

        $data = array(
            'data' => array(),
        );

        if ($stmt->execute()) {
            
            $stmt->bind_result($id, $name, $state);

            while ($stmt->fetch()) {
                $city = array(
                    "id" => $id,
                    "name" => $name . " ({$state})"
                );

                $data['data'][] = $city;
            }

            $stmt->close();
        } 

        return $data;
    }

    /**
     * update apikey 
     * @param String $username user username
     */
    public function updateApiKey($username) {
        $apikey = $this->generateApiKey();
        $stmt = $this->conn->prepare("UPDATE admin SET api_key = ? WHERE usuario = ?");
        $stmt->bind_param("ss", $apikey, $username);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function updateOrderPermissionStatus($id, $val) {
        $stmt = $this->conn->prepare("UPDATE admin SET update_order = ? WHERE id = ?");
        $stmt->bind_param("ii", $val, $id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function changePassword($username, $password) {
        $apikey = $this->generateApiKey();
        $stmt = $this->conn->prepare("UPDATE admin SET clave = ?, api_key = ?, first_time = 0 WHERE usuario = ?");
        $stmt->bind_param("sss", $password, $apikey, $username);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * update user profile
     * @param String $username user username
     */
    public function updateProfile($username, $name, $lastname, $email1, $email2, $phone1, $phone2, $password, $notifications, $token) {
        $log = new LoggerHandler();
        $pass = trim($password);
        $email2 = trim($email2);
        $phone2 = trim($phone2);
        $passSQL = (empty($pass) ? "" : ", clave = ?");
        $tokenSQL = (empty($token) ? "" : ", device_token = ?");
        $notifications = (empty($notifications) ? 0 : $notifications);
        $now = date("d/M/Y H:i");


        $sql = "UPDATE admin SET nombre = ?, apellido = ?, correo1 = ? , correo2 = ?, telefono1 = ?, telefono2 = ?, notifications = ?, fecha_edicion = '{$now}' {$passSQL} {$tokenSQL} WHERE usuario = ?";

        $stmt = $this->conn->prepare($sql);

        if (empty($pass) && empty($token)) {
            $stmt->bind_param("ssssssss", $name, $lastname, $email1, $email2, $phone1, $phone2, $notifications, $username);
        } else if (!empty($pass) && empty($token)) {
            $stmt->bind_param("sssssssss", $name, $lastname, $email1, $email2, $phone1, $phone2, $notifications, $pass, $username);
        } else if (!empty($token) && empty($pass)) {
            $stmt->bind_param("sssssssss", $name, $lastname, $email1, $email2, $phone1, $phone2, $notifications, $token, $username);
        } else if (!empty($token) && !empty($pass)) {
            $stmt->bind_param("sssssssss", $name, $lastname, $email1, $email2, $phone1, $phone2, $notifications, $pass, $token, $username);
        }

        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * This function resets the user password 
     * @param String $username user username
     * @param String $password new user password
     */
    public function resetPassword($username, $password, $idLink) {
        $stmt = $this->conn->prepare("UPDATE admin SET clave = ? WHERE usuario = ?");
        $stmt->bind_param("ss", $password, $username);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        if ($num_affected_rows > 0) {
            $stmt = $this->conn->prepare("DELETE FROM recoverpassword WHERE idLink = ?");
            $stmt->bind_param("s", $idLink);
            $stmt->execute();
            $num_affected_rows = $stmt->affected_rows;
            $stmt->close();
            return $num_affected_rows > 0;
        }

        return false;
    }

    /**
     * Validate if is a valid link
     * @param string $idLink
     * @return boolean
     */
    public function validateLink($idLink) {
        $time = strtotime("-30 minutes");

        $stmt = $this->conn->prepare("SELECT username, date FROM recoverpassword WHERE idLink = ?");
        $stmt->bind_param("s", $idLink);

        if ($stmt->execute()) {
            $stmt->bind_result($username, $date);
            $stmt->fetch();

            $time = strtotime("-30 minutes");

            if ($date <= $time || $date >= $time) {
                $user = array();
                $user["username"] = $username;
                $stmt->close();
                return $user;
            }
        }

        return false;
    }

    /**
     * Generate a link for recover password
     * @param string $username
     * @return boolean
     */
    public function recoverPassword($username) {
        $user = $this->getUserByUsername($username);

        if ($user == NULL) {
            return false;
        }

        $cod = uniqid();
        $time = time();
        $stmt = $this->conn->prepare("INSERT INTO recoverpassword(idLink, username, date) VALUES (?,?,?)");
        $stmt->bind_param("ssi", $cod, $username, $time);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            if ($this->sendMail($cod, $user['mail1'])) {
                return true;
            }
        }

        return false;
    }

    public function getPrelocation($id) {
        $response = array(
            "location" => 0,
            "latitude" => 0,
            "longitude" => 0
        );

        $stmt = $this->conn->prepare("SELECT latitude, longitude FROM prelocation WHERE idOrden = ? ORDER BY idPrelocation DESC LIMIT 1");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->bind_result($latitude, $longitude);
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                $stmt->close();

                $response["latitude"] = $latitude;
                $response["longitude"] = $longitude;
                $response["location"] = 1;
            } else {
                $stmt->close();
            }
        }

        return $response;
    }

    /**
     * Send a mail with instructions for to recover password
     * @param string $code
     * @param string $email
     * @return boolean
     */
    private function sendMail($code, $email) {
        $link = '<a href="http://www.transportesejecutivos.com/recoverpass/index.php?code=' . $code . '">Reestablecer contrase&ntilde;a</a>';
        $html = '<html> <head></head> <body> <h2> <span style="font-family: Helvetica;"> <strong>Estimado usuario:</strong> </span> </h2> <br><table> <tbody> <tr> <td> <span style="font-family: Helvetica;"> Ha olvidado su contrase&ntilde;a, para reestablecerla por favor haga clic en el siguiente enlace, </span> <br><br></td></tr><tr> <td> <span style="font-family: Helvetica;"> %tmpurl% </span> </td></tr><tr> <td> <br><span style="font-family: Helvetica;"> Si no ha solicitado reestablecer su contrase&ntilde;a, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, cont&aacute;ctenos a trav&eacute;s de este correo <a href="mailto:info@transportesejecutivos.com">info@transportesejecutivos.com.</a> </span> </td></tr><tr> <td> <br><img src="http://www.transportesejecutivos.com/recoverpass/images/complete-logo.png" alt="Transportes Ejecutivos"/> </td></tr></tbody> </table> </body></html>';

        $para = $email;
        $titulo = 'Instrucciones para recuperar la contraseña de acceso a Transportes Ejecutivos';
        $mensaje = str_replace("%tmpurl%", $link, $html);


        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: <info@transportesejecutivos.com>' . "\r\n";

        return mail($para, $titulo, $mensaje, $headers);
    }

}

?>
