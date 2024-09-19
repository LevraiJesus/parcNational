<?php
class Camping {
    private $conn;
    private $table_name = "campings";

    public $id;
    public $name;
    public $longitude;
    public $latitude;
    public $description;
    public $image;
    public $price;
    public $capacity;
    public $closeTrails;
    public $manager;
    public $createdAt;
    public $modifiedAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, longitude=:longitude, latitude=:latitude, description=:description, price=:price, capacity=:capacity, created_at=NOW(), modified_at=NOW()";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();


        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":longitude", $this->longitude);
        $stmt->bindParam(":latitude", $this->latitude);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":capacity", $this->capacity);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function read($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->longitude = $row['longitude'];
            $this->latitude = $row['latitude'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            $this->price = $row['price'];
            $this->capacity = $row['capacity'];
            $this->closeTrails = $row['closeTrails'];
            $this->manager = $row['manager'];
            $this->createdAt = $row['created_at'];
            $this->modifiedAt = $row['modified_at'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                    SET name=:name, longitude=:longitude, latitude=:latitude, description=:description, 
                     price=:price, capacity=:capacity, 
                        modified_at=NOW() 
                    WHERE id=:id";
        
        
        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":longitude", $this->longitude);
        $stmt->bindParam(":latitude", $this->latitude);
        $stmt->bindParam(":description", $this->description);
        $stmt->BindParam(":price", $this->price);
        $stmt->bindParam(":capacity", $this->capacity);
        $stmt->bindParam(":id", $this->id);


        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAllCampings(){
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function sanitize() {
        $this->name = $this->name ? htmlspecialchars(strip_tags($this->name)) : null;
        $this->longitude = $this->longitude ? htmlspecialchars(strip_tags($this->longitude)) : null;
        $this->latitude = $this->latitude ? htmlspecialchars(strip_tags($this->latitude)) : null;
        $this->description = $this->description ? htmlspecialchars(strip_tags($this->description)) : null;
        $this->image = $this->image ? htmlspecialchars(strip_tags($this->image)) : null;
        $this->price = $this->price ? htmlspecialchars(strip_tags($this->price)) : null;
        $this->capacity = $this->capacity ? htmlspecialchars(strip_tags($this->capacity)) : null;
        $this->closeTrails = $this->closeTrails ? htmlspecialchars(strip_tags($this->closeTrails)) : null;
    }
}