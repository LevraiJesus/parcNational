<?php
class Trail {
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
    public $estimatedTime;
    public $trailType;
    public $seasonAvailability;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, longitudeStart=:longitudeStart, longitudeEnd=:longitudeEnd, latitudeStart=:latitudeStart, latitudeEnd=:latitudeEnd, 
                      distance=:distance, heightDiff=:heightDiff, pointOfInterest=:pointOfInterest, camping=:camping, difficulty=:difficulty, 
                      estimatedTime=:estimatedTime, trailType=:trailType, seasonAvailability=:seasonAvailability, 
                      created_at=NOW(), modified_at=NOW()";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        if (!$this->validateData()) {
            error_log("Trail model: Data validation failed");
            return false;
        }

        $this->bindParams($stmt);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                error_log("Trail model: Trail created with ID: " . $this->id);
                return true;
            }
        } catch (PDOException $e) {
            error_log("Trail model: Trail creation failed. Error: " . $e->getMessage());
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
            $this->populateTrailData($row);
            return true;
        }
        
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, longitudeStart=:longitudeStart, longitudeEnd=:longitudeEnd, latitudeStart=:latitudeStart, latitudeEnd=:latitudeEnd, 
                      distance=:distance, heightDiff=:heightDiff, pointOfInterest=:pointOfInterest, camping=:camping, difficulty=:difficulty, 
                      estimatedTime=:estimatedTime, trailType=:trailType, seasonAvailability=:seasonAvailability, 
                      modified_at=NOW() 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        if (!$this->validateData()) {
            error_log("Trail model: Data validation failed");
            return false;
        }

        $this->bindParams($stmt);
        $stmt->bindParam(":id", $this->id);

        try {
            if ($stmt->execute()) {
                error_log("Trail model: Trail updated");
                return true;
            }
        } catch (PDOException $e) {
            error_log("Trail model: Trail update failed. Error: " . $e->getMessage());
        }
        
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        try {
            if ($stmt->execute()) {
                error_log("Trail model: Trail deleted");
                return true;
            }
        } catch (PDOException $e) {
            error_log("Trail model: Trail delete failed. Error: " . $e->getMessage());
        }
        
        return false;
    }

    public function getAllTrails($limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTrailsByDifficulty($difficulty) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE difficulty = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $difficulty);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTrailsWithinRadius($lat, $lon, $radius) {
        $query = "SELECT *, 
                  (6371 * acos(cos(radians(?)) * cos(radians(latitudeStart)) * cos(radians(longitudeStart) - radians(?)) + sin(radians(?)) * sin(radians(latitudeStart)))) AS distance 
                  FROM " . $this->table_name . " 
                  HAVING distance < ? 
                  ORDER BY distance";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $lat);
        $stmt->bindParam(2, $lon);
        $stmt->bindParam(3, $lat);
        $stmt->bindParam(4, $radius);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function sanitize() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->longitudeStart = floatval($this->longitudeStart);
        $this->longitudeEnd = floatval($this->longitudeEnd);
        $this->latitudeStart = floatval($this->latitudeStart);
        $this->latitudeEnd = floatval($this->latitudeEnd);
        $this->distance = floatval($this->distance);
        $this->heightDiff = floatval($this->heightDiff);
        $this->pointOfInterest = htmlspecialchars(strip_tags($this->pointOfInterest));
        $this->camping = filter_var($this->camping, FILTER_VALIDATE_BOOLEAN);
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        $this->estimatedTime = htmlspecialchars(strip_tags($this->estimatedTime));
        $this->trailType = htmlspecialchars(strip_tags($this->trailType));
        $this->seasonAvailability = htmlspecialchars(strip_tags($this->seasonAvailability));
    }

    private function validateData() {
        if (!$this->name || strlen($this->name) > 255) return false;
        if ($this->longitudeStart < -180 || $this->longitudeStart > 180) return false;
        if ($this->longitudeEnd < -180 || $this->longitudeEnd > 180) return false;
        if ($this->latitudeStart < -90 || $this->latitudeStart > 90) return false;
        if ($this->latitudeEnd < -90 || $this->latitudeEnd > 90) return false;
        if ($this->distance < 0) return false;
        if ($this->heightDiff < 0) return false;
        return true;
    }

    private function bindParams($stmt) {
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":longitudeStart", $this->longitudeStart);
        $stmt->bindParam(":longitudeEnd", $this->longitudeEnd);
        $stmt->bindParam(":latitudeStart", $this->latitudeStart);
        $stmt->bindParam(":latitudeEnd", $this->latitudeEnd);
        $stmt->bindParam(":distance", $this->distance);
        $stmt->bindParam(":heightDiff", $this->heightDiff);
        $stmt->bindParam(":pointOfInterest", $this->pointOfInterest);
        $stmt->bindParam(":camping", $this->camping, PDO::PARAM_BOOL);
        $stmt->bindParam(":difficulty", $this->difficulty);
        $stmt->bindParam(":estimatedTime", $this->estimatedTime);
        $stmt->bindParam(":trailType", $this->trailType);
        $stmt->bindParam(":seasonAvailability", $this->seasonAvailability);
    }

    private function populateTrailData($row) {
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
        $this->estimatedTime = $row['estimatedTime'];
        $this->trailType = $row['trailType'];
        $this->seasonAvailability = $row['seasonAvailability'];
    }
}
