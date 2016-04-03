<?php

require_once 'PHPMailer/class.phpmailer.php';

class MailSender {
    public $mail;
    public $PHPMailer;
    public $data;
    
    public function __construct() {
        $this->PHPMailer = new PHPMailer(true);
        $this->PHPMailer->IsSMTP();
    }

    public function setMailData($data) {
        $this->data = $data;
    }
    
    public function sendMail() {
        try {
            $this->PHPMailer->Host       = "smtp.mandrillapp.com"; // SMTP server
            $this->PHPMailer->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
            $this->PHPMailer->SMTPAuth   = true;                  // enable SMTP authentication
            $this->PHPMailer->Host       = "smtp.mandrillapp.com";      // sets GMAIL as the SMTP server
            $this->PHPMailer->Port       = 587;                   // set the SMTP port for the GMAIL server
            $this->PHPMailer->Username   = "info@transportesejecutivos.com";  // GMAIL username
            $this->PHPMailer->Password   = "bkTq6yOR5BdZXHR7IHz6Rw";            // GMAIL password
            $this->PHPMailer->AddAddress($this->data->email1, $this->data->email1);
            $this->PHPMailer->AddAddress($this->data->email2, $this->data->email2);
            $this->PHPMailer->AddAddress($this->data->email3, $this->data->email3);
            $this->PHPMailer->SetFrom('info@transportesejecutivos.com', 'Transportes Ejecutivos');
            $this->PHPMailer->AddReplyTo('info@transportesejecutivos.com', 'Transportesejecutivos.com');
            $this->PHPMailer->Subject = $this->data->subject;
            $this->PHPMailer->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
            $this->PHPMailer->MsgHTML($this->mail);
            $this->PHPMailer->Send();
        } 
        catch (phpmailerException $e) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } 
        catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
    }
    
    public function createDriverNotification($data) {
        $this->mail = "
            <table width='600' border='0' align='center' cellpadding='0' cellspacing='0'>
            <tr>
            <td height='30' align='center' valign='middle'>
            FAVOR HABILITAR MOSTRAR IMAGENES
            </td>
            </tr>
            </table>

            <table width='600' border='0' align='center' cellpadding='0' cellspacing='0'>
                  <tr>
                    <td height='25' align='center'><table width='100%' border='0' align='center' cellpadding='3' cellspacing='0'>
                      <tr>
                        <td align='center' bgcolor='#FFFFFF'><span class='logo'><a href='http://www.transportesejecutivos.com'><img src='http://transportesejecutivos.com/html/images/logo.jpg' width='600' height='61' align='absmiddle' /></a></span></td>
                      </tr>
                      <tr>
                        <td align='left' valign='top' class='texto_10'><span class='texto_8'><span class='Estilo2'><a href='../admin.php'></a></span></span>
                          <table width='600' height='700' border='1' align='center' cellpadding='0' cellspacing='0' bordercolor='#999999'>
                            <tr>
                              <td align='center' valign='middle'><a href='' class='bar'><strong> </strong></a>
                                <table width='97%' border='0' cellspacing='0' cellpadding='5'>
                                  <tr>
                                    <td colspan='3' align='center' bgcolor='#F0F0F0'><h2><strong><span class='Estilo6 texto_10'>Orden de Servicio</span></strong><strong> {$data->ref}<span class='Estilo6 texto_10'> para </span></strong><strong></strong>{$data->company}<strong>  - {$data->clientService}</strong><br />
                                      </br>
                                      <img src='http://transportesejecutivos.com/app/empresas/logos/redimensionar_logo.php?imagen=".$logo_empresa.".jpg&amp;ancho=200' /> </h2>
                                      <h2><strong>Fecha y Hora del Servicio:</strong> {$data->startDate} </h2></td>
                                  </tr>
                                  <tr>
                                    <td height='2' colspan='3' align='right' valign='top' bgcolor='#CCCCCC'></td>
                                  </tr>
                                  <tr>
                                    <td width='54%' align='center' bgcolor='#F0F0F0'><strong>Conductor / Driver :

                                    </strong></td>
                                    <td width='46%' align='center' bgcolor='#E2E2E2'><strong>Veh&iacute;culo:</strong></td>
                                  </tr>
                                  <tr>
                                    <td align='center'></br>
                                      <img src='http://transportesejecutivos.com/app/conductores/logos/redimensionar_logo.php?imagen=cara".$row_cond['codigo'].".jpg&amp;ancho=220' /></td>
                                    <td align='center'><img src='http://transportesejecutivos.com/app/conductores/logos/redimensionar_logo.php?imagen=carro".$row_cond['codigo'].".jpg&amp;ancho=220' /></td>
                                  </tr>
                                  <tr>
                                    <td align='center' bgcolor='#F0F0F0'><strong>Nombre / Name:</strong> ".$row_cond['nombre']." ".$row_cond['apellido']."</td>
                                    <td align='center' bgcolor='#E2E2E2'><strong>Tpo de Veh&iacute;culo: </strong>".$row_cond['carro_tipo']."</td>
                                  </tr>
                                  <tr>
                                    <td align='center'><strong>C.C. / I. D. :</strong>".$row_cond['telefono3']."</td>
                                    <td align='center'><strong>Marca: </strong>".$row_cond['marca']."</td>
                                  </tr>
                                  <tr>
                                    <td align='center' bgcolor='#F0F0F0'><strong>Idiomas Hablados:</strong> ".$row_cond['idioma1']." ".$row_cond['idioma2']." ".$row_cond['idioma3']."</td>
                                    <td align='center' bgcolor='#E2E2E2'><strong>Modelo:</strong> ".$row_cond['modelo']."</td>
                                  </tr>
                                  <tr>
                                    <td align='center'><strong>Celular / Cell Phone : </strong>".$row_cond['telefono1']."</td>
                                    <td align='center'><strong>Placa: </strong>".$row_cond['placa']."</td>
                                  </tr>
                                  <tr>
                                    <td align='center' bgcolor='#F0F0F0'><strong>Telefono Alterno/Alt. phone :</strong> ".$row_cond['telefono2']."</td>
                                    <td align='center' bgcolor='#E2E2E2'>".$boucher."</td>
                                  </tr>
                                  <tr>
                                    <td align='center'>&nbsp;</td>
                                    <td align='center'>&nbsp;</td>
                                  </tr>

                                  <tr>
                                    <td height='2' colspan='3' align='right' valign='top' bgcolor='#CCCCCC'></td>
                                  </tr>
                                  <tr>
                                    <td colspan='2' align='center' bgcolor='#F0F0F0'><strong>Direcci&oacute;n Inicio (P.U.): ".$row_orden['ciudad_inicio']." - ".$row_orden['dir_origen']."</strong></td>
                                  </tr>
                                  <tr>
                                    <td colspan='2' align='center' bgcolor='#E2E2E2'><strong>Direcci&oacute;n Destino (D.O.): ".$row_orden['ciudad_destino']." - ".$row_orden['dir_destino']."</strong></td>
                                  </tr>
                                  <tr>
                                    <td height='2' colspan='3' align='right' valign='top' bgcolor='#CCCCCC'></td>
                                  </tr>
                                  <tr>
                                    <td colspan='3' align='center' bgcolor='#F0F0F0'><h2><strong> Aerolinea: ".$row_orden['aerolinea']." - ".$row_orden['vuelo']."</strong></h2></td>
                                  </tr>
                                </table>
                                <table width='97%' border='0' cellspacing='0' cellpadding='5'>
                                  <tr>
                                    <td height='2' colspan='4' align='right' valign='top' bgcolor='#CCCCCC'></td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' align='center' valign='middle'><strong>Pasajeros  a Transportar / Passanger Names</strong></td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' align='center' bgcolor='#F0F0F0'><strong>1.

                                      </strong>".$pax1."</td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' align='center'><strong>2. </strong>".$pax2.";</td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' align='center' bgcolor='#F0F0F0'><strong>3. </strong>".$pax3."</td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' align='center'><strong>4. </strong>".$pax4."</td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' align='center' bgcolor='#F0F0F0'><strong>5. </strong>".$pax5."</td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' align='center' bgcolor='#F0F0F0'><strong>Cantidad de Pasajeros: ".$row_orden['cant_pax']."</strong></td>
                                  </tr>
                                  <tr>
                                    <td height='2' colspan='4' align='right' bgcolor='#CCCCCC'></td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' align='center'><strong><span class='titulo1'>Observaciones y/o Indicaciones Adicionales: ".$row_orden['obaservaciones']."<br />
            Observaciones del Conductor: ".$row_orden['observaciones_cond']."</span></strong></td>
                                  </tr>
                                  <tr>
                                    <td height='2' colspan='4' align='right' bgcolor='#CCCCCC'></td>
                                  </tr>
                                  <!-- MERCANCIA -->
                                ".$mercancia."  <tr>

                                    <td height='2' colspan='4' align='right' bgcolor='#CCCCCC'></td>
                                  </tr>
                                  <tr>
                                    <td colspan='2' align='left' bgcolor='#F0F0F0'><strong>Fecha  Orden / Order Date:</strong> ".$row_orden['fecha_e']." - ".$row_orden['hora_e']."></td>
                                    <td colspan='2' align='left'>
                                                            <strong>
                                                            Por / By : ".$row_elaborado['nombre']." ".$row_elaborado['apellido']." - ".$row_elaborado['empresa']."</strong></td>
                                  </tr>
                                  <tr>
                                    <td height='2' colspan='4' align='right' bgcolor='#CCCCCC'></td>
                                  </tr>
                                  <!--Mercancia -->
                                  <tr>
                                    <td colspan='4' align='center' bgcolor='#F0F0F0'><strong><a href='http://transportesejecutivos.com/app/pasajeros/pdf/".$pdf.".pdf'>Aviso en Formato PDF: <img src='http://transportesejecutivos.com/PDF.jpeg' width='65' height='66' /><br>
                                    </a><a href='http://transportesejecutivos.com/app/icalendar/ics/".$row_orden ['referencia'].".ics'>Agregue este evento a su calendario: <img src='http://transportesejecutivos.com/ical.jpg' width='65' height='66' /></a></strong></td>
                                  </tr>
                                   <tr>
                                    <td colspan='4' align='center' bgcolor='#F0F0F0'>

                                    <font size='-2'> 

                                <strong>Compromisos del Conductor :</strong><br />
                                    1.- Puntualidad (llegar 15 minutos antes de la hora que fue solicitado el servicio) <br />
                                    2.-  Respete TODAS  las normas de transito vehicular. <br />
                                    3.- Usar su cinturon de seguridad y asegurese de que la pasajero tambien cumpla con este estandar. <br />
                                    4.- Portar toda la Documentacion del Vehiculo  VIGENTE, . <br />
                                    5.- Contestar celulares con manos libres. <br />
                                    6.- Asegurese de que el vehiculo este siempre limpio (interno y externo) para cada servicio. <br />
                                    7.- Use ropa adecuada. Esto es Camisa de manga, Pantalon de drill y zapatos cerrados. Nunca Camisetas, jeans o tenis. Para Bogota y Medellin USAR SIEMPRE Traje. <br />
                                    8.- Ser Siempre Cordial. Bajarse del vehiculo para abrir la puerta al pasajero y recibirle el equipaje. <br />
                                    9.- No utilizar radio a menos que el pasajero lo solicite. <br />
                                    10.- No entablar o proponer conversaciones con el pasajero. Solo contestar lo preguntado. <br />
                                    11.- NUNCA llevar acompanantes para el cumplimiento del servicio.<br />
                                    12.-  Reportar de inmediato al coordinador de Transportes ejecutivos si el pasajero solicita algo distinto a lo solicitado en la orden de servicio. No se pagara ningun servicio adicional que no este autorizado previamente<br />
                                    <strong><br />
                                    Despues del servicio: </strong><br />
                                    1.- Compruebe si los pasajeros dejaron algun objeto olvidado y avisar de manera inmediata. <br />
                                    2.- Hacer firmar la constancia de prestacion de Servicio al pasajero. (UNA POR CADA VIAJE) Sin esta constancia NO SE PAGA EL VIAJE.<br />
                                    <br />
                                    <strong>Condiciones: </strong><br />
                                    1.- Cualquier cambio de conductor o de vehiculo, debe ser autorizado por transportes ejecutivos. Bajo ninguna circunstancia se debe enviar un conductor de reemplazo sin previa autorizacion nuestra. <br />
                                    2.- Para ciudades diferentes a Bogota y Barranquilla el conductor debe siempre esperar al pasajero en el muelle de salida con un aviso impreso en computador. Contactarlo previamente via Mensaje de Texto o llamada. Siempre debe guardar estos registros por una semana minimo<br />
                                    3.- En caso que por extrema urgencia, y solo para casos excepcionales, cuando vea que no podra estar en el sitio en la hora establecida, contactenos de inmediato para nosotros informar de inmediato  al cliente y buscar otro vehiculo. <br />
                                    <br />
                                    <strong>Telefonos de Contacto </strong><br />
                                    Oficina Nacional (+572)5518641 , Avantel ID: (+572)*8524 (+572)3503077337 (+57)3113874082 (+57) 3175166008 )info@transportesejecutivos.com<br />
                                    Aeropuerto El Dorado (BOG) Bogota (+57) 3117435261 ID 2* 10792 3503083503 eldorado@transportesejecutivos.com
                                    Aeropuerto Ernesto Cortissoz (BAQ) Barranquilla  (+57)1484*1 (57+)3503093534

                                    </font>

                                    </td>
                                  </tr>
                                </table>
                            </td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                  </tr>
                </table>

            ";
    }
}

