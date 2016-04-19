<?php
    session_start();
    
    require_once '../api/include/DbHandlerDriver.php';
    
    if (!isset($_GET["id"])) {
        header("Location: notfound.html");
        die();
    }
    
    $id = $_GET["id"];
    
    $db = new DbHandlerDriver();
    $ref = $db->validateServiceExistsById($id);

    /*
    if ($ref == 0) {
        header("Location: notfound.html");
        die();
    }
     * 
     */
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
        <link rel="stylesheet" href="css/stylesheet.css">
        <title>
            Transportes Ejecutivos - Califica nuestro servicio
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
					<h1>Califica nuestro servicio</h1>
			    </div>
		    </div>

		    <form method="POST" action="process.php">
			    <div class="row">
			    	<div class="three columns">
			    		&nbsp;
			    	</div>
				    <div class="six columns">
                        <span class="starRating">
                            <input id="rating5" type="radio" name="rating" value="5">
                            <label for="rating5">5</label>
                            <input id="rating4" type="radio" name="rating" value="4">
                            <label for="rating4">4</label>
                            <input id="rating3" type="radio" name="rating" value="3">
                            <label for="rating3">3</label>
                            <input id="rating2" type="radio" name="rating" value="2">
                            <label for="rating2">2</label>
                            <input id="rating1" type="radio" name="rating" value="1">
                            <label for="rating1">1</label>
                        </span>
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
			      	    <label for="comments">Escribe tus comentarios</label>
                                        <textarea name="comments" class="u-full-width"></textarea>
				        <input name="ref" value="<?php echo $ref; ?>" type="hidden">
				        <input name="id" value="<?php echo $id; ?>" type="hidden">
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
			      	    <input class="button-primary" type="submit" value="Calificar">
				    </div>
				    <div class="three columns">
				    	&nbsp;
			    	</div>
			  	</div>

				<?php
                                    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                                ?>
                                    <br>
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