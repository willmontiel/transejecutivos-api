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
 * User recover password
 * url - /recoverpassword
 * method - POST
 * params - username
 */
$app->post('/recoverpassword', function() use ($app) {
    verifyRequiredParams(array('username'));

    $username = $app->request()->post('username');    
    $response = array();
    try {
        $db = new DbHandler();

        $res = $db->recoverPassword($username);

        if ($res) {
            $response["error"] = false;
            $response['message'] = 'We send you a mail with instructions for to reset your password';
        } 
        else {
            $response['error'] = true;
            $response['message'] = "An error occurred. Please try again";
        }

        echoRespnse(200, $response);
    } 
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting data for service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["services"] = array("An error occurred, contact the administrator");
        echoRespnse(500, $response);
    }
});

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
        
    try {
        $db = new DbHandler();
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
        $response["services"] = array("An error occurred, contact the administrator");
        echoRespnse(500, $response);
    }
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
    
    try {
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
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting data for service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["services"] = array("An error occurred, contact the administrator");
        echoRespnse(500, $response);
    }
});



/**
 * Listing all user services since today until next days
 * method GET
 * url /services          
 */
$app->get('/services', 'authenticate', function() {
    //$log = new LoggerHandler();
    $response = array();
    $response["error"] = false;
    $response["services"] = array();
    
    try {
        global $user;
        $db = new DbHandler();
        $response["services"] = $db->getServices($user['code']);

        echoRespnse(200, $response);
    } 
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting data for service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["services"] = array("An error occurred, contact the administrator");
        echoRespnse(500, $response);
    }
});


/**
 * Listing all user services by date
 * method POST
 * url /service         
 */
$app->post('/service', 'authenticate', function() use ($app) {
    //$log = new LoggerHandler();
    // check for required params
    verifyRequiredParams(array('date'));
    // reading post params
    $date = $app->request()->post('date');
    $response = array();
    $response["error"] = false;
    $response["services"] = array();
    
    try {
        global $user;
        $db = new DbHandler();
        $response["services"] = $db->getServicesByDate($user['code'], $date);
        echoRespnse(200, $response);
    } 
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting data for service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["services"] = array("An error occurred, contact the administrator");
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