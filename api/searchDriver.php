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
        <title>Transportes Ejecutivos</title>
    </head>
    <body style="text-align:center;color:blue">
    <h1>Buscar conductor</h1>
    <form method='POST' action=''>
        <div>
            <input type="text" name="query" id="query" placeholder="Buscar por usuario"/>
        </div>
        <div style="margin-top:10px">
            <input type = 'submit' name="submit" value='Buscar'>
        </div>
    </form>
    <?php
        if($status) {
    ?>
            <form method='POST' action='sendNotification.php'>
                <div>
                    <input type="hidden" name="token" id="token" value="<?php echo $driver['device_token']; ?>"/>
                    <textarea rows="6" name="message" cols="50" placeholder='Mensaje sque será enviado al conductor'></textarea>
                </div>
                <div style="margin-top:10px">
                    <input type = 'submit' name="submit" value = 'Enviar'>
                </div>
            </form>
    <?php
            var_dump($driver);
        }
    ?>
    </body>
</html>