<?php
require_once '../models/Camping.php';
require_once '../helpers/FileUploaderHelper.php';

class CampingController {
    private $camping;

    public function __construct($db) {
        $this->camping = new Camping($db);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));

        if (!$data) {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid or empty request body"));
            return;
        }

        $this->camping->name = $data->name ?? null;
        $this->camping->longitude = $data->longitude ?? null;
        $this->camping->latitude = $data->latitude ?? null;
        $this->camping->description = $data->description ?? null;
        $this->camping->price = $data->price ?? null;
        $this->camping->capacity = $data->capacity ?? null;

        if(isset($_FILES['image'])) {
            $uploadedFilePath = FileUploadHelper::uploadFile($_FILES['image'], 'uploads/campings/');
            if ($uploadedFilePath) {
                $this->camping->image_path = $uploadedFilePath;
            }
        }

        if($this->camping->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Camping was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create camping."));
        }
    }


    public function read($id) {
        $camping = $this->camping->read($id);
        if ($camping) {
            $response = array(
                "id" => $camping->id,
                "name" => $camping->name,
                "longitude" => $camping->longitude,
                "latitude" => $camping->latitude,
                "description" => $camping->description,
                "price" => $camping->price,
                "capacity" => $camping->capacity,
                "image_path" => $camping->image_path
            );
            echo json_encode($response);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Camping not found."));
        }
    }
    

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));
        
        $this->camping->name = $data->name;
        $this->camping->longitude = $data->longitude;
        $this->camping->latitude = $data->latitude;
        $this->camping->description = $data->description;
        $this->camping->price = $data->price;
        $this->camping->capacity = $data->capacity;
        if(isset($_FILES['image'])) {
            $uploadedFilePath = FileUploadHelper::uploadFile($_FILES['image'], 'uploads/campings/');
            if ($uploadedFilePath) {
                $this->camping->image_path = $uploadedFilePath;
            }
        }
        $this->camping->id = $id;

        if($this->camping->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "CONTROLLER : Camping was updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "CONTROLLER : Unable to update camping."));
        }

    }

    public function delete($id) {
        $this->camping->id = $id;
        if($this->camping->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "CONTROLLER : Camping was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "CONTROLLER : Unable to delete camping."));
        }
    }

    public function index(){
        $campings = $this->camping->getAllCampings();
        if ($campings) {
            echo json_encode($campings);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "CONTROLLER : Campings not found."));
        }
    }
}