<?php

session_start();

require_once '../api/include/DbHandlerDriver.php';

$db = new DbHandlerDriver();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comments = $_POST['comments'];
    $ref = $_POST['ref'];
    $id = $_POST['id'];
    $points = $_POST['points'];

    $res = $db->setQualify($id, $ref, $points, $comments);
    if ($res) {
        header("Location: success.html");
                    die();
    }
    else {
        $_SESSION['error']  = 'No se pudo calificar el servicio, lo invitamos a hacer todo el proceso de nuevo';
        header("Location: index.php?id={$id}");
        die();
    }
    
}