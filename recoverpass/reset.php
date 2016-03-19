<?php

session_start();

require_once '../api/include/DbHandler.php';

$db = new DbHandler();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $pass1 = $_POST['password1'];
    $pass2 = $_POST['password2'];
    $link = $_POST['link'];

    if ($pass1 != $pass2) {
        $_SESSION['error']  = 'Las contraseñas no coinciden, por favor valida la información';
        header("Location: index.php?code={$link}");
        die();
    }
    else {
        $res = $db->resetPassword($username, $pass1, $link);
        if ($res) {
            header("Location: success.html");
                        die();
        }
        else {
            $_SESSION['error']  = 'No se pudo reestablecer la contraseña, lo invitamos a hacer todo el proceso de nuevo';
            header("Location: index.php?code={$link}");
            die();
        }
    }
}