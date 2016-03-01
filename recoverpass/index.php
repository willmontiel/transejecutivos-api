<?php
	session_start();

	require_once '../api/include/DbHandler.php';

	$db = new DbHandler();

	if (!isset($_GET["code"])) {
		header("Location: notfound.html");
		die();
	}
	else {
		$code = $_GET["code"];
		$user = $db->validateLink($code);

		if (!$user) {
			header("Location: notfound.html");
			die();
		}
	}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=1">
        <link rel="apple-touch-icon" sizes="57x57" href="images/favicons/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="images/favicons/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="images/favicons/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="images/favicons/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="images/favicons/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="images/favicons/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="images/favicons/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="images/favicons/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="images/favicons/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="images/favicons/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="images/favicons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="images/favicons/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="images/favicons/favicon-16x16.png">
		<link rel="manifest" href="images/favicons/manifest.json">
		<meta name="msapplication-TileImage" content="images/favicons/ms-icon-144x144.png">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/skeleton.css">
        <link rel="stylesheet" href="css/normalize.css">
        <title>
			Transejecutivos - Reestrablecer contraseña
        </title>
    </head>
    <body>
        <!-- .container is main centered wrapper -->
		<div class="container">

		    <div class="row">
			    <div class="twelve columns text-center">
					<img src="images/complete-logo.png" >
			    </div>
		    </div>

			<br> <br> 

		    <div class="row">
			    <div class="twelve columns text-center">
					<h1>Reestablecer contraseña</h1>
			    </div>
		    </div>

		    <form method="POST" action="reset.php">
			    <div class="row">
			    	<div class="three columns">
			    		&nbsp;
			    	</div>
				    <div class="six columns">
			      	    <label for="password1">Escribe tu nueva contraseña</label>
				        <input name="password1" class="u-full-width" required="required" autofocus="autofocus" type="password" placeholder="Escribe tu nueva contraseña" id="password1">
				    </div>
				    <div class="three columns">
				    	&nbsp;
			    	</div>
			  	</div>

			  	<div class="row">
			    	<div class="three columns">
			    		&nbsp;
			    	</div>
				    <div class="six columns">
			      	    <label for="password2">Confirma tu nueva contraseña</label>
				        <input name="password2" class="u-full-width" required="required" type="password" placeholder="Confirma tu nueva contraseña" id="password2">
				        <input name="link" value="<?php echo $code; ?>" type="hidden">
				    </div>
				    <div class="three columns">
				    	&nbsp;
			    	</div>
			  	</div>
				
				<br>

			  	<div class="row">
			    	<div class="three columns">
			    		&nbsp;
			    	</div>
				    <div class="six columns">
			      	    <input class="button-primary" type="submit" value="Reestablecer">
				    </div>
				    <div class="three columns">
				    	&nbsp;
			    	</div>
			  	</div>

				<?php
					if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
    			?>
					<div class="row">
				    	<div class="twelve columns text-center">
							<div class="alert-error"><?php echo $_SESSION['error']; ?></div>
					    </div>
				  	</div>
    			<?php
					} 
				?>
			</form>
		</div>
    </body>
</html>