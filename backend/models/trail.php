<?php
use Firebase\JWT\JWT;

class trail {
    private $conn;
    private $table_name = "trail";

    public $id;
    public $name;
    public $longitudeStart;
    public $longitudeEnd;
    public $latitudeStart;
    public $latitudeEnd;
    public $distance;
    public $heightDiff;
    public $pointOfInterest;
    public $camping;
    public $difficulty;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        error_log("Trail model: create method called");
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, longitudeStart=:longitudeStart, longitudeEnd=:longitudeEnd, latidudeStart=:latitudeStart, latitudeEnd=:latitudeEnd, distance=:distance, 
                      heightDiff=:heightDiff, pointOfInterest=:pointOfInterest, camping=:camping, difficulty=:difficulty, 
                      created_at=NOW(), modified_at=NOW()";

        $stmt = $this->conn->prepare($query);
        error_log("Prepared query: " . $query);

        $this->sanitize();

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":longitudeStart", $this->longitudeStart);
        $stmt->bindParam(":longitudeEnd", $this->longitudeEnd);
        $stmt->bindParam(":latitudeStart", $this->latitudeStart);
        $stmt->bindParam(":latitudeEnd", $this->latitudeEnd);
        $stmt->bindParam(":distance", $this->distance);
        $stmt->bindParam(":heightDiff", $this->heightDiff);
        $stmt->bindParam(":pointOfInterest", $this->pointOfInterest);
        $stmt->bindParam(":camping", $this->camping);
        $stmt->bindParam(":difficulty", $this->difficulty);
    

    error_log("Bound parameters: " . print_r([$this->name, $this->longitudeStart, $this->latitudeStart, $this->longitudeEnd, $this->latitudeEnd, $this->distance, $this->heightDiff, $this->pointOfInterest, $this->camping, $this->difficulty], true));

    if ($stmt->execute()) {
        error_log("Trail model: Trail created");
        return true;
        }
    error_log("Trail model: Trail creation failed");
    return false;
    }

    public function read($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->longitudeStart = $row['longitudeStart'];
            $this->longitudeEnd = $row['longitudeEnd'];
            $this->latitudeStart = $row['latitudeStart'];
            $this->latitudeEnd = $row['latitudeEnd'];
            $this->distance = $row['distance'];
            $this->heightDiff = $row['heightDiff'];
            $this->pointOfInterest = $row['pointOfInterest'];
            $this->camping = $row['camping'];
            $this->difficulty = $row['difficulty'];
            return true;
        }

        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, longitudeStart=:longitudeStart, longitudeEnd=:longitudeEnd, latidudeStart=:latitudeStart, latitudeEnd=:latitudeEnd, distance=:distance, 
                      heightDiff=:heightDiff, pointOfInterest=:pointOfInterest, camping=:camping, difficulty=:difficulty, 
                      modified_at=NOW() 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":longitudeStart", $this->longitudeStart);
        $stmt->bindParam(":longitudeEnd", $this->longitudeEnd);
        $stmt->bindParam(":latitudeStart", $this->latitudeStart);
        $stmt->bindParam(":latitudeEnd", $this->latitudeEnd);
        $stmt->bindParam(":distance", $this->distance);
        $stmt->bindParam(":heightDiff", $this->heightDiff);
        $stmt->bindParam(":pointOfInterest", $this->pointOfInterest);
        $stmt->bindParam(":camping", $this->camping);
        $stmt->bindParam(":difficulty", $this->difficulty);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            error_log("Trail model: Trail updated");
            return true;
        }
        error_log("Trail model: Trail update failed");
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()) {
            error_log("Trail model: Trail deleted");
            return true;
        }
        error_log("Trail model: Trail delete failed");
        return false;
    }

    private function sanitize() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->longitudeStart = htmlspecialchars(strip_tags($this->longitudeStart));
        $this->longitudeEnd = htmlspecialchars(strip_tags($this->longitudeEnd));
        $this->latitudeStart = htmlspecialchars(strip_tags($this->latitudeStart));
        $this->latitudeEnd = htmlspecialchars(strip_tags($this->latitudeEnd));
        $this->distance = htmlspecialchars(strip_tags($this->distance));
        $this->heightDiff = htmlspecialchars(strip_tags($this->heightDiff));
        $this->pointOfInterest = htmlspecialchars(strip_tags($this->pointOfInterest));
        $this->camping = htmlspecialchars(strip_tags($this->camping));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
    }
}