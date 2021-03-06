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
            //$log = new LoggerHandler();
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
 * Test to Get the driver prelocation
 * method GET
 * url /getprelocationtest/:id          
 */
$app->get('/getprelocationtest/:id', function($id) {
    //$log = new LoggerHandler();
    $response = array();
    $response["error"] = false;
    $response["location"] = array(
        "latitude" => 3.537972,
        "longitude" => -76.297166
    );
    

	echoRespnse(200, $response);
    
});
 
 
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
        $response["message"] = "An error occurred, contact the administrator";
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
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * User reset password
 * url - /resetpassword
 * method - POST
 * params - username, password
 */
$app->post('/resetpassword', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('username', 'password'));

    // reading post params
    $username = $app->request()->post('username');
    $password = $app->request()->post('password');
    $response = array();
        
    try {
        $db = new DbHandler();
        // check for correct email and password
        if ($db->changePassword($username, $password)) {
            $response['error'] = false;
            $response['message'] = "Password resetted successfully";
        } 
        else {
            $response['error'] = true;
            $response['message'] = "An error occurred. Please try again";
        }

        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while resetting password: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
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
        $log->writeString("Exception while creating/updating apikey: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * Listing all user services since today until next days
 * method GET
 * url /getprelocation/:id          
 */
$app->get('/getprelocation/:id', 'authenticate', function($id) {
    //$log = new LoggerHandler();
    $response = array();
    $response["error"] = false;
    $response["location"] = array(
        "latitude" => 0,
        "longitude" => 0
    );
    
    try {
        global $user;
        $db = new DbHandler();
        $response["location"] = $db->getPrelocation($id);

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
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});

/**
 * Listing all user services since today until next days
 * method GET
 * url /servicesgrouped          
 */
$app->get('/servicesgrouped', 'authenticate', function() {
    //$log = new LoggerHandler();
    $response = array();
    
    try {
        global $user;
        $db = new DbHandler();
        $response = $db->getServicesGrouped($user);
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
 * Listing all car types
 * method GET
 * url /getcartypes
 */
$app->get('/getcartypes', 'authenticate', function() {
    //$log = new LoggerHandler();
    $response = array();
    
    try {
        global $user;
        $db = new DbHandler();
        $response = $db->getCarTypes();
        $response["error"] = false;

        echoRespnse(200, $response);
    } 
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting car types: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});

/**
 * Listing all aerolines
 * method GET
 * url /getaerolines
 */
$app->get('/getaerolines', 'authenticate', function() {
    //$log = new LoggerHandler();
    $response = array();

    try {
        global $user;
        $db = new DbHandler();
        $response = $db->getAerolines();
        $response["error"] = false;

        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting car types: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * Listing all cities
 * method GET
 * url /getcities
 */
$app->get('/getcities', 'authenticate', function() {
    //$log = new LoggerHandler();
    $response = array();

    try {
        global $user;
        $db = new DbHandler();
        $response = $db->getCities();
        $response["error"] = false;

        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while getting car types: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});


/**
 * Listing all user services by date
 * method POST
 * url /service         
 */
$app->post('/service', 'authenticate', function() use ($app) {
    $log = new LoggerHandler();
    // check for required params
    verifyRequiredParams(array('date'));
    // reading post params
    $date = $app->request()->post('date');
    $response = array();
    
    try {
        global $user;
        
        $db = new DbHandler();
        $response = $db->getServicesByDate($user, $date);
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
 * Request a service
 * method POST
 * url /requestservice         
 */
$app->post('/requestservice', 'authenticate', function() use ($app) {
    $log = new LoggerHandler();
    // check for required params
    verifyRequiredParams(array('carType', 'passengers', 'date', 'time', 'startCity', 'startAddress', 'endCity', 'endAddress'));
    verifyNotRequiredParams(array('observations'));
    // reading post params
    $data = new stdClass();
    $data->carType = $app->request()->post('carType');
    $data->passengers = $app->request()->post('passengers');
    $data->date = $app->request()->post('date');
    $data->time = $app->request()->post('time');
    $data->startCity = $app->request()->post('startCity');
    $data->startAddress = $app->request()->post('startAddress');
    $data->endCity = $app->request()->post('endCity');
    $data->endAddress = $app->request()->post('endAddress');
    $data->aeroline = $app->request()->post('aeroline');
    $data->fly = $app->request()->post('fly');
    $data->observations = $app->request()->post('observations');

    $log->writeArray($data);

    $response = array();
    
    try {
        global $user;
        $db = new DbHandler();

        if ($db->requestService($user, $data)) {
            $response["message"] = "Service requested successfully";
            $response["error"] = false;
        } else {
            $response["error"] = true;
            $response["message"] = "Error while requesting service. Please try again!";
        }

        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while requesting service: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());

        $response["error"] = true;
        $response["message"] = "An error occurred, contact the administrator";
        echoRespnse(500, $response);
    }
});

/**
 * Edit user data
 * method PUT
 * url /updateprofile         
 */
$app->put('/updateprofile', 'authenticate', function() use ($app) {
    $log = new LoggerHandler();
    // check for required params
    verifyRequiredParams(array('name', 'lastName', 'email1', 'phone1'));
    verifyNotRequiredParams(array('email2', 'phone2', 'password', 'notifications', 'gcm_token'));

    // reading post params
    $name = $app->request()->post('name');
    $lastName = $app->request()->post('lastName');
    $email1 = $app->request()->post('email1');
    $email2 = $app->request()->post('email2');
    $phone1 = $app->request()->post('phone1');
    $phone2 = $app->request()->post('phone2');
    $password = $app->request()->post('password');
    $notifications = $app->request()->post('notifications');
    $token = $app->request()->post('gcm_token');

    validateEmail($email1);
    $email2 = trim($email2);

    if (!empty($email2)) {
        validateEmail($email2);
    }

    try {
        global $user;
        $db = new DbHandler();
        $response = array();
        //Generating Api Key
        $result = $db->updateProfile($user['username'], $name, $lastName, $email1, $email2, $phone1, $phone2, $password, $notifications, $token);
        if ($result) {
            $response = $db->getUserByUsername($user['username']);
            $response["error"] = false;
        } else {
            // Apikey failed to generate
            $response["error"] = true;
            $response["message"] = "Error while updating profile user. Please try again!";
        }
        echoRespnse(200, $response);
    }
    catch (Exception $ex) {
        $log = new LoggerHandler();
        $log->writeString("Exception while updating user profile: " . $ex->getMessage());
        $log->writeString($ex->getTraceAsString());
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
