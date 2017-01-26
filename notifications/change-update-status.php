<?php
require_once '../api/include/DbHandler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDriver = $_POST['idDriver'];
    $val = $_POST['val'];
    
    if (isset($query)) {
        $db = new DbHandler();
        $driver = $db->updateOrderPermissionStatus($id, $val);

        if ($driver) {
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Se ha cambiado el estado existosamente, este conductor no podrá editar seguimientos"));
            return;
        }
        
        header('Content-Type: application/json');
        echo json_encode(array("message" => "Ocurrión un error, por favor contacta a soporte"));
    }
}
