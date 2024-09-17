<?php
require_once '../models/User.php';

class UserController {
    private $user;

    public function __construct($db) {
        $this->user = new User($db);
    }

    public function create() {
        error_log("UserController: create method called");
        $data = json_decode(file_get_contents("php://input"));
        error_log("Received data: " . print_r($data, true));
        
        $this->user->email = $data->email;
        $this->user->password = $data->password;
        $this->user->name = $data->name;
        $this->user->firstname = $data->firstname;
        $this->user->phoneNumber = $data->phoneNumber;
        $this->user->admin = $data->admin ?? false;
    
        if($this->user->create()) {
            error_log("UserController: User created successfully");
            http_response_code(201);
            echo json_encode(array("message" => "CONTROLLER : User was created."));
        } else {
            error_log("UserController: Failed to create user");
            http_response_code(503);
            echo json_encode(array("message" => "CONTROLLER : Unable to create user."));
        }
    }
    
    

    public function read($id) {
        $user = $this->user->read($id);
        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "CONTROLLER : User not found."));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));
        
        $this->user->id = $id;
        $this->user->email = $data->email;
        $this->user->name = $data->name;
        $this->user->firstname = $data->firstname;
        $this->user->phoneNumber = $data->phoneNumber;
        $this->user->admin = $data->admin ?? false;
    
        if($this->user->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "CONTROLLER : User was updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "CONTROLLER : Unable to update user."));
        }
    }

    public function delete($id) {
        if($this->user->delete($id)) {
            http_response_code(200);
            echo json_encode(array("message" => "User was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete user."));
        }
    }

    public function index() {
        $users = $this->user->getAllUsers();
        echo json_encode($users);
    }
}
