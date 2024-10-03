<?php
require_once 'config/dp.php';
use Skand\Backend\Controllers\UserController;
use Skand\Backend\Controllers\CampingController;
use Skand\Backend\Controllers\TrailController;
use Skand\Backend\Controllers\BookingController;
use Skand\Backend\Controllers\PointOfInterestController;
require_once 'middlewares/JWTMiddleware.php';

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$campingController = new CampingController($db);
$trailController = new TrailController($db);
$bookingController = new BookingController($db);
$pointOfInterestController = new PointOfInterestController($db);
$jwtMiddleware = new JWTMiddleware($_ENV['JWT_SECRET']);

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


$path = ltrim($request_uri, '/');
$elements = explode('/', $path);

// routes :
// http://localhost:8000/pointsofinterest/
// http://localhost:8000/users/
// http://localhost:8000/login/
// http://localhost:8000/campings/
// http://localhost:8000/trails/
// http://localhost:8000/bookings/
switch($request_method) {
    case 'GET':
        if (empty($elements[0])) {
            // Handle root path
            echo "Welcome to the API";
        } elseif ($elements[0] == 'users' && empty($elements[1])) {
            $userController->index();
        } elseif ($elements[0] == 'users' && isset($elements[1])) {
            $userController->read($elements[1]);
        } elseif ($elements[0] == 'campings' && empty($elements[1])) {
            $campingController->index();
        } elseif ($elements[0] == 'campings' && isset($elements[1])) {
            $campingController->read($elements[1]);
        } elseif ($elements[0] == 'trails' && empty($elements[1])) {
            $trailController->index();
        } elseif ($elements[0] == 'trails' && isset($elements[1])) {
            $trailController->read($elements[1]);
        } elseif ($elements[0] == 'trails' && $elements[1] == 'difficulty' && isset($elements[2])) {
            $trailController->getByDifficulty($elements[2]);
        } elseif ($elements[0] == 'trails' && $elements[1] == 'radius' && isset($elements[2]) && isset($elements[3]) && isset($elements[4])) {
            $trailController->getWithinRadius($elements[2], $elements[3], $elements[4]);
        } elseif ($elements[0] == 'bookings' && empty($elements[1])) {
            $bookingController->index();
        } elseif ($elements[0] == 'pointsofinterest' && empty($elements[1])) {
            $pointOfInterestController->index();
        } elseif ($elements[0] == 'pointsofinterest' && isset($elements[1])) {
            $pointOfInterestController->read($elements[1]);
        }
        break;
    case 'POST':
        if ($elements[0] == 'users' && empty($elements[1])) {
            $userController->create();
        } elseif($elements[0]== 'login'){
            $userController->login();
        } elseif($elements[0] == 'campings' && empty($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    if ($decoded->role === 'admin') {
                        $campingController->create();
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif ($elements[0] == 'trails' && empty($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    if ($decoded->role === 'admin') {
                        $trailController->create();
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif ($elements[0] == 'bookings' && empty($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $bookingController->create();
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif ($elements[0] == 'pointsofinterest' && empty($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    if ($decoded->role === 'admin') {
                        $pointOfInterestController->create();
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        }
        break;
    case 'PUT':
        if ($elements[0] == 'users' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
            
            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $userId = $elements[1];
                    if ($decoded->role === 'admin' || $decoded->user_id == $userId) {
                    $userController->update($userId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You can only update your own profile or must be an admin"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif($elements[0] == 'campings' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $campingId = $elements[1];
                    if ($decoded->role === 'admin') {
                    $campingController->update($campingId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You can only update your own profile or must be an admin"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif($elements[0] == 'trails' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $trailId = $elements[1];
                    if ($decoded->role === 'admin') {
                        $trailController->update($trailId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin to update trails"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif($elements[0] == 'bookings' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $bookingId = $elements[1];
                    if ($decoded->role === 'admin') {
                    $bookingController->update($bookingId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin to update bookings"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif($elements[0] == 'pointsofinterest' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $pointOfInterestId = $elements[1];
                    if ($decoded->role === 'admin') {
                        $pointOfInterestController->update($pointOfInterestId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin to update points of interest"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        }
        break;
    case 'DELETE':
        if ($elements[0] == 'users' && isset($elements[1])) {
            $userController->delete($elements[1]);
        } elseif($elements[0] == 'campings' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $campingId = $elements[1];
                    if ($decoded->role === 'admin') {
                    $campingController->delete($campingId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You can only update your own profile or must be an admin"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif($elements[0] == 'trails' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $trailId = $elements[1];
                    if ($decoded->role === 'admin') {
                        $trailController->delete($trailId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin to delete trails"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif($elements[0] == 'bookings' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $bookingId = $elements[1];
                    if ($decoded->role === 'admin') {
                        $bookingController->delete($bookingId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin to delete bookings"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        } elseif($elements[0] == 'pointsofinterest' && isset($elements[1])) {
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
            if ($token) {
                $decoded = $jwtMiddleware->verifyToken($token);
                if ($decoded) {
                    $pointOfInterestId = $elements[1];
                    if ($decoded->role === 'admin') {
                        $pointOfInterestController->delete($pointOfInterestId);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Forbidden: You must be an admin to delete points of interest"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Unauthorized: Invalid token"]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unauthorized: No token provided"]);
            }
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}
