<?php
namespace Skand\Backend\Models;
use PDO;

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
    public $image_path;
    public $createdAt;
    public $modifiedAt;

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
        $this->name = $this->name !== null ? htmlspecialchars(strip_tags($this->name)) : null;
        $this->longitudeStart = $this->longitudeStart !== null ? floatval($this->longitudeStart) : null;
        $this->longitudeEnd = $this->longitudeEnd !== null ? floatval($this->longitudeEnd) : null;
        $this->latitudeStart = $this->latitudeStart !== null ? floatval($this->latitudeStart) : null;
        $this->latitudeEnd = $this->latitudeEnd !== null ? floatval($this->latitudeEnd) : null;
        $this->distance = $this->distance !== null ? floatval($this->distance) : null;
        $this->heightDiff = $this->heightDiff !== null ? floatval($this->heightDiff) : null;
        if (is_array($this->pointOfInterest)) {
            $this->pointOfInterest = array_map('intval', $this->pointOfInterest);
        } else {
            $this->pointOfInterest = $this->pointOfInterest !== null ? htmlspecialchars(strip_tags($this->pointOfInterest)) : null;
        }
        
        if (is_array($this->camping)) {
            $this->camping = array_map('intval', $this->camping);
        } else {
            $this->camping = $this->camping !== null ? filter_var($this->camping, FILTER_VALIDATE_BOOLEAN) : null;
        }
        $this->difficulty = $this->difficulty !== null ? htmlspecialchars(strip_tags($this->difficulty)) : null;
        $this->estimatedTime = $this->estimatedTime !== null ? htmlspecialchars(strip_tags($this->estimatedTime)) : null;
        $this->trailType = $this->trailType !== null ? htmlspecialchars(strip_tags($this->trailType)) : null;
        $this->seasonAvailability = $this->seasonAvailability !== null ? htmlspecialchars(strip_tags($this->seasonAvailability)) : null;
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
        $pointOfInterestString = is_array($this->pointOfInterest) ? implode(',', $this->pointOfInterest) : $this->pointOfInterest;
        $stmt->bindParam(":pointOfInterest", $pointOfInterestString);
        $campingString = is_array($this->camping) ? implode(',', $this->camping) : $this->camping;
        $stmt->bindParam(":camping", $campingString);
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
        $this->pointOfInterest = explode(',', $row['pointOfInterest']);
        $this->camping = explode(',', $row['camping']);
        $this->difficulty = $row['difficulty'];
        $this->estimatedTime = $row['estimatedTime'];
        $this->trailType = $row['trailType'];
        $this->seasonAvailability = $row['seasonAvailability'];
    }
}
