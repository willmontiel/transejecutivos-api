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

    public function getPendingService($code) {
        $stmt = $this->conn->prepare("SELECT id, usuario, nombre, apellido, correo1, correo2, telefono1, telefono2, empresa, api_key, nivel_clte, codigo FROM admin WHERE usuario = ? AND estado = ?");
    }
}