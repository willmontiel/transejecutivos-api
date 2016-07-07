<?php

require_once 'LoggerHandler.php';

class DbManager {

  private $conn;
  
  function __construct() {
    require_once dirname(__FILE__) . '/DbConnect.php';
    // opening db connection
    $db = new DbConnect();
    $this->conn = $db->connect();
  }
  
  public function createSelectQuery() {
    $data = array();
    
    $username = "will";
    
    $sql = "SELECT id, usuario, nombre, apellido, correo1, correo2, telefono1, telefono2, empresa, api_key, nivel_clte, codigo, first_time "
            . "FROM admin "
            . "WHERE usuario = ? "
            .   "AND estado = 'activo' "
            .   "AND nivel_clte = 'conductor'";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $username);
    
    if ($stmt->execute()) {
      $actor = $resultado->fetch_assoc();
      
      
      $stmt->bind_result($id, $username, $name, $lastname, $email1, $email2, $phone1, $phone2, $company, $api_key, $type, $code, $first_time);
      $stmt->fetch();
      $user = array(
          'id' => $id,
          'username' => $username,
          'name' => $name,
          'lastname' => $lastname,
          'email1' => $email1,
          'email2' => $email2,
          'phone1' => $phone1,
          'phone2' => $phone2,
          'company' => $company,
          'api_key' => $api_key,
          'type' => $type,
          'code' => $code,
          'first_time' => $first_time,
      );

      $stmt->close();
      return $user;
    }
    
    throw new InvalidArgumentException('Ocurrió un error, mientras se realizaba la consulta, contacta a soporte');
  }
}
