<?php

require_once 'LoggerHandler.php';
//require_once 'MailSender.php';
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
        $stmt = $this->conn->prepare("SELECT id, usuario, codigo, nombre, apellido FROM admin WHERE api_key = ? AND nivel_clte = 'conductor'");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id, $username, $codigo, $nombre, $apellido);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["username"] = $username;
            $user["code"] = $codigo;
            $user["name"] = $nombre;
            $user["lastname"] = $apellido;
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
        $stmt = $this->conn->prepare("SELECT id, usuario, nombre, apellido, correo1, correo2, telefono1, telefono2, empresa, api_key, nivel_clte, codigo, first_time FROM admin WHERE usuario = ? AND estado = ?");

        $status = "activo";
        $stmt->bind_param("ss", $username, $status);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $lastname, $email1, $email2, $phone1, $phone2, $company, $api_key, $type, $code, $first_time);
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
            $user["first_time"] = $first_time;
            
            $stmt->close();
            return $user;
        } 
        else {
            return NULL;
        }
    }

    /**
     * Busca un servicio pendiente
     * @param type $code
     * @return type
     */
    public function searchPendingService($code) {
        $stmt = $this->conn->prepare("SELECT 
                                            o.id 
                                    FROM orden AS o 
                                            LEFT JOIN seguimiento AS s ON (s.referencia = o.referencia)
                                    WHERE o.conductor = ? 
                                            AND o.fecha_s < ?
                                            AND (o.CD = null OR o.CD = '') 
                                            AND s.referencia IS NULL
                                    LIMIT 1");

        $today = date("d/m/Y");
        $stmt->bind_param("ss", $code, $today);

        $service = array();

        if ($stmt->execute()) {
            $stmt->bind_result($id);
            $stmt->fetch();
            
            $service["service_id"] = $id;

            $stmt->close();  
        } 

        return $service;
    }

    
    /**
     * 
     * @param type $id
     * @param type $code
     * @return string
     */
    public function getService($id, $code) {
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
                        p.nombre,
                        p.apellido,
                        p.telefono1,
                        p.telefono2,
                        p.correo1,
                        p.correo2,
                        s.id as seguimiento_id,
                        s.b1ha,
                        s.bls,
                        s.pab,
                        s.st
            FROM orden AS o
                LEFT JOIN pasajeros AS p ON (p.codigo = o.persona_origen) 
                LEFT JOIN seguimiento as s ON (s.referencia = o.referencia)
            WHERE o.id = ?
            AND o.conductor = ? 
            AND o.estado != 'cancelar'";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("is", $id, $code);

        if ($stmt->execute()) {
            $stmt->bind_result($orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $observaciones, $orden_estado, $cd, $passenger_id, $passenger_code, $name, $lastName, $phone1, $phone2, $email1, $email2, $trace_id, $b1ha, $bls, $pab, $st);

            $stmt->fetch();
            
            $old = 0;
            
            $dateArray = explode("/", $fecha_s);
            $timeStampDateS = strtotime("{$dateArray[1]}/{$dateArray[0]}/{$dateArray[2]}");
            $timeStampToday = time();       
            if ($timeStampDateS < $timeStampToday) {
                $old = 1;
            }
            
            $b1ha = trim($b1ha);
            $bls = trim($bls);
            $pab = trim($pab);
            $st = trim($st);

            $service = array();
            $service["service_id"] = $orden_id;
            $service["ref"] = $referencia;
            $service["date"] = $fecha_e . " " . $hora_e;
            $service["start_date"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2;
            $service["fly"] = $vuelo;
            $service["aeroline"] = $aerolinea;
            $service["pax_cant"] = $cant_pax;
            $service["pax"] = $this->getPassengers($pax2, $pax3, $pax4, $pax5);
            $service["source"] = trim($ciudad_inicio) . ", " . trim($dir_origen);
            $service["destiny"] = trim($ciudad_destino) . ", " . trim($dir_destino);
            $service["observations"] = trim($observaciones);
            $service["status"] = $orden_estado;
            $service["cd"] = $cd;
            $service['old'] = $old;
            $service["passenger_id"] = $passenger_id;
            $service["passenger_code"] = $passenger_code;
            $service["passenger_name"] = $name;
            $service["passenger_lastname"] = $lastName;
            $service["phone"] = trim($phone1) . ", " . trim($phone2);
            $service["email"] = trim($email1) . ", " . trim($email2);
            $service["trace_id"] = (empty($trace_id) ? 0 : $trace_id);
            $service["b1ha"] = (empty($b1ha) ? null : $b1ha);
            $service["bls"] = (empty($bls) ? null : $bls);
            $service["pab"] = (empty($pab) ? null : $pab);
            $service["st"] = (empty($st) ? null : $st);

            $stmt->close();  
            
            return $service;
        }
        else {
            return $service = array();
        }
    }
    
    /**
     * 
     * @param type $pax2
     * @param type $pax3
     * @param type $pax4
     * @param type $pax5
     * @return type
     */
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
    
    public function getServicesGrouped($code) {
        $sql = $this->getServicesSQL(true);
        
        $stmt = $this->conn->prepare($sql);

        $currentDate =  date('m/d/Y', strtotime(date('Y-m-d'). ' - 8 days'));
        $nextdate = date('m/d/Y', strtotime(date('Y-m-d'). ' + 30 days'));
        
        $stmt->bind_param("sss", $currentDate, $nextdate, $code);
        $stmt->execute();

        $services = $this->modelGroupedDataServices($stmt);

        //$services = $stmt->get_result();
        $stmt->close();
        return $services;
    }
    
    private function getServicesSQL($between) {
        $date = ($between ? 'o.fecha_s between ? AND ? ' : "o.fecha_s = ? ");
        
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
                        p.nombre,
                        p.apellido,
                        p.telefono1,
                        p.telefono2,
                        p.correo1,
                        p.correo2
            FROM orden AS o
                LEFT JOIN pasajeros AS p ON (p.codigo = o.persona_origen) 
            WHERE {$date}
            AND o.conductor = ? 
            AND o.estado != 'cancelar'
            AND (o.CD != null OR o.CD != '')";
            
        return $sql;
    }
    
    
    private function modelGroupedDataServices($stmt) {
        //$log = new LoggerHandler();
        $dates = array();
        $data = array(
            'dates' => array(),
            'services' => array(),
        );
        
        $stmt->bind_result($orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $observaciones, $orden_estado, $cd, $passenger_id, $passenger_code, $name, $lastName, $phone1, $phone2, $email1, $email2);

        while ($stmt->fetch()) {
            $date = trim($fecha_s);
            
            $old = 0;
            
            $dateArray = explode("/", $date);
            $timeStampDateS = strtotime("{$dateArray[1]}/{$dateArray[0]}/{$dateArray[2]}");
            $timeStampToday = time();       
            if ($timeStampDateS < $timeStampToday) {
                $old = 1;
            }
            
            $service = array();
            $service["service_id"] = $orden_id;
            $service["ref"] = $referencia;
            $service["date"] = $fecha_e . " " . $hora_e;
            $service["start_date"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2;
            $service["fly"] = $vuelo;
            $service["aeroline"] = $aerolinea;
            $service["pax_cant"] = $cant_pax;
            $service["pax"] = $this->getPassengers($pax2, $pax3, $pax4, $pax5);
            $service["source"] = trim($ciudad_inicio) . ", " . trim($dir_origen);
            $service["destiny"] = trim($ciudad_destino) . ", " . trim($dir_destino);
            $service["observations"] = trim($observaciones);
            $service["status"] = $orden_estado;
            $service["cd"] = $cd;
            $service["old"] = $old;
            $service["passenger_id"] = $passenger_id;
            $service["passenger_code"] = $passenger_code;
            $service["passenger_name"] = $name;
            $service["passenger_lastname"] = $lastName;
            $service["phone"] = trim($phone1) . ", " . trim($phone2);
            $service["email"] = trim($email1) . ", " . trim($email2);
            //Driver information
            

            if (in_array($date, $dates)) {
                $key = array_search($date, $dates);
                $data['services'][$key][] = $service;
            }
            else {
                $newKey = count($dates);
                $dates[$newKey] = $date;
                $data['services'][$newKey][] = $service;
            }
        }
        
        $data['dates'] = $dates;

        return $data;
    }
    
    /**
     * 
     * @param type $code
     * @param type $idOrden
     * @param type $status
     * @return type
     */
    public function acceptOrDeclineService($code, $idOrden, $status) {
        if ($status == 1 || $status == "1") {
            $estado = date("D M j G:i:s T Y");
            $conductor = $code;
        }
        else {
            $estado = "";
            $conductor = "";
        }
        
        $stmt = $this->conn->prepare("UPDATE orden SET CD = ?, conductor = ? WHERE id = ?");
        
        $stmt->bind_param("ssi", $estado, $conductor, $idOrden);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * 
     * @param type $id
     * @param type $code
     * @param type $start
     * @param type $end
     * @param type $observations
     */
    public function traceService($id, $user, $start, $end, $observations) {
        $log = new LoggerHandler();

        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($id, $user['code']);

        //2. Validamos que el servicio no tenga seguimiento
        $this->validateTraceExists($reference);

        //3. Aceptamos el servicio
        $this->acceptService($id, $user['code']);
        
        //4. Tomamos la placa del conductor
        $carLicense = $this->getCarLicense($user['code']);

        //5. Guardamos el seguimiento
        return $this->setTrace($reference, $start, $end, $user, $observations, $carLicense);
    }

    private function validateServiceExists($id, $code) {
        $stmt = $this->conn->prepare("SELECT referencia FROM orden WHERE id = ? AND conductor = ?");

        $stmt->bind_param("is", $id, $code);

        if ($stmt->execute()) {
            $stmt->bind_result($referencia);
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                $stmt->close();

                return $referencia;
            } 
            else {
                $stmt->close();
                throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
            }
        } 
        else {
            $stmt->close(); 
            throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
        }
    }

    private function validateTraceExists($reference) {
        $stmt = $this->conn->prepare("SELECT id FROM seguimiento WHERE referencia = ?");
        $stmt->bind_param("s", $reference);

        if ($stmt->execute()) {
            $stmt->bind_result($id);
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();
                throw new InvalidArgumentException('El servicio ya tiene seguimiento');
            }
        } 
        else {
            $stmt->close(); 
            throw new InvalidArgumentException('El servicio ya tiene seguimiento');
        }
    }

    private function acceptService($id, $code) {
        $estado = date("D M j G:i:s T Y");
        $stmt = $this->conn->prepare("UPDATE orden SET CD = ?, conductor = ? WHERE id = ?");
        $stmt->bind_param("ssi", $estado, $code, $id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        if ($num_affected_rows > 0 == false) {
            $stmt->close();
            throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
        }
        
        $stmt->close();
    }

    private function getCarLicense($code) {
        $stmt = $this->conn->prepare("SELECT placa FROM conductor WHERE codigo = ?");
        $stmt->bind_param("s", $code);

        if ($stmt->execute()) {
            $stmt->bind_result($placa);
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                $stmt->close();

                return $placa;
            } 
            else {
                $stmt->close();
                throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
            }
        } 
        else {
            $stmt->close(); 
            throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
        }
    }

    private function setTrace($reference, $start, $end, $user, $observations, $carLicense) {
        $stmt = $this->conn->prepare("INSERT INTO seguimiento(referencia, hora1, hora2, conductor, elaborado, observaciones) VALUES(?, ?, ?, ?, ?, ?)");
        
        $conductor = "{$user['name']} {$user['lastname']} ({$carLicense})";
        $elaborado = date("D, F d Y, H:i:s");
        $observations = (empty($observations) ? "SERVICIO SIN NOVEDAD" : $observations);

        $stmt->bind_param("ssssss", $reference, $start, $end, $conductor, $elaborado, $observations);
        $result = $stmt->execute();
        $stmt->close();
 
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 
     * @param type $user
     * @param type $idOrden
     */
    public function confirmService($user, $idOrden) {
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($idOrden, $user['code']);

        //2. Tomamos la placa del auto
        $carLicense = $this->getCarLicense($user['code']);

        //3. Cambiamos el estado de la orden a reconfirmacion = 1 y reconfirmacion2 = "si"
        $this->reconfirmService($idOrden);
        
        //4. Guardamos el seguimiento con el estado B1HA
        return saveB1HAStatus($reference, $user, $carLicense);
    }

    private function reconfirmService($id) {
        $stmt = $this->conn->prepare("UPDATE orden SET reconfirmacion = 1, reconfirmacion2 = 'si' WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        if ($num_affected_rows > 0 == false) {
            $stmt->close();
            throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
        }
        
        $stmt->close();
    }
    
    private function saveB1HAStatus($reference, $user, $carLicense) {
        $stmt = $this->conn->prepare("INSERT INTO seguimiento(referencia, conductor, b1ha) VALUES(?, ?, ?)");
        
        $conductor = "{$user['name']} {$user['lastname']} ({$carLicense})";
        $b1ha = date("d/m/Y H:i:s");

        $stmt->bind_param("sss", $reference, $conductor, $b1ha);
        $result = $stmt->execute();
        $stmt->close();
 
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $user
     * @param type $idOrden
     */
    public function setOnSource($user, $idOrden) {
        $log = new LoggerHandler();

        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($idOrden, $user['code']);

        //3. Guardamos el seguimiento con el estado BLS
        return saveBLSStatus($reference);
    }


    public function saveBLSStatus($reference) {
        $stmt = $this->conn->prepare("UPDATE seguimiento SET bls = ? WHERE referencia = ?");
        
        $bls = date("d/m/Y H:i:s");

        $stmt->bind_param("ss", $bls, $reference);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
    /**
     * 
     * @param type $user
     * @param type $idOrden
     * @param type $lat
     * @param type $lon
     */
    public function setPreLocation($user, $idOrden, $lat, $lon) {
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($idOrden, $user['code']);

        //2. Guardamos la latitud y longitud en la tabla location
        return $this->savePreLocation($idOrden, $reference, $lat, $lon);
    }

    private function savePreLocation($idOrden, $reference, $lat, $lon) {
        $stmt = $this->conn->prepare("INSERT INTO prelocation(idOrden, referencia, latitude, longitude, createdon) VALUES(?, ?, ?, ?, ?)");

        $createdon = date("d/m/Y H:i:s");
        $stmt->bind_param("isssss", $idOrden, $reference, $lat, $lon, $createdon);
        $result = $stmt->execute();
        $stmt->close();
 
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * 
     * @param type $user
     * @param type $idOrden
     */
    public function startService($user, $idOrden) {
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($idOrden, $user['code']);

        //2. Guardamos la hora de inicio del segumiento
        return saveStartTimeService($reference);
    }
    
    public function saveStartTimeService($reference) {
        $stmt = $this->conn->prepare("UPDATE seguimiento SET hora1 = ? WHERE referencia = ?");
        
        $start = date("H:i");

        $stmt->bind_param("ss", $start, $reference);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * Save a location with latitude and longitude
     * @param type $code
     * @param type $idOrden
     * @param type $lat
     * @param type $lon
     */
    public function setLocation($code, $idOrden, $lat, $lon) {
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($idOrden, $code);

        //2. Guardamos la latitud y longitud en la tabla location
        return $this->saveLocation($idOrden, $reference, $lat, $lon);
    }

    private function saveLocation($idOrden, $reference, $lat, $lon) {
        $stmt = $this->conn->prepare("INSERT INTO location(idOrden, referencia, latitude, longitude, createdon) VALUES(?, ?, ?, ?, ?)");

        $createdon = date("d/m/Y H:i:s");
        $stmt->bind_param("issss", $idOrden, $reference, $lat, $lon, $createdon);
        $result = $stmt->execute();
        $stmt->close();
 
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    

    /**
     * 
     * @param type $code
     * @param type $idOrden
     */
    public function finishService($user, $idOrden, $observations) {
        $log = new LoggerHandler();

        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($idOrden, $user['code']);
        

        //3. Actualizamos el seguimiento con la hora de finalización y demás datos
        return saveEndTimeService($reference, $user, $observations);
    }

    private function saveEndTimeService($reference, $user, $observations) {
        $stmt = $this->conn->prepare("UPDATE seguimiento SET hora2 = ?, elaborado = ?, observaciones = ? WHERE referencia = ?");
        
        $end = date("H:i");
        $elaborado = date("D, F d Y, H:i:s");
        $observations = (empty($observations) ? "SERVICIO SIN NOVEDAD" : $observations);

        $stmt->bind_param("ssss", $end, $elaborado, $observations, $reference);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
}