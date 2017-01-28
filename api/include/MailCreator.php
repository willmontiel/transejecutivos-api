<?php

require_once 'LoggerHandler.php';

class MailCreator {
    public $html;
    public $plaintext;
    
    public function __construct() {
        
    }
    
    public function getHtml() {
        return $this->html;
    }
    
    public function getPlaintext() {
        return $this->plaintext;
    }

    public function createDeclineServiceNotification($data) {
        $this->html = "";
    }
    
    public function createAcceptServiceNotification($data) {
        $this->html = "";
    }
    
    public function createConfirmNotification($data) {
        $this->html = "";
    }
    
    public function createOnSourceNotification($data) {
        $this->html = "";
    }
    
    public function createResumeNotification($data) {
        $this->html = '<html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=utf8">
                                <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                                <meta name="viewport" content="width=device-width, initial-scale=1">
                            </head>
                            <body>
                                <table style="background-color: #dddddd; width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td style="padding: 20px;"><center>
                                        <table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 100%; vertical-align: top; padding: 0; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border: 0px none #ffffff;">
                                                        <table style="table-layout: fixed; width: 100%; border-spacing: 0px;" width="100%" cellpadding="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="padding-left: 0px; padding-right: 0px;">
                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 10px; margin-bottom: 10px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 100%; vertical-align: middle; padding-left: 0px; padding-right: 0px;" width="100%">
                                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td style="width: 432px;" align="center" width="432px"><img class="CToWUd" style="min-height: 63px; width: 432px;" src="http://transportesejecutivos.com/images/horizontal_logo.png" alt="horizontal_logo.png" width="432" height="63" /></td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 100%; vertical-align: top; padding: 0; background-color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border: 0px none #ffffff;">
                                                        <table style="table-layout: fixed; width: 100%; border-spacing: 0px;" width="100%" cellpadding="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="padding-left: 0px; padding-right: 0px;">
                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">
                                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td style="word-break: break-word; padding: 5px 0px 0px 0px; font-family: Helvetica,Arial,sans-serif;">
                                                                                                        <p style="text-align: center; font-weight: 400; color: #7f7f7f;">Gracias por elegir nuestro servicio Sr(a)</p>
                                                                                                        <h2 style="text-align: center;">' . $data->name . '</h2>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-left: 0px; padding-right: 0px;">
                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">
                                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td style="word-break: break-word; padding: 0px 0px; font-family: Helvetica,Arial,sans-serif;">
                                                                                                        <p style="text-align: center;"><span style="color: #7f7f7f;">Este es el resumen del servicio de referencia <strong>' . $data->reference . '</strong> </span></p>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 100%; vertical-align: top; padding: 0; background-color: #ffffff; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border: 0px none #ffffff;">
                                                        <table style="table-layout: fixed; width: 100%; border-spacing: 0px;" width="100%" cellpadding="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 100%; vertical-align: middle; padding-left: 0px; padding-right: 0px;" width="100%">
                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 600px;" align="center" width="600px"><img class="CToWUd" style="min-height: 600px; width: 600px;" src="' . $data->mapUrl . '" alt="' . $data->reference . '" width="640" height="640" /></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 100%; vertical-align: top; padding: 0; background-color: #ffffff; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border: 0px none #ffffff;">
                                                                        <table style="table-layout: fixed; width: 100%; border-spacing: 0px;" width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="padding-left: 0px; padding-right: 0px;">
                                                                                        <table style="background-color: #dddddd; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td style="text-align: left; word-break: break-word; font-size: 11px; padding: 10px 10px; font-family: Helvetica,Arial,sans-serif;"><span style="color: black;"> Por favor, califique nuestro servicio </span></td>
                                                                                                    <td style="text-align: right; padding: 0px 6px;">
                                                                                                        <span style="font-size: 11px; width: 20px!important; min-height: 20px; display: inline-block!important; padding: 0px 2px;"> 
                                                                                                            <a style="color: #2ba6cb; text-decoration: none;" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=1" target="_blank"> '
                    . '                                                                                         <img style="outline: none; text-decoration: none; float: left; clear: both; display: block; width: 20px; border: none;" src="http://www.transportesejecutivos.com/images/rating.png" alt="" align="left" /> '
                    . '                                                                                     </a> '
                    . '                                                                                 </span> '
                    . '                                                                                 <span style="font-size: 11px; width: 20px!important; min-height: 20px; display: inline-block!important; padding: 0px 2px;"> '
                    . '                                                                                     <a style="color: #2ba6cb; text-decoration: none;" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=2" target="_blank"> '
                    . '                                                                                         <img style="outline: none; text-decoration: none; float: left; clear: both; display: block; width: 20px; border: none;" src="http://www.transportesejecutivos.com/images/rating.png" alt="" align="left" /> '
                    . '                                                                                     </a> '
                    . '                                                                                 </span> '
                    . '                                                                                 <span style="font-size: 11px; width: 20px!important; min-height: 20px; display: inline-block!important; padding: 0px 2px;"> '
                    . '                                                                                     <a style="color: #2ba6cb; text-decoration: none;" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=3" target="_blank"> '
                    . '                                                                                         <img style="outline: none; text-decoration: none; float: left; clear: both; display: block; width: 20px; border: none;" src="http://www.transportesejecutivos.com/images/rating.png" alt="" align="left" /> '
                    . '                                                                                     </a> '
                    . '                                                                                 </span> '
                    . '                                                                                 <span style="font-size: 11px; width: 20px!important; min-height: 20px; display: inline-block!important; padding: 0px 2px;"> '
                    . '                                                                                     <a style="color: #2ba6cb; text-decoration: none;" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=4" target="_blank"> '
                    . '                                                                                         <img style="outline: none; text-decoration: none; float: left; clear: both; display: block; width: 20px; border: none;" src="http://www.transportesejecutivos.com/images/rating.png" alt="" align="left" /> '
                    . '                                                                                     </a> '
                    . '                                                                                 </span> '
                    . '                                                                                 <span style="font-size: 11px; width: 20px!important; min-height: 20px; display: inline-block!important; padding: 0px 2px;"> '
                    . '                                                                                     <a style="color: #2ba6cb; text-decoration: none;" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=5" target="_blank"> 
                                                                                                                <img style="outline: none; text-decoration: none; float: left; clear: both; display: block; width: 20px; border: none;" src="http://www.transportesejecutivos.com/images/rating.png" alt="" align="left" /> 
                                                                                                            </a> 
                                                                                                        </span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-left: 0px; padding-right: 0px;">
                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellspacing="0" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">
                                                                                        <table style="border-spacing: 0; border-collapse: collapse; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td style="width: 15%; padding-left: 0px; padding-right: 0px;" width="15%">&nbsp;</td>
                                                                                                    <td style="width: 70%; text-align: center; font-size: 18px; font-weigth: light; color: #7f7f7f; word-break: break-word; padding: 10px 0px; font-family: Helvetica,Arial,sans-serif;" width="70%">
                                                                                                        <p>' . $data->date . '</p>
                                                                                                    </td>
                                                                                                    <td style="width: 15%; padding-left: 0px; padding-right: 0px;" width="15%">&nbsp;</td>
                                                                                                </tr>
                                                                                                <tr style="background-color: rgba(70,185,216,0.1);">
                                                                                                    <td style="width: 15%; padding-left: 0px; padding-right: 0px; text-align: right;" width="15%"><img src="http://www.transportesejecutivos.com/maps/start_marker.png" alt="Inicio" width="25px" /></td>
                                                                                                    <td style="width: 70%; text-align: left; word-break: break-word; padding: 15px 15px; font-family: Helvetica,Arial,sans-serif;" width="70%">
                                                                                                        <p style="color: #46b9d8; font-size: 22px; margin: 0px;">' . $data->startTime . '</p>
                                                                                                        <p style="margin: 0px; font-size: 11px;">' . $data->source . '</p>
                                                                                                    </td>
                                                                                                    <td style="width: 15%; padding-left: 0px; padding-right: 0px;" width="15%">&nbsp;</td>
                                                                                                </tr>
                                                                                                <tr style="background-color: rgba(135,189,75,0.1);">
                                                                                                    <td style="width: 15%; padding-left: 0px; padding-right: 0px; text-align: right;" width="15%"><img src="http://www.transportesejecutivos.com/maps/end_marker.png" alt="Fin" width="25px" /></td>
                                                                                                    <td style="width: 70%; text-align: left; word-break: break-word; padding: 15px 15px; font-family: Helvetica,Arial,sans-serif;" width="70%">
                                                                                                        <p style="color: #87bd4b; font-size: 22px; margin: 0px;">' . $data->endTime . '</p>
                                                                                                        <p style="margin: 0px; font-size: 11px;">' . $data->destiny . '</p>
                                                                                                    </td>
                                                                                                    <td style="width: 15%; padding-left: 0px; padding-right: 0px;" width="15%">&nbsp;</td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-left: 0px; padding-right: 0px;">
                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 20px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">
                                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td style="word-break: break-word; padding: 5px 5px; font-family: Helvetica,Arial,sans-serif;">
                                                                                                        <p style="text-align: center; font-size: 11px; color: #7f7f7f; margin: 0px;">Viaj&oacute; con</p>
                                                                                                        <p style="text-align: center; font-size: 16px; margin: 0px;">' . $data->driverName . '</p>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-left: 0px; padding-right: 0px;">
                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 100%; vertical-align: middle; padding-left: 0px; padding-right: 0px;" width="100%">
                                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td style="width: 80px;" align="center" width="80px"><img class="CToWUd" style="min-height: 80px; width: 80px;" src="http://www.transportesejecutivos.com/app/conductores/logos/redimensionar_logo.php?imagen=cara' . $data->driverCode . '.jpg&ancho=80" alt="x.png" width="80" height="80" /></td>
                                                                                                    <td style="word-break: break-word; padding: 5px 5px; font-family: Helvetica,Arial,sans-serif; text-align: center;">
                                                                                                        <p style="font-size: 24px; margin: 0px;"><--! 14 km --> -- </p>
                                                                                                        <p style="font-size: 12px; margin: 1px;"><--! Tiempo de recorrido: 20 min. --> -- </p>
                                                                                                    </td>
                                                                                                    <td style="width: 80px;" align="center" width="80px"><img style="width: 90px; margin: 1px;" src="http://www.transportesejecutivos.com/app/conductores/logos/redimensionar_logo.php?imagen=' . $data->driverCode . '.jpg&ancho=80" alt="x.png" width="90" /></td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr style="padding-left: 0px; padding-right: 0px; background-color: black;">
                                                                    <td>
                                                                        <table style="background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%; border-spacing: 0px; border: 0px none #ffffff;" width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td><img src="http://www.transportesejecutivos.com/admin/nuevo_admin_2015/template/img/logo.png" alt="" /></td>
                                                                                    <td style="word-break: break-word; padding: 5px 5px; font-family: Helvetica,Arial,sans-serif; color: white">
                                                                                        <p style="font-size: 20px;margin: 0px;">¿Necesitas ayuda?</p>
                                                                                        <p style="font-size: 12px;margin: 5px;">
                                                                                            <span style="color: #9d9da3;">Visita </span>
                                                                                            <strong><a href="http://www.transportesejecutivos.com/" style="color: #ffffff;text-decoration: none;font-weight:bold;">nuestra página</a></strong> 
                                                                                            <span style="color: #9d9da3;">para más información</span>
                                                                                        </p>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </center>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </body>
            </html>';
        
        $this->plaintext = "Resumen de servicio con Transportes Ejecutivos con referencia: {$data->referencia}";
    }
    
