<?php
require_once '../api/include/DbHandler.php';

$status = 0;
if (isset($_POST['submit'])) {
    $query = $_POST['query'];
    if (isset($query)) {
        $db = new DbHandler();
        $driver = $db->getUserByUsername($query);

        if ($driver != null && $driver['id'] != 0) {
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
        <link href="../admin/nuevo_admin_2015/template/plugins/switchery/switchery.min.css" rel="stylesheet">
        <script src="../admin/nuevo_admin_2015/template/plugins/pace/pace.min.js"></script>
        <script src="../admin/nuevo_admin_2015/template/js/jquery-2.1.1.min.js"></script>
        <script src="../admin/nuevo_admin_2015/template/js/bootstrap.min.js"></script>
        <script src="../admin/nuevo_admin_2015/template/js/nifty.min.js"></script>
        <script src="../admin/nuevo_admin_2015/template/plugins/switchery/switchery.min.js"></script>
        <script>
            $(document).ready(function () {
                var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));

                elems.forEach(function (html) {
                    html.onchange = function () {
                        var s = 0;
                        if (html.checked) {
                            s = 1;
                        }
                        
                        var idD = $("#idDriver").val();

                        $.ajax({
                            type: "POST",
                            url: "change-update-status.php",
                            data: {idDriver: idD, val: s},
                            success: function (data) {
                                if (s) {
                                    notification("success", "Se ha activado {{resource}} exitosamante", 4500);
                                } else {
                                    notification("warning", "Se ha desactivado {{resource}} exitosamante", 4500);
                                }
                            },
                            error: function (data) {
                                notification("warning", data.responseJSON.message, 4500);
                                html.checked = true;
                            },
                        });
                    };

                    var switchery = new Switchery(html);
                });
            });

            function notification(type, msg, time) {
                $.niftyNoty({
                    type: type,
                    container: 'page',
                    html: msg,
                    timer: time
                });
            }

        </script>
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
                    if ($status) {
                        ?>
                        <div class="row">
                            <div class="col-md-offset-1 col-md-10">
                                <div class="panel panel-bordered-dark">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Detalle de conductor</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Información</th>
                                                                <th>Detalles</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span style="font-size: 1.2em; font-weight: bold;">
                                                                        <?php echo $driver['name'] . " " . $driver['lastname']; ?>
                                                                    </span>
                                                                    <br />
                                                                    <span style="font-size: 1em">
                                                                        <?php echo $driver['email1']; ?>
                                                                    </span>
                                                                    <br />
                                                                    <span style="font-size: 0.9em">
                                                                        Usuario: 
                                                                        <em>
                                                                            <?php echo $driver['username']; ?>
                                                                        </em>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php echo $driver['phone1']; ?> - <?php echo $driver['phone2']; ?>
                                                                    <br />
                                                                    <div style="font-size: 0.5em">
                                                                        <strong>Api key: </strong>
                                                                        <em><?php echo $driver['api_key']; ?></em>
                                                                    </div>
                                                                    <div style="font-size: 0.5em">
                                                                        <strong>Token: </strong>
                                                                        <em><?php echo $driver['device_token']; ?></em>
                                                                    </div>
                                                                </td>
                                                                <td class="text-right">
                                                                    <input type="hidden" id="idDriver" value="<?php echo $driver['id']; ?>" />
                                                                    <input type="checkbox" class="switchery add-tooltip" data-placement="top" title="Revocar permiso para editar seguimiento" 
                                                                    <?php
                                                                    if ($driver['update_order']) {
                                                                        echo 'checked';
                                                                    }
                                                                    ?>
                                                                           value='<?php echo $driver['update_order']; ?>'>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-6">
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
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="row">
                            <div class="col-md-offset-4 col-md-4 text-center">
                                <h1>No se encontraron coincidencias.</h1>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>