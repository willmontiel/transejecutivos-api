<?php

require_once '../api/include/DbHandler.php';

$status = 0;
if(isset($_POST['submit'])) {
    $query = $_POST['query'];
    if(isset($query)) {
        $db = new DbHandler();
        $driver = $db->getUserByUsername($query);
        if ($driver != null) {
            $status = 1;
        }
    }   
}
?>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../admin/nuevo_admin_2015/template/css/bootstrap.min.css" rel="stylesheet">
		<link href="../admin/nuevo_admin_2015/template/css/nifty.min.css" rel="stylesheet">
		<link href="../admin/nuevo_admin_2015/template/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<link href="../admin/nuevo_admin_2015/template/plugins/animate-css/animate.min.css" rel="stylesheet">
		<link href="../admin/nuevo_admin_2015/template/plugins/pace/pace.min.css" rel="stylesheet">
		<script src="../admin/nuevo_admin_2015/template/plugins/pace/pace.min.js"></script>
		<script src="../admin/nuevo_admin_2015/template/js/jquery-2.1.1.min.js"></script>
		<script src="../admin/nuevo_admin_2015/template/js/bootstrap.min.js"></script>
		<script src="../admin/nuevo_admin_2015/template/js/nifty.min.js"></script>
        <title>Transportes Ejecutivos</title>
    </head>
    <body>
		<div id="" class="effect mainnav-sm">
			<div id="content-container">
				<div id="page-content">
					<div class="row">
						<div class="col-md-offset-4 col-md-4 text-center">
							<h1 class="">Transportes Ejecutivos</h1>
						</div>
					</div>
					<div class="row">
						<div class="col-md-offset-4 col-md-4">
							<form method='POST' class="" action=''>
								<div class="panel panel-bordered panel-dark">
									<div class="panel-heading">
										<h3 class="panel-title">Buscar conductor</h3>
									</div>
									<div class="panel-body form-horizontal">
										<div class="form-group">
											<label class="col-md-4 control-label" for="query">Usuario del conductor</label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="query" id="query" placeholder="Buscar por usuario" style="height: auto;"/>
											</div>
										</div>
									</div>
									<div class="panel-footer text-right">
										<input class="btn btn-success" type='submit' name="submit" value='Buscar'>
									</div>
								</div>
							</form>
						</div>
					</div>

					
					<?php
						if($status) {
					?>
						<div class="row">
							<div class="col-md-offset-4 col-md-4">
								<div class="panel panel-bordered-dark">
									<div class="panel-heading">
										<h3 class="panel-title">Detalle de conductor</h3>
									</div>
									<div class="panel-body">
										<table class="table table-bordered">
											<tr>
												<td>Nombre</td>
												<td><?php echo $driver['name'] . " " . $driver['lastname']; ?></td>
												<td>Usuario</td>
												<td><?php echo $driver['username']; ?></td>
											</tr>
											<tr>
												<td>Correo</td>
												<td><?php echo $driver['email1']; ?></td>
												<td>Teléfono</td>
												<td><?php echo $driver['phone1']; ?></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-offset-4 col-md-4">
								<form method='POST' action='sendNotification.php'>
									<div class="panel panel-bordered-danger">
										<div class="panel-heading">
											<h3 class="panel-title">Enviar notificación</h3>
										</div>
										<div class="panel-body form-horizontal">
											<div class="form-group">
												<label class="col-md-4 control-label" for="query">Mensaje</label>
												<div class="col-md-8">
													<input type="hidden" name="token" id="token" value="<?php echo $driver['device_token']; ?>"/>
													<textarea rows="6" class="form-control" name="message" cols="50" placeholder='Mensaje que será enviado al conductor'></textarea>
												</div>
											</div>
										</div>
										<div class="panel-footer text-right">
											<input class="btn btn-success" type='submit' name="submit" value='Enviar'>
										</div>
									</div>
								</div>
							</form>
						</div>
					<?php
						}
					?>
						
				</div>
			</div>
		</div>
    </body>
</html>