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
        $this->trail->estimatedTime = $data->estimatedTime;
        $this->trail->trailType = $data->trailType;
        $this->trail->seasonAvailability = $data->seasonAvailability;

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
        $data = json_decode(file_get_contents("php://input"));
        
        $this->trail->id = $id;
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
        $this->trail->estimatedTime = $data->estimatedTime;
        $this->trail->trailType = $data->trailType;
        $this->trail->seasonAvailability = $data->seasonAvailability;

        if($this->trail->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Trail was updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update trail."));
        }
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
