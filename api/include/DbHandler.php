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
     * Fetching user data by api key
     * @param String $api_key user api key
     */
    public function getUser($api_key) {
        $stmt = $this->conn->prepare("SELECT id, usuario, nivel_clte, codigo FROM admin WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id, $username, $type, $codigo);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["username"] = $username;
            $user["type"] = $type;
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

        $currentDate =  date('m/d/Y');
        $nextdate = date('m/d/Y', strtotime(date('Y-m-d'). ' + 30 days'));
        
        $stmt->bind_param("sss", $currentDate, $nextdate, $code);
        $stmt->execute();

        $services = $this->modelDataServices($stmt);

        //$services = $stmt->get_result();
        $stmt->close();
        return $services;
    }


    /**
    * Fetching all user services by date
    * @param String $code user code
    * @param String $date service date
    */
    public function getServicesByDate($code, $date) {
        //$log = new LoggerHandler();
        
        $sql = $this->getServicesSQL(false);
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bind_param("ss", $date, $code);
        
        $stmt->execute();

        $services = $this->modelDataServices($stmt);
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
                            c.estado
            FROM admin AS a
                    LEFT JOIN orden AS o ON (o.persona_origen = a.codigo)
                    LEFT JOIN conductor AS c ON (c.codigo = o.conductor) 
            WHERE {$date} 
            AND a.codigo = ? 
            ORDER BY o.fecha_s ASC";
            
        return $sql;
    }
    
    private function modelDataServices($stmt) {
        $services = array();
        
        $stmt->bind_result($orden_id, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $empresa, $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $obaservaciones,
                           $conductor_id, $conductor_nombre, $conductor_apellido, $conductor_telefono1, $conductor_telefono2, $conductor_direccion, $conductor_ciudad, $conductor_email, $conductor_codigo, $carro_tipo, $marca, $modelo, $color, $placa, $estado);
        
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
            $tmp["pax_cant"] = $cant_pax;
            $tmp["pax"] = $this->getPassengers($pax2, $pax3, $pax4, $pax5);
            $tmp["source"] = trim($ciudad_inicio) . ", " . trim($dir_origen);
            $tmp["destiny"] = trim($ciudad_destino) . ", " . trim($dir_destino);
            $tmp["service_observations"] = $obaservaciones;
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


    /**
     * update user profile
     * @param String $username user username
     */
    public function updateProfile($username, $name, $lastname, $email1, $email2, $phone1, $phone2, $password) {
        $log = new LoggerHandler();
        $pass = trim($password);
        $email2 = trim($email2);
        $phone2 = trim($phone2);
        $passSQL = (empty($pass) ? "" : ", clave = ?");
     

        $sql = "UPDATE admin SET nombre = ?, apellido = ?, correo1 = ? , correo2 = ?, telefono1 = ?, telefono2 = ? {$passSQL} WHERE usuario = ?";
       
        $stmt = $this->conn->prepare($sql);
       
        if (empty($pass)) {
            $stmt->bind_param("sssssss", $name, $lastname, $email1, $email2, $phone1, $phone2, $username);
        }
        else {
            $stmt->bind_param("ssssssss", $name, $lastname, $email1, $email2, $phone1, $phone2, $pass, $username);
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
    
    /**
     * Send a mail with instructions for to recover password
     * @param string $code
     * @param string $email
     * @return boolean
     */
    private function sendMail($code, $email){
        $link = '<a href="http://www.transportesejecutivos.com/recoverpass/index.php?code=' . $code . '">Reestablecer contrase&ntilde;a</a>';
        $html = '<html> <head></head> <body> <h2> <span style="font-family: Helvetica;"> <strong>Estimado usuario:</strong> </span> </h2> <br><table> <tbody> <tr> <td> <span style="font-family: Helvetica;"> Ha olvidado su contrase&ntilde;a, para reestablecerla por favor haga clic en el siguiente enlace, </span> <br><br></td></tr><tr> <td> <span style="font-family: Helvetica;"> %tmpurl% </span> </td></tr><tr> <td> <br><span style="font-family: Helvetica;"> Si no ha solicitado reestablecer su contrase&ntilde;a, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, cont&aacute;ctenos a trav&eacute;s de este correo <a href="mailto:info@transportesejecutivos.com">info@transportesejecutivos.com.</a> </span> </td></tr><tr> <td> <br><img src="http://www.transportesejecutivos.com/recoverpass/images/complete-logo.png" alt="Transportes Ejecutivos"/> </td></tr></tbody> </table> </body></html>';
        
        $para      = $email;
        $titulo    = 'Instrucciones para recuperar la contraseña de acceso a Transportes Ejecutivos';
        $mensaje   = str_replace("%tmpurl%", $link, $html);
        
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: <info@transportesejecutivos.com>' . "\r\n";

        return mail($para, $titulo, $mensaje, $headers);
    }
}

?>
