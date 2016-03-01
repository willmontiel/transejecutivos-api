<?php

require_once 'LoggerHandler.php';
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
        $stmt = $this->conn->prepare("SELECT clave FROM admin WHERE usuario = ?");

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
        $stmt = $this->conn->prepare("SELECT id, usuario, nombre, apellido, correo1, correo2, empresa, api_key, nivel_clte FROM admin WHERE usuario = ? AND estado = ?");

        $status = "activo";
        $stmt->bind_param("ss", $username, $status);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $lastname, $mail1, $mail2, $company, $api_key, $type);
            $stmt->fetch();
            $user = array();
            $user["id"] = $id;
            $user["username"] = $username;
            $user["name"] = $name;
            $user["lastname"] = $lastname;
            $user["mail1"] = $mail1;
            $user["mail2"] = $mail2;
            $user["type"] = $type;
            $user["company"] = $company;
            $user["api_key"] = $api_key;
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
        $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
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
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUser($api_key) {
        $stmt = $this->conn->prepare("SELECT id, usuario, nivel_clte, empresa FROM admin WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id, $username, $type, $empresa);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["username"] = $username;
            $user["type"] = $type;
            $user["company"] = $empresa;
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
    * @param String $company company of the user
    */
    public function getServices($company) {
        $log = new LoggerHandler();
        $services = array();
        
        $stmt = $this->conn->prepare("SELECT o.id AS orden_id, 
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
                                            o.tipo_s,
                                            o.cant_pax,
                                            o.representando,
                                            o.ciudad_inicio,
                                            o.dir_origen,
                                            o.ciudad_destino,
                                            o.dir_destino,                                            
                                            o.obaservaciones,
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
                                            p.id AS pasajeros_id, 
                                            p.codigo AS pasajeros_codigo, 
                                            p.nombre AS pasajeros_nombre, 
                                            p.apellido AS pasajeros_apellido, 
                                            p.telefono1 AS pasajeros_telefono1, 
                                            p.telefono2 AS pasajeros_telefono2, 
                                            p.empresa AS pasajeros_empresa,
                                            p.correo1 AS pasajeros_correo1,
                                            p.direccion AS pasajeros_direccion,
                                            p.ciudad AS pasajeros_ciudad
                                            
                                            FROM orden AS o
                                                LEFT JOIN conductor AS c ON (c.codigo = o.conductor) 
                                                LEFT JOIN pasajeros AS p ON (p.codigo = o.persona_origen)
                                            WHERE o.fecha_s = ? 
                                            AND o.empresa = ? 
                                            ORDER BY o.hora_s1 ASC, o.hora_s2 ASC");

        //$currentDate =  date('m/d/Y');
        $currentDate =  "11/28/2012";
        
        $stmt->bind_param("ss", $currentDate, $company);
        $stmt->execute();

        $stmt->bind_result($orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $empresa, $tipo_s, $cant_pax, $representando, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $obaservaciones,
 $conductor_id, $conductor_nombre, $conductor_apellido, $conductor_telefono1, $conductor_telefono2, $conductor_direccion, $conductor_ciudad, $conductor_email, $conductor_codigo, $carro_tipo, $marca, $modelo, $color, $placa, $estado, $pasajeros_id, $pasajeros_codigo, $pasajeros_nombre, $pasajeros_apellido, $pasajeros_telefono1, $pasajeros_telefono2, $pasajeros_empresa, $pasajeros_correo1, $pasajeros_direccion, $pasajeros_ciudad);

        while ($stmt->fetch()) {
            $tmp = array();
            //Service information
            $tmp["service_id"] = $orden_id;
            $tmp["ref"] = $referencia;
            $tmp["date"] = $fecha_e . " " . $hora_e;
            $tmp["start_date"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2 . ":" . $hora_s3;
            $tmp["end_date"] = null;
            $tmp["fly"] = $vuelo;
            $tmp["aeroline"] = $aerolinea;
            $tmp["company"] = $empresa;
            $tmp["passenger_type"] = $tipo_s;
            $tmp["pax_cant"] = $cant_pax;
            $tmp["represent"] = $representando;
            //$tmp["source"] = $ciudad_inicio . ", " . $dir_origen;
            //$tmp["destiny"] = $ciudad_destino . ", " . $dir_destino;
            $tmp["source"] = $ciudad_inicio;
            $tmp["destiny"] = $ciudad_destino;
            $tmp["service_observations"] = $obaservaciones;
            //Driver information
            $tmp["driver_id"] = $conductor_id;
            $tmp["driver_code"] = $conductor_codigo;
            $tmp["driver_name"] = $conductor_nombre;
            $tmp["driver_lastname"] = $conductor_apellido;
            $tmp["driver_phone1"] = $conductor_telefono1;
            $tmp["driver_phone1"] = $conductor_telefono2;
            $tmp["driver_address"] = $conductor_direccion;
            $tmp["driver_city"] = $conductor_ciudad;
            $tmp["driver_email"] = $conductor_email;
            $tmp["car_type"] = $carro_tipo;
            $tmp["car_brand"] = $marca;
            $tmp["car_model"] = $modelo;
            $tmp["car_color"] = $color;
            $tmp["car_license_plate"] = $placa;
            $tmp["driver_status"] = $estado;
            //Passenger information
            $tmp["passenger_id"] = $pasajeros_id;
            $tmp["passenger_code"] = $pasajeros_codigo;
            $tmp["passenger_name"] = $pasajeros_nombre;
            $tmp["passenger_lastname"] = $pasajeros_apellido;
            $tmp["passenger_company"] = $pasajeros_empresa;
            $tmp["passenger_phone1"] = $pasajeros_telefono1;
            $tmp["passenger_phone2"] = $pasajeros_telefono2;
            $tmp["passenger_email"] = $pasajeros_correo1;
            $tmp["passenger_address"] = $pasajeros_direccion;
            $tmp["passenger_city"] = $pasajeros_ciudad;

            $services[] = $tmp;
        }

        //$services = $stmt->get_result();
        $stmt->close();
        return $services;
    }


    /**
    * Fetching all user services by date
    * @param String $company company of the user
    * @param String $date service date
    */
    public function getServicesByDate($company, $date) {
        $log = new LoggerHandler();
        $services = array();
        
        $stmt = $this->conn->prepare("SELECT o.id AS orden_id, 
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
                                            o.tipo_s,
                                            o.cant_pax,
                                            o.representando,
                                            o.ciudad_inicio,
                                            o.dir_origen,
                                            o.ciudad_destino,
                                            o.dir_destino,                                            
                                            o.obaservaciones,
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
                                            p.id AS pasajeros_id, 
                                            p.codigo AS pasajeros_codigo, 
                                            p.nombre AS pasajeros_nombre, 
                                            p.apellido AS pasajeros_apellido, 
                                            p.telefono1 AS pasajeros_telefono1, 
                                            p.telefono2 AS pasajeros_telefono2, 
                                            p.empresa AS pasajeros_empresa,
                                            p.correo1 AS pasajeros_correo1,
                                            p.direccion AS pasajeros_direccion,
                                            p.ciudad AS pasajeros_ciudad
                                            
                                            FROM orden AS o
                                                LEFT JOIN conductor AS c ON (c.codigo = o.conductor) 
                                                LEFT JOIN pasajeros AS p ON (p.codigo = o.persona_origen)
                                            WHERE o.fecha_s = ? 
                                            AND o.empresa = ? 
                                            ORDER BY o.hora_s1 ASC, o.hora_s2 ASC");
        
        $stmt->bind_param("ss", $date, $company);
        $stmt->execute();

        $stmt->bind_result($orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $empresa, $tipo_s, $cant_pax, $representando, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $obaservaciones,
 $conductor_id, $conductor_nombre, $conductor_apellido, $conductor_telefono1, $conductor_telefono2, $conductor_direccion, $conductor_ciudad, $conductor_email, $conductor_codigo, $carro_tipo, $marca, $modelo, $color, $placa, $estado, $pasajeros_id, $pasajeros_codigo, $pasajeros_nombre, $pasajeros_apellido, $pasajeros_telefono1, $pasajeros_telefono2, $pasajeros_empresa, $pasajeros_correo1, $pasajeros_direccion, $pasajeros_ciudad);

        while ($stmt->fetch()) {
            $tmp = array();
            //Service information
            $tmp["service_id"] = $orden_id;
            $tmp["ref"] = $referencia;
            $tmp["date"] = $fecha_e . " " . $hora_e;
            $tmp["start_date"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2 . ":" . $hora_s3;
            $tmp["end_date"] = null;
            $tmp["fly"] = $vuelo;
            $tmp["aeroline"] = $aerolinea;
            $tmp["company"] = $empresa;
            $tmp["passenger_type"] = $tipo_s;
            $tmp["pax_cant"] = $cant_pax;
            $tmp["represent"] = $representando;
            //$tmp["source"] = $ciudad_inicio . ", " . $dir_origen;
            //$tmp["destiny"] = $ciudad_destino . ", " . $dir_destino;
            $tmp["source"] = $ciudad_inicio;
            $tmp["destiny"] = $ciudad_destino;
            $tmp["service_observations"] = $obaservaciones;
            //Driver information
            $tmp["driver_id"] = $conductor_id;
            $tmp["driver_code"] = $conductor_codigo;
            $tmp["driver_name"] = $conductor_nombre;
            $tmp["driver_lastname"] = $conductor_apellido;
            $tmp["driver_phone1"] = $conductor_telefono1;
            $tmp["driver_phone1"] = $conductor_telefono2;
            $tmp["driver_address"] = $conductor_direccion;
            $tmp["driver_city"] = $conductor_ciudad;
            $tmp["driver_email"] = $conductor_email;
            $tmp["car_type"] = $carro_tipo;
            $tmp["car_brand"] = $marca;
            $tmp["car_model"] = $modelo;
            $tmp["car_color"] = $color;
            $tmp["car_license_plate"] = $placa;
            $tmp["driver_status"] = $estado;
            //Passenger information
            $tmp["passenger_id"] = $pasajeros_id;
            $tmp["passenger_code"] = $pasajeros_codigo;
            $tmp["passenger_name"] = $pasajeros_nombre;
            $tmp["passenger_lastname"] = $pasajeros_apellido;
            $tmp["passenger_company"] = $pasajeros_empresa;
            $tmp["passenger_phone1"] = $pasajeros_telefono1;
            $tmp["passenger_phone2"] = $pasajeros_telefono2;
            $tmp["passenger_email"] = $pasajeros_correo1;
            $tmp["passenger_address"] = $pasajeros_direccion;
            $tmp["passenger_city"] = $pasajeros_ciudad;

            $services[] = $tmp;
        }

        //$services = $stmt->get_result();
        $stmt->close();
        return $services;
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
}

?>
