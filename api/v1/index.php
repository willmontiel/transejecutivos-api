<?php

require_once '../include/DbHandler.php';
//require_once '../include/PassHash.php';
require_once '../include/LoggerHandler.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user = NULL;

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers2();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['AUTHORIZATION'])) {
        

        $db = new DbHandler();

        // get the api key
        $api_key = $headers['AUTHORIZATION'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $user;
            // get user primary key id
            $user = $db->getUser($api_key);
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * ----------- METHODS WITHOUT AUTHENTICATION ---------------------------------
 */

/**
 * User Login
 * url - /login
 * method - POST
 * params - email, password
 */
$app->post('/login', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('username', 'password'));

    // reading post params
    $username = $app->request()->post('username');
    $password = $app->request()->post('password');
    $response = array();

    $db = new DbHandler();
    // check for correct email and password
    if ($db->checkLogin($username, $password)) {
        // get the user by email
        $user = $db->getUserByUsername($username);

        if ($user != NULL) {
            $response["error"] = false;
            $response["id"] = $user['name'];
            $response["username"] = $user['username'];
            $response['name'] = $user['name'];
            $response["lastname"] = $user['lastname'];
            $response["mail1"] = $user['mail1'];
            $response["mail2"] = $user['mail2'];
            $response["company"] = $user['company'];
            $response["api_key"] = $user['api_key'];
            $response["type"] = $user['type'];
        } else {
            // unknown error occurred
            $response['error'] = true;
            $response['message'] = "An error occurred. Please try again";
        }
    } else {
        // user credentials are wrong
        $response['error'] = true;
        $response['message'] = 'Login failed. Incorrect credentials';
    }

    echoRespnse(200, $response);
});

/*
 * ------------------------ METHODS WITH AUTHENTICATION ------------------------
 */
/**
 * Generating apikey for user
 * method PUT
 * params username
 * url - /apikey
 */
$app->put('/apikey', 'authenticate', function() use($app) {
    // check for required params
    verifyRequiredParams(array('username'));

    global $user;    

    validateUserAdmin($user);

    $username = $app->request->put('username');

    $db = new DbHandler();
    $response = array();

    //Generating Api Key
    $result = $db->updateApiKey($username);

    if ($result) {
        // Apikey generated successfully
        $response["error"] = false;
        $response["message"] = "Apikey generated successfully for user: {$username}";
    } else {
        // Apikey failed to generate
        $response["error"] = true;
        $response["message"] = "Apikey failed to generate. Please try again!";
    }
    echoRespnse(200, $response);
});



/**
 * Listing all user services since today until next days
 * method GET
 * url /services          
 */
$app->get('/services', 'authenticate', function() {
    global $user;
    $response = array();
    $db = new DbHandler();

    // fetching all user tasks
    $result = $db->getServices($user['company']);

    $response["error"] = false;
    $response["services"] = array();

    // looping through result and preparing tasks array
    while ($service = $result->fetch_assoc()) {
        $tmp = array();

        //Service information
        $tmp["service_id"] = $service["orden_id"];
        $tmp["ref"] = $service["referencia"];
        $tmp["date"] = $service["fecha_e"] . " " . $service["hora_e"];
        $tmp["start_date"] = $service["fecha_s"] . " " . $service['hora_s1'] . ":" . $service['hora_s2'] . ":" . $service['hora_s3'];
        $tmp["end_date"] = null;
        $tmp["fly"] = $service["vuelo"];
        $tmp["aeroline"] = $service["aerolinea"];
        $tmp["company"] = $service["empresa"];
        $tmp["passenger_type"] = $service["tipo_s"];
        $tmp["pax_cant"] = $service["cant_pax"];
        $tmp["represent"] = $service["representando"];
        //$tmp["source"] = $service["ciudad_inicio"] . ", " . $service['dir_origen'];
        //$tmp["destiny"] = $service["ciudad_destino"] . ", " . $service['dir_destino'];
        $tmp["source"] = $service["ciudad_inicio"];
        $tmp["destiny"] = $service["ciudad_destino"];
        $tmp["service_observations"] = $service["obaservaciones"];
        //Driver information
        $tmp["driver_id"] = $service["conductor_id"];
        $tmp["driver_code"] = $service["conductor_codigo"];
        $tmp["driver_name"] = $service["conductor_nombre"];
        $tmp["driver_lastname"] = $service["conductor_apellido"];
        $tmp["driver_phone1"] = $service["conductor_telefono1"];
        $tmp["driver_phone1"] = $service["conductor_telefono2"];
        $tmp["driver_address"] = $service["conductor_direccion"];
        $tmp["driver_city"] = $service["conductor_ciudad"];
        $tmp["driver_email"] = $service["conductor_email"];
        $tmp["car_type"] = $service["carro_tipo"];
        $tmp["car_brand"] = $service["marca"];
        $tmp["car_model"] = $service["modelo"];
        $tmp["car_color"] = $service["color"];
        $tmp["car_license_plate"] = $service["placa"];
        $tmp["driver_status"] = $service["estado"];
        //Passenger information
        $tmp["passenger_id"] = $service["pasajeros_id"];
        $tmp["passenger_code"] = $service["pasajeros_codigo"];
        $tmp["passenger_name"] = $service["pasajeros_nombre"];
        $tmp["passenger_lastname"] = $service["pasajeros_apellido"];
        $tmp["passenger_company"] = $service["pasajeros_empresa"];
        $tmp["passenger_phone1"] = $service["pasajeros_telefono1"];
        $tmp["passenger_phone2"] = $service["pasajeros_telefono2"];
        $tmp["passenger_email"] = $service["pasajeros_correo1"];
        $tmp["passenger_address"] = $service["pasajeros_direccion"];
        $tmp["passenger_city"] = $service["pasajeros_ciudad"];

        

        //$log = new LoggerHandler();
        //$log->writeArray($tmp);

        array_push($response["services"], $tmp);
        //$response["services"][] = $tmp;
    }

    $log = new LoggerHandler();
    $log->writeArray($response);

    echoRespnse(200, $response);
});

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}


/**
* Validate if is an administrador
*/
function validateUserAdmin($user) {
    $app = \Slim\Slim::getInstance();
    if ($user['type'] != "superadministrador") {
        $response["error"] = true;
        $response["message"] = "Access denied, you have not permission for to do this action";
        echoRespnse(401, $response);
        $app->stop();
    }
}

function apache_request_headers2() {
    $arh = array();
    $rx_http = '/\AHTTP_/';
    foreach($_SERVER as $key => $val) {
        if (preg_match($rx_http, $key)) {
            $arh_key = preg_replace($rx_http, '', $key);
            $rx_matches = array();
            // do some nasty string manipulations to restore the original letter case
            // this should work in most cases
            $rx_matches = explode('_', $arh_key);
            if(count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                $arh_key = implode('-', $rx_matches);
            }
            $arh[$arh_key] = $val;
        }
    }
  return( $arh );
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();