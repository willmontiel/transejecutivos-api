<?php
require_once '../api/include/DbHandler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDriver = $_POST['idDriver'];
    $val = $_POST['val'];
    
    if (!empty($idDriver)) {
        $db = new DbHandler();
        $driver = $db->updateOrderPermissionStatus($idDriver, $val);

        if ($driver) {
            header('Content-Type: application/json');
            header("HTTP/1.0 200 Success");
            echo json_encode(array("message" => "Se ha cambiado el estado existosamente, este conductor no podrá editar seguimientos"));
            return;
        }
        
        header('Content-Type: application/json');
        header("HTTP/1.0 500 Server error");
        echo json_encode(array("message" => "Ocurrió un error, por favor contacta a soporte"));
        return;
    }
}