    public function createResumeNotificationForDriver($data) {
      $this->html = '<table style="background-color:#dddddd;width:100%">
                       <tbody>
                          <tr>
                             <td style="padding:20px">
                                <center>
                                   <table style="width:600px" width="600px" cellspacing="0" cellpadding="0">
                                      <tbody>
                                         <tr>
                                            <td style="width:100%;vertical-align:top;padding:0;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-color:#ffffff;border-style:none;border-width:0px">
                                               <table style="table-layout:fixed;width:100%;border-spacing:0px" width="100%" cellpadding="0">
                                                  <tbody>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:10px;margin-bottom:10px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;vertical-align:middle;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td align="center" style="width:432px" width="432px"><img src="http://transportesejecutivos.com/images/horizontal_logo.png" alt="horizontal_logo.png" style="min-height:63px;width:432px" height="63" width="432" class="CToWUd"></td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                         <tr>
                                            <td style="width:100%;vertical-align:top;padding:0;background-color:#ffffff;border-top-left-radius:8px;border-top-right-radius:8px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-color:#ffffff;border-style:none;border-width:0px">
                                               <table style="table-layout:fixed;width:100%;border-spacing:0px" width="100%" cellpadding="0">
                                                  <tbody>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td style="word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif">
                                                                                   <h4 style="text-align:center"><span style="color:rgb(127,127,127)">Este es el resumen del servicio de referencia ' . $data->reference . ' </span></h4>
                                                                                </td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                         <tr>
                                            <td style="width:100%;vertical-align:top;padding:0;background-color:#ffffff;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-color:#ffffff;border-style:none;border-width:0px">
                                               <table style="table-layout:fixed;width:100%;border-spacing:0px" width="100%" cellpadding="0">
                                                  <tbody>
													<tr>
														<td style="width:100%;vertical-align:middle;padding-left:0px;padding-right:0px" width="100%">
														   <table style="border-color:#dddddd;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
															  <tbody>
																 <tr>
																	<td align="center" style="width:600px" width="600px"><img src="' . $data->mapUrl . '" alt="' . $data->reference . '" style="min-height:600px;width:600px" height="600" width="600" class="CToWUd"></td>
																 </tr>
															  </tbody>
														   </table>
														</td>
													</tr>
													<tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td style="width:15%;padding-left:0px;padding-right:0px" width="15%"></td>
                                                                                <td style="width:70%;text-align:center;word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif" width="70%">
                                                                                   <p><strong>' . $data->date . '</strong></p>
                                                                                </td>
                                                                                <td style="width:15%;padding-left:0px;padding-right:0px" width="15%"></td>
                                                                             </tr>
                                                                              <tr>
                                                                                <td style="width:15%;padding-left:0px;padding-right:0px" width="15%"></td>
                                                                                <td style="width:70%;text-align:left;word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif" width="70%">
                                                                                  <p><strong style="color:rgb(166,197,249);">' . $data->startTime . '</strong></p>
                                                                                  <p>' . $data->source . '</p>
                                                                                </td>
                                                                                <td style="width:15%;padding-left:0px;padding-right:0px" width="15%"></td>
                                                                              </tr>
                                                                              <tr>
                                                                                <td style="width:15%;padding-left:0px;padding-right:0px" width="15%"></td>
                                                                                <td style="width:70%;text-align:left;word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif" width="70%">
                                                                                  <p><strong style="color:rgb(156,188,40);">' . $data->endTime . '</strong></p>
                                                                                  <p>' . $data->destiny . '</p>
                                                                                </td>
                                                                                <td style="width:15%;padding-left:0px;padding-right:0px" width="15%"></td>
                                                                              </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:20px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td style="word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif">
                                                                                   <p style="text-align:center">' . $data->driverName . '</p>
                                                                                </td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;vertical-align:middle;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td align="center" style="width:80px; border-radius: 50%;" width="80px"><img src="http://www.transportesejecutivos.com/app/conductores/logos/redimensionar_logo.php?imagen=cara' . $data->driverCode . '.jpg&amp;ancho=80" alt="x.png" style="min-height:80px;width:80px;border-radius: 50%;" height="80" width="80" class="CToWUd"></td>
                                                                                <td align="center" style="width:80px; border-radius: 50%;" width="80px"><img src="http://www.transportesejecutivos.com/app/conductores/logos/redimensionar_logo.php?imagen=carro' . $data->driverCode . '.jpg&amp;ancho=80" alt="x.png" style="min-height:80px;width:80px;border-radius: 50%;" height="80" width="80" class="CToWUd"></td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                      </tbody>
                                   </table>
                                </center>
                             </td>
                          </tr>
                       </tbody>
                    </table>';
        
        $this->plaintext = "Resumen de servicio con Transportes Ejecutivos con referencia: {$data->referencia}";
    }
}

