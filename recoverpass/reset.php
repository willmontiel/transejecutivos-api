<?php

	require_once '../api/include/DbHandler.php';

	$db = new DbHandler();

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$pass1 = $_POST['password1'];
		$pass2 = $_POST['password2'];
		$link = $_POST['link'];

		if ($password1 != $password2) {
			$_SESSION['error']  = 'Las contraseñas no coinciden, por favor valida la información';
			header("Location: index.php?code={$link}");
			die();
		}
		else {
	    	$res = $db->resetPassword($username, $password, $link);

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