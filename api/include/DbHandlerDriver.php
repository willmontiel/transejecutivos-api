<?php

require_once 'LoggerHandler.php';
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Will Montiel
 */
class DbHandlerDriver {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }


    /**
     * Checking driver login
     * @param String $username User login username
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($username, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT clave FROM admin WHERE usuario = ? AND nivel_clte = 'conductor'");

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
     * Fetching user data by api key
     * @param String $api_key user api key
     */
    public function getUser($api_key) {
        $stmt = $this->conn->prepare("SELECT id, usuario, codigo FROM admin WHERE api_key = ? AND nivel_clte = 'conductor'");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id, $username, $codigo);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["username"] = $username;
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
     * Fetching user by username
     * @param String $username User username
     */
    public function getUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT id, usuario, nombre, apellido, correo1, correo2, telefono1, telefono2, empresa, api_key, nivel_clte, codigo FROM admin WHERE usuario = ? AND estado = ?");

        $status = "activo";
        $stmt->bind_param("ss", $username, $status);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $lastname, $email1, $email2, $phone1, $phone2, $company, $api_key, $type, $code);
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
            $user["code"] = $code;
            $stmt->close();
            return $user;
        } 
        else {
            return NULL;
        }
    }

    public function searchPendingService($code) {
        $stmt = $this->conn->prepare("SELECT id FROM orden WHERE conductor = ? AND (CD = null OR CD < 0 or CD = '') LIMIT 1");

        $stmt->bind_param("s", $code);

        $service = array();

        if ($stmt->execute()) {
            $stmt->bind_result($id);
            $stmt->fetch();
            
            $service["id"] = $id;

            $stmt->close();  
        } 

        return $service;
    }

    public function getPendingService($id, $code) {
        $sql = "SELECT o.id AS orden_id, 
                        o.referencia,
                        o.fecha_e,
                        o.hora_e,
                        o.fecha_s,
                        o.hora_s1,
                        o.hora_s2,
                        o.hora_s3,
                        o.vuelo,
                        o.aerolinea,
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
                        o.CD,
                        p.id AS passenger_id,
                        p.codigo AS passenger_code,
                        p.name,
                        p.apellido,
                        p.telefono1,
                        p.telefono2,
                        p.correo1,
                        p.correo2
            FROM admin AS a
                    LEFT JOIN orden AS o ON (o.persona_origen = a.codigo)
                    LEFT JOIN pasajeros AS p ON (p.codigo = o.persona_origen) 
            AND o.id = ?
            AND o.conductor = ? 
            AND o.estado != 'cancelar'
            AND (CD = null OR CD < 0 or CD = '')";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("is", $id, $code);

        $service = array();

        if ($stmt->execute()) {
            $stmt->bind_result($orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $observaciones, $orden_estado, $cd, $passenger_id, $passenger_code, $name, $lastName, $phone1, $phone2, $email1, $email2);

            $stmt->fetch();
            
            $service["service_id"] = $orden_id;
            $service["ref"] = $referencia;
            $service["date"] = $fecha_e . "" . $hora_e;
            $service["startDate"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2;
            $service["fly"] = $vuelo;
            $service["aeroline"] = $aerolinea;
            $service["paxCant"] = $cant_pax;
            $service["pax"] = trim($pax2) . ", " . trim($pax3) . ", " . trim($pax4) . ", " . trim($pax5);
            $service["source"] = trim($ciudad_inicio) . ", " . trim($dir_origen);
            $service["destiny"] = trim($ciudad_destino) . ", " . trim($dir_destino);
            $service["observations"] = trim($observaciones);
            $service["status"] = $orden_estado;
            $service["cd"] = $cd;
            $service["passenger_id"] = $passenger_id;
            $service["passenger_code"] = $passenger_code;
            $service["passenger_name"] = $name;
            $service["passenger_lastname"] = $lastName;
            $service["phone"] = trim($phone1) . ", " . trim($phone2);
            $service["email"] = trim($email1) . ", " . trim($email2);

            $stmt->close();  
        } 

        return $service;
    }
}