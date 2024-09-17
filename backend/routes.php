<?php
require_once 'config/dp.php';
require_once 'controllers/UserController.php';
require_once 'middlewares/JWTMiddleware.php';

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$jwtMiddleware = new JWTMiddleware($_ENV['JWT_SECRET']);

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


$path = ltrim($request_uri, '/');
$elements = explode('/', $path);

// Route the request
switch($request_method) {
    case 'GET':
        if (empty($elements[0])) {
            // Handle root path
            echo "Welcome to the API";
        } elseif ($elements[0] == 'users' && empty($elements[1])) {
            $userController->index();
        } elseif ($elements[0] == 'users' && isset($elements[1])) {
            $userController->read($elements[1]);
        } elseif ($elements[0] == 'protected'){
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
        
            if ($token && $jwtMiddleware->verifyToken($token)) {
                echo json_encode(["message" => "Access granted to protected resource"]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized"]);
            }
        }
        break;
    case 'POST':
        if ($elements[0] == 'users' && empty($elements[1])) {
            $userController->create();
        } elseif($elements[0]== 'login'){
            $userController->login();
        }
        break;
    case 'PUT':
        if ($elements[0] == 'users' && isset($elements[1])) {
            $userController->update($elements[1]);
        }
        break;
    case 'DELETE':
        if ($elements[0] == 'users' && isset($elements[1])) {
            $userController->delete($elements[1]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}
