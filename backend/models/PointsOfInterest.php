<?php
use Firebase\JWT\JWT;

class PointOfInterest {
    private $conn;
    private $table_name = "pointOfInterest";

    public $id;
    public $name;
    public $type;
    public $location;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        error_log("PointOfInterest model: create method called");
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, type=:type, location=:location, description=:description, 
                      created_at=NOW(), modified_at=NOW()";

        $stmt = $this->conn->prepare($query);
        error_log("Prepared query: " . $query);

        $this->sanitize();

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":description", $this->description);

        error_log("Bound parameters: " . print_r([$this->name, $this->type, $this->location, $this->description], true));

        if ($stmt->execute()) {
            error_log("PointOfInterest model: PointOfInterest created");
            return true;
        }
        error_log("PointOfInterest model: PointOfInterest creation failed");
        return false;
    }

    public function read($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->type = $row['type'];
            $this->location = $row['location'];
            $this->description = $row['description'];
            return true;
        }

        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, type=:type, location=:location, description=:description, modified_at=NOW() 
                  WHERE id=:id";
                
        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":description", $this->description);

        if ($stmt->execute()) {
            error_log("PointOfInterest model: PointOfInterest updated");
            return true;
        }

        error_log("PointOfInterest model: PointOfInterest update failed");
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            error_log("PointOfInterest model: PointOfInterest deleted");
            return true;
        }

        error_log("PointOfInterest model: PointOfInterest deletion failed");
        return false;
    }

    private function sanitize() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->description = htmlspecialchars(strip_tags($this->description));
    }
}