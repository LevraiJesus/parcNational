<?php
use Firebase\JWT\JWT;

class Camping {
    private $conn;
    private $table_name = "camping";

    public $id;
    public $name;
    public $capacity;
    public $images;
    public $location;
    public $equipment;
    public $closeTrail;
    public $note;
    public $price;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        error_log("Camping model: create method called");
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, capacity=:capacity, images=:images, location=:location, 
                      equipment=:equipment, closetrail=:closetrail, note=:note, price=:price, 
                      created_at=NOW(), modified_at=NOW()";
    
        $stmt = $this->conn->prepare($query);
        error_log("Prepared query: " . $query);
    
        $this->sanitize();

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":capacity", $this->capacity);
        $stmt->bindParam(":images", $this->images);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":equipment", $this->equipment);
        $stmt->bindParam(":closetrail", $this->closeTrail);
        $stmt->bindParam(":note", $this->note);
        $stmt->bindParam(":price", $this->price);

        error_log("Bound parameters: " . print_r([$this->name, $this->capacity, $this->images, $this->location, $this->equipment, $this->closeTrail, $this->note, $this->price], true));
    
        if ($stmt->execute()) {
            error_log("Camping model: Camping created");
            return true;
        }

        error_log("Camping model: Camping creation failed");
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
            $this->capacity = $row['capacity'];
            $this->images = $row['images'];
            $this->location = $row['location'];
            $this->equipment = $row['equipment'];
            $this->closeTrail = $row['closetrail'];
            $this->note = $row['note'];
            $this->price = $row['price'];
            return true;
        }

        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, capacity=:capacity, images=:images, location=:location, 
                      equipment=:equipment, closetrail=:closetrail, note=:note, price=:price, 
                      modified_at=NOW() 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        // Bind parameters
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":capacity", $this->capacity);
        $stmt->bindParam(":images", $this->images);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":equipment", $this->equipment);
        $stmt->bindParam(":closetrail", $this->closeTrail);
        $stmt->bindParam(":note", $this->note);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            error_log("Camping model: Camping updated");
            return true;
        }

        error_log("Camping model: Update failed");
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            error_log("Camping model: Camping deleted");
            return true;
        }

        error_log("Camping model: Deletion failed");
        return false;
    }

    private function sanitize() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->capacity = htmlspecialchars(strip_tags($this->capacity));
        $this->images = htmlspecialchars(strip_tags($this->images));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->equipment = htmlspecialchars(strip_tags($this->equipment));
        $this->closeTrail = htmlspecialchars(strip_tags($this->closeTrail));
        $this->note = htmlspecialchars(strip_tags($this->note));
        $this->price = htmlspecialchars(strip_tags($this->price));
    }
}
