<?php

require_once '../include/DbHandlerDriver.php';
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
        $db = new DbHandlerDriver();

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
 * Driver longin
 * url - /login
 * method - POST
 * params - username
 * params - password
 */
$app->post('/login', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('username', 'password'));

    // reading post params
    $username = $app->request()->post('username');
    $password = $app->request()->post('password');
    $response = array();
        
    try {
        $db = new DbHandlerDriver();
        // check for correct email and password
        if ($db->checkLogin($username, $password)) {
            // get the user by email
            $response = $db->getUserByUsername($username);

            if ($response != NULL) {
                $response["error"] = false;
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
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting data for service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});



/**
* ----------- METHODS WITH AUTHENTICATION ---------------------------------
 */

/**
 * Search driver pending services 
 * method GET
 * url /searchpendingservice          
 */
$app->get('/searchpendingservice', 'authenticate', function() {
    //$log = new LoggerHandler();
    $response = array();
    $response["error"] = false;
    $response["service"] = array();
    
    try {
        global $user;
        $db = new DbHandlerDriver();
        $response["service"] = $db->searchPendingService($user['code']);

        echoRespnse(200, $response);
    } 
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while searching for pending service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});

/**
 * Listing a pending service by id
 * method GET
 * url /getservice/:id         
 */
$app->get('/getservice/:id', 'authenticate', function($id) {
    //$log = new LoggerHandler();
    $response = array();
    $response["error"] = false;
    $response["service"] = array();
    
    try {
        global $user;
        $db = new DbHandlerDriver();
        $response["service"] = $db->getService($id, $user['code']);

        echoRespnse(200, $response);
    } 
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting data for pending service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * Listing a pending service by id
 * method GET
 * url /servicesgrouped       
 */
$app->get('/servicesgrouped', 'authenticate', function() {
    $response = array();
    try {
        global $user;
        $db = new DbHandlerDriver();
        $response = $db->getServicesGrouped($user['code']);
        $response["error"] = false;

        echoRespnse(200, $response);
    } 
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting data for service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * accept service
 * method PUT
 * params idOrden
 * url - /acceptordeclineservice/:id
 */
$app->put('/acceptordeclineservice/:id', 'authenticate', function($id) use($app) {
    global $user;   

    verifyRequiredParams(array('status'));

    $status = $app->request->put('status');
    
    $message = ($status == 1 || $status == "1" ? "The driver has accepted the service" : "The driver has not accepted the service");

    try {
        $db = new DbHandlerDriver();
        $response = array();
        $result = $db->acceptOrDeclineService($user['code'], $id, $status);
        if ($result) {
            $response["error"] = false;
            $response["message"] = $message;
        } else {
            $response["error"] = true;
            $response["message"] = "Accepting service failed. Please try again!";
        }
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while accepting serviceee: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * Search a service by date
 * method POST
 * url - /searchservice
 */
$app->post('/searchservice', 'authenticate', function() use($app) {
    $log = new LoggerHandler();
    global $user;    
    
    // check for required params
    verifyRequiredParams(array("date"));

    // reading post params
    $date = $app->request()->post('date');

    try {
        $db = new DbHandlerDriver();
        $response = $db->getServicesByDate($user, $date);
        $response["error"] = false;
        echoRespnse(200, $response);
    }
    catch (InvalidArgumentException $ex) {
        $response["error"] = true;
        $response["message"] = $ex->getMessage();
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while tracing service: " . $ex->getMessage());
        $log->writeArray($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "Ocurrió un error mientras se guardaba el segumiento";
        echoRespnse(500, $response);
    }
});

/**
 * accept service
 * method POST
 * params id
 * url - /traceservice/:id 
 */
$app->post('/traceservice/:id', 'authenticate', function($id) use($app) {
    $log = new LoggerHandler();
    global $user;    
    
    // check for required params
    verifyRequiredParams(array("image"));
    verifyNotRequiredParams(array("start", "end", 'observations'));

    // reading post params
    $start = $app->request()->post('start');
    $end = $app->request()->post('end');
    $observations = $app->request()->post('observations');
    $image = $app->request()->post('image');

    try {
        $db = new DbHandlerDriver();
        $response = array();
        
        if ($db->traceService($id, $user, $start, $end, $image, $observations)) {
            $response["error"] = false;
            $response["message"] = "Se ha hecho el seguimiento exitosamente";
        } else {
            $response["error"] = true;
            $response["message"] = "Ocurrió un error, por favor intenta de nuevo";
        }
        echoRespnse(200, $response);
    }
    catch (InvalidArgumentException $ex) {
        $response["error"] = true;
        $response["message"] = $ex->getMessage();
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while tracing service: " . $ex->getMessage());
        $log->writeArray($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "Ocurrió un error mientras se guardaba el segumiento";
        echoRespnse(500, $response);
    }
});


/**
 * Confirma el servicio, setea el estado de B1HA (Botón una hora antes)
 * method POST
 * params id
 * url - /confirmservice/:id 
 */
$app->post('/confirmservice/:id', 'authenticate', function($id) use($app) {
    $log = new LoggerHandler();
    global $user;    

    try {
        $db = new DbHandlerDriver();
        $response = array();

        if ($db->confirmService($user, $id)) {
            $response["error"] = false;
            $response["message"] = "Se ha actualizado la orden a estoy en camino";
        } else {
            $response["error"] = true;
            $response["message"] = "No se pudo actualizar el estado, por favor intenta de nuevo";
        }

        echoRespnse(200, $response);
    }
    catch (InvalidArgumentException $ex) {
        $response["error"] = true;
        $response["message"] = $ex->getMessage();
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while setting B1HA log : " . $ex->getMessage());
        $log->writeArray($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});

/**
 * accept service
 * method POST
 * params id
 * url - /setonsource/:id 
 */
$app->post('/setonsource/:id', 'authenticate', function($id) use($app) {
    $log = new LoggerHandler();
    global $user;    

    try {
        $db = new DbHandlerDriver();
        $response = array();

        if ($db->setOnSource($user, $id)) {
            $response["error"] = false;
            $response["message"] = "Se ha actualizado la orden a Estoy en el sitio";
        } else {
            $response["error"] = true;
            $response["message"] = "No se pudo actualizar el estado, por favor intenta de nuevo";
        }

        echoRespnse(200, $response);
    }
    catch (InvalidArgumentException $ex) {
        $response["error"] = true;
        $response["message"] = $ex->getMessage();
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while setting BLS log: " . $ex->getMessage());
        $log->writeArray($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * accept service
 * method POST
 * params id
 * url - /startservice/:id 
 */
$app->post('/startservice/:id', 'authenticate', function($id) use($app) {
    $log = new LoggerHandler();
    global $user;    

    try {
        $db = new DbHandlerDriver();
        $response = array();

        if ($db->startService($user, $id)) {
            $response["error"] = false;
            $response["message"] = "Se ha iniciado el servicio exitosamente";
        } else {
            $response["error"] = true;
            $response["message"] = "No se pudo iniciar el servicio, por favor intenta de nuevo";
        }

        echoRespnse(200, $response);
    }
    catch (InvalidArgumentException $ex) {
        $response["error"] = true;
        $response["message"] = $ex->getMessage();
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while starting service: " . $ex->getMessage());
        $log->writeArray($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * Save the driver location updates during the service
 * method POST
 * params id
 * url - /setlocation/:id 
 */
$app->post('/setlocation/:id', 'authenticate', function($id) use($app) {
    $log = new LoggerHandler();
    global $user;    
    
    try {
        // check for required params
        verifyRequiredParams(array("longitude", "latitude"));

        // reading post params
        $longitude = $app->request()->post('longitude');
        $latitude = $app->request()->post('latitude');

        $db = new DbHandlerDriver();
        $response = array();
        if ($db->setLocation($user, $id, $latitude, $longitude)) {
            $response["error"] = false;
            $response["message"] = "Location setted succesusfuly";
        }
        else {
            $response["error"] = true;
            $response["message"] = "Error while setting location, please try again";
        }

        echoRespnse(200, $response);
    }
    catch (InvalidArgumentException $ex) {
        $response["error"] = true;
        $response["message"] = $ex->getMessage();
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while setting location driver on service: " . $ex->getMessage());
        $log->writeArray($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * Finish a service
 * method POST
 * params id
 * url - /finishservice/:id 
 */
$app->post('/finishservice/:id', 'authenticate', function($id) use($app) {
    $log = new LoggerHandler();
    global $user;    

    verifyNotRequiredParams(array('observations', 'image'));
    $image = $app->request()->post('image');
    $observations = $app->request()->post('observations');

    try {
        $db = new DbHandlerDriver();
        $response = array();

        $db->finishService($user, $id, $observations, $image);
        $response["error"] = false;
        $response["message"] = "Se ha finalizado el servicio exitosamente";
        
        echoRespnse(200, $response);
    }
    catch (InvalidArgumentException $ex) {
        $response["error"] = true;
        $response["message"] = $ex->getMessage();
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while finishing service: " . $ex->getMessage());
        $log->writeArray($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});

/**
 * Save the driver location updates, Before reaching the destination
 * method POST
 * params id
 * url - /setprelocation/:id 
 */
$app->post('/setprelocation/:id', 'authenticate', function($id) use($app) {
    $log = new LoggerHandler();
    global $user;    
    
        // check for required params
        verifyRequiredParams(array("longitude", "latitude"));

        // reading post params
        $longitude = $app->request()->post('longitude');
        $latitude = $app->request()->post('latitude');
        
    try {
        
        $db = new DbHandlerDriver();
        $response = array();
        if ($db->setPreLocation($user, $id, $latitude, $longitude)) {
            $response["error"] = false;
            $response["message"] = "Prelocation setted succesusfuly";
        }
        else {
            $response["error"] = true;
            $response["message"] = "Error while setting prelocation, please try again";
        }

        echoRespnse(200, $response);
    }
    catch (InvalidArgumentException $ex) {
        $response["error"] = true;
        $response["message"] = $ex->getMessage();
        echoRespnse(400, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while setting prelocation driver on the way: " . $ex->getMessage());
        $log->writeArray($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
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
 * Verifying required params posted or not
 */
function verifyNotRequiredParams($required_fields) {
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
        if (!isset($request_params[$field])) {
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
* Validate if is a user administrador
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

/**
 * This function replace the original php function apache_request_headers
 * @return array
 */
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