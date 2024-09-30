<?php
require_once '../models/trail.php';

class TrailController {
    private $trail;

    public function __construct($db) {
        $this->trail = new Trail($db);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));
        
        $this->trail->name = $data->name;
        $this->trail->longitudeStart = $data->longitudeStart;
        $this->trail->longitudeEnd = $data->longitudeEnd;
        $this->trail->latitudeStart = $data->latitudeStart;
        $this->trail->latitudeEnd = $data->latitudeEnd;
        $this->trail->distance = $data->distance;
        $this->trail->heightDiff = $data->heightDiff;
        $this->trail->pointOfInterest = $data->pointOfInterest;
        $this->trail->camping = $data->camping;
        $this->trail->difficulty = $data->difficulty;
        if(isset($_FILES['image'])) {
            $uploadedFilePath = FileUploadHelper::uploadFile($_FILES['image'], 'uploads/trails/');
            if ($uploadedFilePath) {
                $this->trail->image_path = $uploadedFilePath;
            }
        }

        if($this->trail->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Trail was created.", "id" => $this->trail->id));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create trail."));
        }
    }

    public function read($id) {
        if($this->trail->read($id)) {
            echo json_encode($this->trail);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Trail not found."));
        }
    }

    public function update($id) {
        ob_start();
    
        $data = json_decode(file_get_contents("php://input"));
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            $response = json_encode(array("message" => "Invalid JSON data"));
            $statusCode = 400;
        } else {
            $this->trail->id = $id;
            $this->trail->name = $data->name ?? $this->trail->name;
            $this->trail->longitudeStart = $data->longitudeStart ?? $this->trail->longitudeStart;
            $this->trail->longitudeEnd = $data->longitudeEnd ?? $this->trail->longitudeEnd;
            $this->trail->latitudeStart = $data->latitudeStart ?? $this->trail->latitudeStart;
            $this->trail->latitudeEnd = $data->latitudeEnd ?? $this->trail->latitudeEnd;
            $this->trail->distance = $data->distance ?? $this->trail->distance;
            $this->trail->heightDiff = $data->heightDiff ?? $this->trail->heightDiff;
            $this->trail->pointOfInterest = $data->pointOfInterest ?? $this->trail->pointOfInterest;
            $this->trail->camping = $data->camping ?? $this->trail->camping;
            $this->trail->difficulty = $data->difficulty ?? $this->trail->difficulty;
            if(isset($_FILES['image'])) {
                $uploadedFilePath = FileUploadHelper::uploadFile($_FILES['image'], 'uploads/trails/');
                if ($uploadedFilePath) {
                    $this->trail->image_path = $uploadedFilePath;
                }
            }
    
            if($this->trail->update()) {
                $response = json_encode(array("message" => "Trail was updated."));
                $statusCode = 200;
            } else {
                $response = json_encode(array("message" => "Unable to update trail."));
                $statusCode = 503;
            }
        }
    
        $output = ob_get_clean();
        http_response_code($statusCode);
        echo $response;
    }
    

    public function delete($id) {
        if($this->trail->delete($id)) {
            http_response_code(200);
            echo json_encode(array("message" => "Trail was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete trail."));
        }
    }

    public function index($limit = 10, $offset = 0) {
        $trails = $this->trail->getAllTrails($limit, $offset);
        if($trails) {
            echo json_encode($trails);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No trails found."));
        }
    }

    public function getByDifficulty($difficulty) {
        $trails = $this->trail->getTrailsByDifficulty($difficulty);
        if($trails) {
            echo json_encode($trails);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No trails found with the specified difficulty."));
        }
    }

    public function getWithinRadius($lat, $lon, $radius) {
        $trails = $this->trail->getTrailsWithinRadius($lat, $lon, $radius);
        if($trails) {
            echo json_encode($trails);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No trails found within the specified radius."));
        }
    }
}
