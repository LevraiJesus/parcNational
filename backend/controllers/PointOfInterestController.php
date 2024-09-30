<?php
require_once '../models/PointsOfInterest.php';
require_once '../helpers/FileUploaderHelper.php';

class PointOfInterestController {
    private $pointOfInterest;

    public function __construct($db) {
        $this->pointOfInterest = new PointOfInterest($db);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!$data) {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid or empty request body"));
            return;
        }
    
        $this->pointOfInterest->name = $data->name ?? null;
        $this->pointOfInterest->type = $data->type ?? null;
        $this->pointOfInterest->latitude = $data->latitude ?? null;
        $this->pointOfInterest->longitude = $data->longitude ?? null;
        $this->pointOfInterest->description = $data->description ?? null;
        if(isset($_FILES['image'])) {
            $uploadedFilePath = FileUploadHelper::uploadFile($_FILES['image'], 'uploads/pointofinterest/');
            if ($uploadedFilePath) {
                $this->pointOfInterest->image_path = $uploadedFilePath;
            }
        }
    
        if($this->pointOfInterest->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "CONTROLLER: Point of Interest was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "CONTROLLER: Unable to create Point of Interest."));
        }
    }
    

    public function read($id) {
        $pointOfInterest = $this->pointOfInterest->read($id);
        if ($pointOfInterest) {
            echo json_encode($pointOfInterest);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "CONTROLLER: Point of Interest not found."));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));
    
        if (!$data) {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid or empty request body"));
            return;
        }
    
        $this->pointOfInterest->id = $id;
        $this->pointOfInterest->name = $data->name ?? null;
        $this->pointOfInterest->type = $data->type ?? null;
        $this->pointOfInterest->latitude = $data->latitude ?? null;
        $this->pointOfInterest->longitude = $data->longitude ?? null;
        $this->pointOfInterest->description = $data->description ?? null;
        if(isset($_FILES['image'])) {
            $uploadedFilePath = FileUploadHelper::uploadFile($_FILES['image'], 'uploads/pointofinterest/');
            if ($uploadedFilePath) {
                $this->pointOfInterest->image_path = $uploadedFilePath;
            }
        }
    
        if($this->pointOfInterest->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "CONTROLLER: Point of Interest was updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "CONTROLLER: Unable to update Point of Interest."));
        }
    }    

    public function delete($id) {
        if($this->pointOfInterest->delete($id)) {
            http_response_code(200);
            echo json_encode(array("message" => "CONTROLLER: Point of Interest was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "CONTROLLER: Unable to delete Point of Interest."));
        }
    }

    public function index() {
        $pointsOfInterest = $this->pointOfInterest->getAllPointsOfInterest();
        if ($pointsOfInterest) {
            echo json_encode($pointsOfInterest);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "CONTROLLER: Points of Interest not found."));
        }
    }
}
