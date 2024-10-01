<?php
namespace Skand\Backend\Models;
use PDO;

class Booking {
    private $conn;
    private $table_name = "bookings";

    public $id;
    public $camping_id;
    public $user_id;
    public $start_date;
    public $end_date;
    public $status;
    public $created_at;
    public $modified_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET camping_id=:camping_id, user_id=:user_id, start_date=:start_date, 
                      end_date=:end_date, status=:status, created_at=NOW(), modified_at=NOW()";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bindParam(":camping_id", $this->camping_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
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

        if($row) {
            $this->id = $row['id'];
            $this->camping_id = $row['camping_id'];
            $this->user_id = $row['user_id'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->modified_at = $row['modified_at'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET camping_id=:camping_id, user_id=:user_id, start_date=:start_date,
                      end_date=:end_date, status=:status, modified_at=NOW()
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bindParam(":camping_id", $this->camping_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAllBookings() {
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkAvailability($camping_id, $start_date, $end_date) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . "
                  WHERE camping_id = :camping_id 
                  AND ((start_date <= :start_date AND end_date >= :start_date)
                  OR (start_date <= :end_date AND end_date >= :end_date)
                  OR (start_date >= :start_date AND end_date <= :end_date))
                  AND status != 'cancelled'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":camping_id", $camping_id);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] == 0;
    }

    private function sanitize() {
        $this->camping_id = htmlspecialchars(strip_tags($this->camping_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->status = htmlspecialchars(strip_tags($this->status));
    }
}
